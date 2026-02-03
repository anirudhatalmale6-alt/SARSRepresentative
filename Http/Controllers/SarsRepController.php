<?php

namespace Modules\SARSRepresentative\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Modules\SARSRepresentative\Models\SarsRepAuditLog;
use Modules\SARSRepresentative\Models\SarsRepDocument;
use Modules\SARSRepresentative\Models\SarsRepRequest;
use Modules\SARSRepresentative\Models\SarsRepresentative;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SarsRepController extends Controller
{
    /**
     * Apply authentication middleware to all controller actions.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of all SARS representative requests.
     *
     * Provides summary statistics alongside the paginated request list.
     */
    public function index()
    {
        $requests = SarsRepRequest::with('sarsRepresentative')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        $stats = [
            'total'     => SarsRepRequest::count(),
            'draft'     => SarsRepRequest::where('status', SarsRepRequest::STATUS_DRAFT)->count(),
            'awaiting'  => SarsRepRequest::where('status', SarsRepRequest::STATUS_AWAITING_DOCUMENTS)->count(),
            'submitted' => SarsRepRequest::whereIn('status', [
                SarsRepRequest::STATUS_SUBMITTED_BRANCH,
                SarsRepRequest::STATUS_SUBMITTED_EFILING,
            ])->count(),
            'approved'  => SarsRepRequest::where('status', SarsRepRequest::STATUS_APPROVED)->count(),
            'rejected'  => SarsRepRequest::where('status', SarsRepRequest::STATUS_REJECTED)->count(),
        ];

        return view('sarsrepresentative::sarsrep.index', compact('requests', 'stats'));
    }

    /**
     * Show the form for creating a new SARS representative request.
     */
    public function create()
    {
        $entity_types = [
            SarsRepRequest::ENTITY_TYPE_COMPANY              => 'Company',
            SarsRepRequest::ENTITY_TYPE_TRUST                => 'Trust',
            SarsRepRequest::ENTITY_TYPE_NPC                  => 'Non-Profit Company (NPC)',
            SarsRepRequest::ENTITY_TYPE_NPO                  => 'Non-Profit Organisation (NPO)',
            SarsRepRequest::ENTITY_TYPE_SOLE_DIRECTOR_COMPANY => 'Sole Director Company',
            SarsRepRequest::ENTITY_TYPE_SOLE_TRUSTEE_TRUST   => 'Sole Trustee Trust',
        ];

        $capacity_types = [
            SarsRepresentative::CAPACITY_DIRECTOR                 => 'Director',
            SarsRepresentative::CAPACITY_SOLE_DIRECTOR             => 'Sole Director',
            SarsRepresentative::CAPACITY_TRUSTEE                   => 'Trustee',
            SarsRepresentative::CAPACITY_SOLE_TRUSTEE              => 'Sole Trustee',
            SarsRepresentative::CAPACITY_ACCOUNTING_OFFICER        => 'Accounting Officer',
            SarsRepresentative::CAPACITY_CHAIRPERSON               => 'Chairperson',
            SarsRepresentative::CAPACITY_TREASURER                 => 'Treasurer',
            SarsRepresentative::CAPACITY_ACCOUNTANT                => 'Accountant',
            SarsRepresentative::CAPACITY_AUTHORISED_REPRESENTATIVE => 'Authorised Representative',
        ];

        return view('sarsrepresentative::sarsrep.create', compact('entity_types', 'capacity_types'));
    }

    /**
     * Validate and store a newly created SARS representative request.
     *
     * Creates or finds the representative by ID number, then creates
     * the request record with associated tax types and initial status.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'entity_name'       => 'required|string|max:255',
            'entity_type'       => 'required|string|in:' . implode(',', SarsRepRequest::ENTITY_TYPES),
            'entity_reg_number' => 'nullable|string|max:100',
            'income_tax_ref'    => 'nullable|string|max:100',
            'paye_ref'          => 'nullable|string|max:100',
            'vat_ref'           => 'nullable|string|max:100',
            'uif_sdl_ref'       => 'nullable|string|max:100',
            'entity_address'       => 'nullable|string|max:500',
            'number_of_directors'  => 'required|integer|min:1|max:20',
            'full_name'            => 'required|string|max:255',
            'id_number'            => 'nullable|string|max:20|required_without:passport_number',
            'passport_number'      => 'nullable|string|max:50|required_without:id_number',
            'email'                => 'required|email|max:255',
            'mobile'               => 'required|string|max:20',
            'capacity'             => 'required|string|in:' . implode(',', SarsRepresentative::CAPACITIES),
            'submission_method'    => 'required|string|in:branch,efiling,both',
            'tax_types'            => 'nullable|array',
            'tax_types.*'          => 'string|in:income_tax,paye,vat,uif,sdl,customs',
            'notes'                => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Create or find the representative by ID number or passport number.
            $representativeData = [
                'full_name'       => $validated['full_name'],
                'id_number'       => $validated['id_number'] ?? null,
                'passport_number' => $validated['passport_number'] ?? null,
                'email'           => $validated['email'],
                'mobile'          => $validated['mobile'],
                'capacity'        => $validated['capacity'],
            ];

            $lookupField = ! empty($validated['id_number']) ? 'id_number' : 'passport_number';
            $lookupValue = $validated[$lookupField];

            $representative = SarsRepresentative::where($lookupField, $lookupValue)->first();

            if ($representative) {
                $representative->update($representativeData);
            } else {
                $representative = SarsRepresentative::create($representativeData);
            }

            // Create the SARS representative request.
            $sarsRepRequest = SarsRepRequest::create([
                'entity_name'            => $validated['entity_name'],
                'entity_reg_number'      => $validated['entity_reg_number'] ?? null,
                'entity_type'            => $validated['entity_type'],
                'income_tax_ref'         => $validated['income_tax_ref'] ?? null,
                'paye_ref'               => $validated['paye_ref'] ?? null,
                'vat_ref'                => $validated['vat_ref'] ?? null,
                'uif_sdl_ref'            => $validated['uif_sdl_ref'] ?? null,
                'entity_address'         => $validated['entity_address'] ?? null,
                'number_of_directors'    => $validated['number_of_directors'],
                'sars_representative_id' => $representative->id,
                'tax_types'              => $validated['tax_types'] ?? [],
                'submission_method'      => $validated['submission_method'],
                'status'                 => SarsRepRequest::STATUS_AWAITING_DOCUMENTS,
                'notes'                  => $validated['notes'] ?? null,
                'created_by'             => Auth::id(),
            ]);

            // Log the creation in the audit trail.
            SarsRepAuditLog::log(
                $sarsRepRequest->id,
                'created_request',
                "Request created for entity '{$sarsRepRequest->entity_name}' with representative '{$representative->full_name}'."
            );

            DB::commit();

            return redirect()
                ->route('sarsrep.show', $sarsRepRequest->id)
                ->with('success', 'SARS Representative request created successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('SarsRepController@store failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create the request. Please try again.');
        }
    }

    /**
     * Display the specified SARS representative request.
     *
     * Shows the request details alongside document upload status,
     * completion percentage, and the full audit trail.
     */
    public function show($id)
    {
        $sarsRepRequest = SarsRepRequest::with([
            'sarsRepresentative',
            'sarsRepDocuments',
            'sarsRepAuditLogs' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
        ])->findOrFail($id);

        // Determine the required document types and their upload status.
        $requiredDocumentTypes = $sarsRepRequest->getRequiredDocumentTypes();
        $uploadedTypes = $sarsRepRequest->sarsRepDocuments
            ->pluck('document_type')
            ->unique()
            ->toArray();

        $documentChecklist = [];
        foreach ($requiredDocumentTypes as $docType => $isRequired) {
            $uploadedDoc = $sarsRepRequest->sarsRepDocuments
                ->where('document_type', $docType)
                ->first();

            $documentChecklist[$docType] = [
                'label'      => SarsRepDocument::getDocumentTypeLabel($docType),
                'required'   => $isRequired,
                'uploaded'   => in_array($docType, $uploadedTypes),
                'document'   => $uploadedDoc,
                'expired'    => $uploadedDoc ? $uploadedDoc->isExpired() : false,
            ];
        }

        $completionPercentage = $sarsRepRequest->getCompletionPercentage();

        return view('sarsrepresentative::sarsrep.show', compact(
            'sarsRepRequest',
            'documentChecklist',
            'completionPercentage'
        ));
    }

    /**
     * Show the form for editing the specified SARS representative request.
     *
     * Reuses the create form with the existing request data pre-filled.
     */
    public function edit($id)
    {
        $sarsRepRequest = SarsRepRequest::with('sarsRepresentative')->findOrFail($id);

        $entity_types = [
            SarsRepRequest::ENTITY_TYPE_COMPANY              => 'Company',
            SarsRepRequest::ENTITY_TYPE_TRUST                => 'Trust',
            SarsRepRequest::ENTITY_TYPE_NPC                  => 'Non-Profit Company (NPC)',
            SarsRepRequest::ENTITY_TYPE_NPO                  => 'Non-Profit Organisation (NPO)',
            SarsRepRequest::ENTITY_TYPE_SOLE_DIRECTOR_COMPANY => 'Sole Director Company',
            SarsRepRequest::ENTITY_TYPE_SOLE_TRUSTEE_TRUST   => 'Sole Trustee Trust',
        ];

        $capacity_types = [
            SarsRepresentative::CAPACITY_DIRECTOR                 => 'Director',
            SarsRepresentative::CAPACITY_SOLE_DIRECTOR             => 'Sole Director',
            SarsRepresentative::CAPACITY_TRUSTEE                   => 'Trustee',
            SarsRepresentative::CAPACITY_SOLE_TRUSTEE              => 'Sole Trustee',
            SarsRepresentative::CAPACITY_ACCOUNTING_OFFICER        => 'Accounting Officer',
            SarsRepresentative::CAPACITY_CHAIRPERSON               => 'Chairperson',
            SarsRepresentative::CAPACITY_TREASURER                 => 'Treasurer',
            SarsRepresentative::CAPACITY_ACCOUNTANT                => 'Accountant',
            SarsRepresentative::CAPACITY_AUTHORISED_REPRESENTATIVE => 'Authorised Representative',
        ];

        return view('sarsrepresentative::sarsrep.create', compact(
            'sarsRepRequest',
            'entity_types',
            'capacity_types'
        ));
    }

    /**
     * Update the specified SARS representative request and its representative details.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $sarsRepRequest = SarsRepRequest::with('sarsRepresentative')->findOrFail($id);

        $validated = $request->validate([
            'entity_name'       => 'required|string|max:255',
            'entity_type'       => 'required|string|in:' . implode(',', SarsRepRequest::ENTITY_TYPES),
            'entity_reg_number' => 'nullable|string|max:100',
            'income_tax_ref'    => 'nullable|string|max:100',
            'paye_ref'          => 'nullable|string|max:100',
            'vat_ref'           => 'nullable|string|max:100',
            'uif_sdl_ref'       => 'nullable|string|max:100',
            'entity_address'       => 'nullable|string|max:500',
            'number_of_directors'  => 'required|integer|min:1|max:20',
            'full_name'            => 'required|string|max:255',
            'id_number'            => 'nullable|string|max:20|required_without:passport_number',
            'passport_number'      => 'nullable|string|max:50|required_without:id_number',
            'email'                => 'required|email|max:255',
            'mobile'               => 'required|string|max:20',
            'capacity'             => 'required|string|in:' . implode(',', SarsRepresentative::CAPACITIES),
            'submission_method'    => 'required|string|in:branch,efiling,both',
            'tax_types'            => 'nullable|array',
            'tax_types.*'          => 'string|in:income_tax,paye,vat,uif,sdl,customs',
            'notes'                => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Update the representative details.
            $sarsRepRequest->sarsRepresentative->update([
                'full_name'       => $validated['full_name'],
                'id_number'       => $validated['id_number'] ?? null,
                'passport_number' => $validated['passport_number'] ?? null,
                'email'           => $validated['email'],
                'mobile'          => $validated['mobile'],
                'capacity'        => $validated['capacity'],
            ]);

            // Update the request details.
            $sarsRepRequest->update([
                'entity_name'       => $validated['entity_name'],
                'entity_reg_number' => $validated['entity_reg_number'] ?? null,
                'entity_type'       => $validated['entity_type'],
                'income_tax_ref'    => $validated['income_tax_ref'] ?? null,
                'paye_ref'          => $validated['paye_ref'] ?? null,
                'vat_ref'           => $validated['vat_ref'] ?? null,
                'uif_sdl_ref'       => $validated['uif_sdl_ref'] ?? null,
                'entity_address'         => $validated['entity_address'] ?? null,
                'number_of_directors'    => $validated['number_of_directors'],
                'tax_types'              => $validated['tax_types'] ?? [],
                'submission_method' => $validated['submission_method'],
                'notes'             => $validated['notes'] ?? null,
            ]);

            SarsRepAuditLog::log(
                $sarsRepRequest->id,
                'updated_request',
                'Request entity and representative details updated.'
            );

            DB::commit();

            return redirect()
                ->route('sarsrep.show', $sarsRepRequest->id)
                ->with('success', 'Request updated successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('SarsRepController@update failed: ' . $e->getMessage(), [
                'request_id' => $id,
                'trace'      => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update the request. Please try again.');
        }
    }

    /**
     * Delete the specified SARS representative request and all associated data.
     *
     * Removes uploaded document files from storage, then deletes
     * documents, audit logs, and the request record itself.
     */
    public function destroy($id): RedirectResponse
    {
        $sarsRepRequest = SarsRepRequest::with('sarsRepDocuments')->findOrFail($id);

        DB::beginTransaction();

        try {
            // Remove all uploaded document files from storage.
            foreach ($sarsRepRequest->sarsRepDocuments as $document) {
                if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                }
            }

            // Remove the entire request directory if it exists.
            $requestDirectory = 'sars_rep_docs/' . $sarsRepRequest->id;
            if (Storage::disk('public')->exists($requestDirectory)) {
                Storage::disk('public')->deleteDirectory($requestDirectory);
            }

            // Delete all associated document records.
            $sarsRepRequest->sarsRepDocuments()->delete();

            // Delete all associated audit log records.
            $sarsRepRequest->sarsRepAuditLogs()->delete();

            // Delete the request itself.
            $sarsRepRequest->delete();

            DB::commit();

            return redirect()
                ->route('sarsrep.index')
                ->with('success', 'Request and all associated data deleted successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('SarsRepController@destroy failed: ' . $e->getMessage(), [
                'request_id' => $id,
                'trace'      => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to delete the request. Please try again.');
        }
    }

    /**
     * Handle AJAX file upload for a document associated with the request.
     *
     * Generates a meaningful stored filename, stores the file, creates the
     * document record, checks for expiry requirements, and evaluates whether
     * all required documents have now been uploaded.
     */
    public function uploadDocument(Request $request, $id): JsonResponse
    {
        try {
            $sarsRepRequest = SarsRepRequest::findOrFail($id);

            $validated = $request->validate([
                'document_type' => 'required|string',
                'file'          => 'required|file|max:5120',
            ]);

            $file = $request->file('file');
            $documentType = $validated['document_type'];
            $entityName = Str::slug($sarsRepRequest->entity_name, '_');
            $docTypeSlug = Str::slug($documentType, '_');
            $timestamp = now()->format('Ymd_His');
            $extension = $file->getClientOriginalExtension();
            $storedFilename = "{$entityName}_{$docTypeSlug}_{$timestamp}.{$extension}";

            // Store the file in storage/app/public/sars_rep_docs/{request_id}/.
            $storagePath = "sars_rep_docs/{$sarsRepRequest->id}";
            $filePath = $file->storeAs($storagePath, $storedFilename, 'public');

            // Determine expiry date for documents that require it.
            $expiryDate = null;
            if (in_array($documentType, ['representative_id', 'representative_address'])) {
                $expiryDate = Carbon::now()->addMonths(3)->toDateString();
            }

            // Create the document record.
            $document = SarsRepDocument::create([
                'sars_rep_request_id' => $sarsRepRequest->id,
                'document_type'       => $documentType,
                'file_path'           => $filePath,
                'original_filename'   => $file->getClientOriginalName(),
                'stored_filename'     => $storedFilename,
                'file_size'           => $file->getSize(),
                'mime_type'           => $file->getMimeType(),
                'status'              => 'uploaded',
                'expiry_date'         => $expiryDate,
                'uploaded_by'         => Auth::id(),
            ]);

            // Log the upload in the audit trail.
            SarsRepAuditLog::log(
                $sarsRepRequest->id,
                'uploaded_document',
                "Uploaded document '{$documentType}': {$file->getClientOriginalName()}"
            );

            // Refresh the request to recalculate completion.
            $sarsRepRequest->refresh();

            // If all required documents are now uploaded, update the status.
            if ($sarsRepRequest->isReadyForSubmission()
                && $sarsRepRequest->status === SarsRepRequest::STATUS_AWAITING_DOCUMENTS
            ) {
                $sarsRepRequest->update(['status' => SarsRepRequest::STATUS_READY_FOR_REVIEW]);

                SarsRepAuditLog::log(
                    $sarsRepRequest->id,
                    'status_changed',
                    'All required documents uploaded. Status changed to ready for review.'
                );
            }

            $completionPercentage = $sarsRepRequest->getCompletionPercentage();

            return response()->json([
                'success'               => true,
                'message'               => 'Document uploaded successfully.',
                'document'              => [
                    'id'                => $document->id,
                    'document_type'     => $document->document_type,
                    'original_filename' => $document->original_filename,
                    'file_size'         => $document->file_size,
                    'expiry_date'       => $document->expiry_date ? $document->expiry_date->format('Y-m-d') : null,
                ],
                'completion_percentage' => $completionPercentage,
                'status'                => $sarsRepRequest->status,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (Exception $e) {
            Log::error('SarsRepController@uploadDocument failed: ' . $e->getMessage(), [
                'request_id' => $id,
                'trace'      => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document. Please try again.',
            ], 500);
        }
    }

    /**
     * Handle AJAX deletion of a document associated with the request.
     *
     * Removes the file from storage, deletes the record, and re-evaluates
     * the request status if documents are no longer complete.
     */
    public function deleteDocument($id, $docId): JsonResponse
    {
        try {
            $sarsRepRequest = SarsRepRequest::findOrFail($id);
            $document = SarsRepDocument::where('id', $docId)
                ->where('sars_rep_request_id', $sarsRepRequest->id)
                ->firstOrFail();

            $documentType = $document->document_type;
            $originalFilename = $document->original_filename;

            // Delete the file from storage.
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Delete the document record.
            $document->delete();

            // Log the deletion in the audit trail.
            SarsRepAuditLog::log(
                $sarsRepRequest->id,
                'deleted_document',
                "Deleted document '{$documentType}': {$originalFilename}"
            );

            // Re-evaluate the request status if it was previously ready.
            $sarsRepRequest->refresh();
            if (! $sarsRepRequest->isReadyForSubmission()
                && in_array($sarsRepRequest->status, [
                    SarsRepRequest::STATUS_READY_FOR_REVIEW,
                    SarsRepRequest::STATUS_READY_FOR_SUBMISSION,
                ])
            ) {
                $sarsRepRequest->update(['status' => SarsRepRequest::STATUS_AWAITING_DOCUMENTS]);

                SarsRepAuditLog::log(
                    $sarsRepRequest->id,
                    'status_changed',
                    'Required document removed. Status reverted to awaiting documents.'
                );
            }

            $completionPercentage = $sarsRepRequest->getCompletionPercentage();

            return response()->json([
                'success'               => true,
                'message'               => 'Document deleted successfully.',
                'completion_percentage' => $completionPercentage,
                'status'                => $sarsRepRequest->status,
            ]);

        } catch (Exception $e) {
            Log::error('SarsRepController@deleteDocument failed: ' . $e->getMessage(), [
                'request_id' => $id,
                'document_id' => $docId,
                'trace'       => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document. Please try again.',
            ], 500);
        }
    }

    /**
     * Generate a document (Mandate, Resolution, or Cover Letter) for the request.
     *
     * Attempts to use Barryvdh\DomPDF to produce a PDF. If the package is not
     * installed, falls back to rendering the Blade template as an HTML file.
     */
    public function generateDocument(Request $request, $id, $type): JsonResponse
    {
        $allowedTypes = ['mandate', 'resolution', 'cover_letter'];

        if (! in_array($type, $allowedTypes)) {
            return response()->json([
                'success' => false,
                'message' => "Invalid document type '{$type}'. Allowed types: " . implode(', ', $allowedTypes),
            ], 422);
        }

        try {
            $sarsRepRequest = SarsRepRequest::with('sarsRepresentative')->findOrFail($id);

            // Render the Blade template to HTML.
            $html = View::make("sarsrepresentative::templates.{$type}", [
                'sarsRepRequest'   => $sarsRepRequest,
                'representative'   => $sarsRepRequest->sarsRepresentative,
                'generatedAt'     => now()->format('d F Y'),
                'numberOfDirectors' => $sarsRepRequest->number_of_directors,
            ])->render();

            $entitySlug = Str::slug($sarsRepRequest->entity_name, '_');
            $timestamp = now()->format('Ymd_His');
            $storagePath = "sars_rep_docs/{$sarsRepRequest->id}";

            // Ensure the storage directory exists.
            Storage::disk('public')->makeDirectory($storagePath);

            $generatedAsPdf = false;
            $filePath = null;
            $storedFilename = null;
            $mimeType = null;

            // Attempt PDF generation with Barryvdh\DomPDF.
            try {
                if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
                        ->setPaper('a4', 'portrait');

                    $storedFilename = "{$entitySlug}_{$type}_{$timestamp}.pdf";
                    $fullPath = Storage::disk('public')->path("{$storagePath}/{$storedFilename}");
                    $pdf->save($fullPath);

                    $filePath = "{$storagePath}/{$storedFilename}";
                    $mimeType = 'application/pdf';
                    $generatedAsPdf = true;
                }
            } catch (Exception $e) {
                // DomPDF not available or failed; fall through to HTML fallback.
                Log::info('DomPDF not available, falling back to HTML generation.', [
                    'error' => $e->getMessage(),
                ]);
            }

            // Fallback: save as HTML file.
            if (! $generatedAsPdf) {
                $storedFilename = "{$entitySlug}_{$type}_{$timestamp}.html";
                $filePath = "{$storagePath}/{$storedFilename}";
                Storage::disk('public')->put($filePath, $html);
                $mimeType = 'text/html';
            }

            // Get the file size from storage.
            $fileSize = Storage::disk('public')->size($filePath);

            // Create the document record.
            $documentTypeMap = [
                'mandate'      => 'sars_mandate',
                'resolution'   => 'resolution',
                'cover_letter' => 'cover_letter',
            ];

            $document = SarsRepDocument::create([
                'sars_rep_request_id' => $sarsRepRequest->id,
                'document_type'       => $documentTypeMap[$type],
                'file_path'           => $filePath,
                'original_filename'   => $storedFilename,
                'stored_filename'     => $storedFilename,
                'file_size'           => $fileSize,
                'mime_type'           => $mimeType,
                'status'              => 'generated',
                'uploaded_by'         => Auth::id(),
            ]);

            // Log the generation in the audit trail.
            $formatLabel = $generatedAsPdf ? 'PDF' : 'HTML';
            SarsRepAuditLog::log(
                $sarsRepRequest->id,
                'generated_document',
                "Generated {$type} document as {$formatLabel}: {$storedFilename}"
            );

            // Re-evaluate request completion and status.
            $sarsRepRequest->refresh();
            if ($sarsRepRequest->isReadyForSubmission()
                && $sarsRepRequest->status === SarsRepRequest::STATUS_AWAITING_DOCUMENTS
            ) {
                $sarsRepRequest->update(['status' => SarsRepRequest::STATUS_READY_FOR_REVIEW]);

                SarsRepAuditLog::log(
                    $sarsRepRequest->id,
                    'status_changed',
                    'All required documents available. Status changed to ready for review.'
                );
            }

            $completionPercentage = $sarsRepRequest->getCompletionPercentage();

            return response()->json([
                'success'               => true,
                'message'               => ucfirst($type) . " generated successfully as {$formatLabel}.",
                'document'              => [
                    'id'                => $document->id,
                    'document_type'     => $document->document_type,
                    'original_filename' => $document->original_filename,
                    'format'            => $generatedAsPdf ? 'pdf' : 'html',
                ],
                'completion_percentage' => $completionPercentage,
                'status'                => $sarsRepRequest->status,
            ]);

        } catch (Exception $e) {
            Log::error('SarsRepController@generateDocument failed: ' . $e->getMessage(), [
                'request_id' => $id,
                'type'        => $type,
                'trace'       => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => "Failed to generate {$type} document. Please try again.",
            ], 500);
        }
    }

    /**
     * Generate the final indexed PDF bundle combining all request documents.
     *
     * Attempts PDF generation via Barryvdh\DomPDF, with an HTML fallback.
     */
    public function generateBundle(Request $request, $id): JsonResponse
    {
        try {
            $sarsRepRequest = SarsRepRequest::with([
                'sarsRepresentative',
                'sarsRepDocuments',
            ])->findOrFail($id);

            // Render the bundle view combining all documents.
            $html = View::make('sarsrepresentative::templates.bundle_index', [
                'sarsRepRequest' => $sarsRepRequest,
                'representative' => $sarsRepRequest->sarsRepresentative,
                'documents'      => $sarsRepRequest->sarsRepDocuments,
                'generatedAt'   => now()->format('d F Y'),
            ])->render();

            $entitySlug = Str::slug($sarsRepRequest->entity_name, '_');
            $timestamp = now()->format('Ymd_His');
            $storagePath = "sars_rep_docs/{$sarsRepRequest->id}";

            Storage::disk('public')->makeDirectory($storagePath);

            $generatedAsPdf = false;
            $filePath = null;
            $storedFilename = null;
            $mimeType = null;

            // Attempt PDF generation with Barryvdh\DomPDF.
            try {
                if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
                        ->setPaper('a4', 'portrait');

                    $storedFilename = "{$entitySlug}_complete_bundle_{$timestamp}.pdf";
                    $fullPath = Storage::disk('public')->path("{$storagePath}/{$storedFilename}");
                    $pdf->save($fullPath);

                    $filePath = "{$storagePath}/{$storedFilename}";
                    $mimeType = 'application/pdf';
                    $generatedAsPdf = true;
                }
            } catch (Exception $e) {
                Log::info('DomPDF not available for bundle generation, falling back to HTML.', [
                    'error' => $e->getMessage(),
                ]);
            }

            // Fallback: save as HTML.
            if (! $generatedAsPdf) {
                $storedFilename = "{$entitySlug}_complete_bundle_{$timestamp}.html";
                $filePath = "{$storagePath}/{$storedFilename}";
                Storage::disk('public')->put($filePath, $html);
                $mimeType = 'text/html';
            }

            $fileSize = Storage::disk('public')->size($filePath);

            $document = SarsRepDocument::create([
                'sars_rep_request_id' => $sarsRepRequest->id,
                'document_type'       => 'generated_pdf_bundle',
                'file_path'           => $filePath,
                'original_filename'   => $storedFilename,
                'stored_filename'     => $storedFilename,
                'file_size'           => $fileSize,
                'mime_type'           => $mimeType,
                'status'              => 'generated',
                'uploaded_by'         => Auth::id(),
            ]);

            $formatLabel = $generatedAsPdf ? 'PDF' : 'HTML';
            SarsRepAuditLog::log(
                $sarsRepRequest->id,
                'generated_bundle',
                "Generated complete document bundle as {$formatLabel}: {$storedFilename}"
            );

            return response()->json([
                'success'  => true,
                'message'  => "Document bundle generated successfully as {$formatLabel}.",
                'document' => [
                    'id'                => $document->id,
                    'document_type'     => $document->document_type,
                    'original_filename' => $document->original_filename,
                    'format'            => $generatedAsPdf ? 'pdf' : 'html',
                ],
            ]);

        } catch (Exception $e) {
            Log::error('SarsRepController@generateBundle failed: ' . $e->getMessage(), [
                'request_id' => $id,
                'trace'      => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate document bundle. Please try again.',
            ], 500);
        }
    }

    /**
     * Download a document file associated with the request.
     */
    public function downloadDocument($id, $docId): BinaryFileResponse|JsonResponse
    {
        try {
            $sarsRepRequest = SarsRepRequest::findOrFail($id);
            $document = SarsRepDocument::where('id', $docId)
                ->where('sars_rep_request_id', $sarsRepRequest->id)
                ->firstOrFail();

            if (! $document->file_path || ! Storage::disk('public')->exists($document->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document file not found on server.',
                ], 404);
            }

            $fullPath = Storage::disk('public')->path($document->file_path);
            $downloadName = $document->original_filename ?: $document->stored_filename;

            return response()->download($fullPath, $downloadName, [
                'Content-Type' => $document->mime_type,
            ]);

        } catch (Exception $e) {
            Log::error('SarsRepController@downloadDocument failed: ' . $e->getMessage(), [
                'request_id'  => $id,
                'document_id' => $docId,
                'trace'       => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to download document.',
            ], 500);
        }
    }

    /**
     * Handle AJAX status update for a SARS representative request.
     *
     * Records the status change in the status history table and
     * creates an audit log entry.
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $sarsRepRequest = SarsRepRequest::findOrFail($id);

            $validated = $request->validate([
                'status'           => 'required|string|in:' . implode(',', SarsRepRequest::STATUSES),
                'rejection_reason' => 'nullable|string|max:1000',
            ]);

            $oldStatus = $sarsRepRequest->status;
            $newStatus = $validated['status'];

            if ($oldStatus === $newStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'The status is already set to the selected value.',
                ], 422);
            }

            DB::beginTransaction();

            // Record the status change in the history table.
            DB::table('sars_rep_status_history')->insert([
                'sars_rep_request_id' => $sarsRepRequest->id,
                'from_status'         => $oldStatus,
                'to_status'           => $newStatus,
                'changed_by'          => Auth::id(),
                'changed_at'          => now(),
            ]);

            // Update the request status and related timestamps.
            $updateData = ['status' => $newStatus];

            if (in_array($newStatus, [
                SarsRepRequest::STATUS_SUBMITTED_BRANCH,
                SarsRepRequest::STATUS_SUBMITTED_EFILING,
            ])) {
                $updateData['submitted_at'] = now();
            }

            if ($newStatus === SarsRepRequest::STATUS_APPROVED) {
                $updateData['approved_at'] = now();
            }

            if ($newStatus === SarsRepRequest::STATUS_REJECTED && ! empty($validated['rejection_reason'])) {
                $updateData['rejection_reason'] = $validated['rejection_reason'];
            }

            $sarsRepRequest->update($updateData);

            // Log the status change in the audit trail.
            $statusLabel = ucfirst(str_replace('_', ' ', $newStatus));
            $description = "Status changed from '{$oldStatus}' to '{$newStatus}'.";

            if (! empty($validated['rejection_reason'])) {
                $description .= " Reason: {$validated['rejection_reason']}";
            }

            SarsRepAuditLog::log(
                $sarsRepRequest->id,
                'status_changed',
                $description
            );

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => "Status updated to '{$statusLabel}'.",
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('SarsRepController@updateStatus failed: ' . $e->getMessage(), [
                'request_id' => $id,
                'trace'      => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status. Please try again.',
            ], 500);
        }
    }

    /**
     * Display the audit trail for the specified SARS representative request.
     */
    public function audit($id)
    {
        $sarsRepRequest = SarsRepRequest::with('sarsRepresentative')->findOrFail($id);

        $auditLogs = SarsRepAuditLog::where('sars_rep_request_id', $sarsRepRequest->id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('sarsrepresentative::sarsrep.audit', compact('sarsRepRequest', 'auditLogs'));
    }
}
