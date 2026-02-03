<?php

namespace Modules\SARSRepresentative\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SarsRepRequest extends Model
{
    protected $table = 'sars_rep_requests';

    protected $fillable = [
        'entity_name',
        'entity_reg_number',
        'entity_type',
        'income_tax_ref',
        'paye_ref',
        'vat_ref',
        'uif_sdl_ref',
        'entity_address',
        'sars_representative_id',
        'tax_types',
        'submission_method',
        'status',
        'rejection_reason',
        'submitted_at',
        'approved_at',
        'notes',
        'created_by',
        'assigned_to',
    ];

    protected $casts = [
        'tax_types'    => 'array',
        'submitted_at' => 'datetime',
        'approved_at'  => 'datetime',
    ];

    /**
     * Entity type enum values.
     */
    const ENTITY_TYPE_COMPANY = 'company';
    const ENTITY_TYPE_TRUST = 'trust';
    const ENTITY_TYPE_NPC = 'npc';
    const ENTITY_TYPE_NPO = 'npo';
    const ENTITY_TYPE_SOLE_DIRECTOR_COMPANY = 'sole_director_company';
    const ENTITY_TYPE_SOLE_TRUSTEE_TRUST = 'sole_trustee_trust';

    const ENTITY_TYPES = [
        self::ENTITY_TYPE_COMPANY,
        self::ENTITY_TYPE_TRUST,
        self::ENTITY_TYPE_NPC,
        self::ENTITY_TYPE_NPO,
        self::ENTITY_TYPE_SOLE_DIRECTOR_COMPANY,
        self::ENTITY_TYPE_SOLE_TRUSTEE_TRUST,
    ];

    /**
     * Status enum values.
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_AWAITING_DOCUMENTS = 'awaiting_documents';
    const STATUS_READY_FOR_REVIEW = 'ready_for_review';
    const STATUS_READY_FOR_SUBMISSION = 'ready_for_submission';
    const STATUS_SUBMITTED_BRANCH = 'submitted_branch';
    const STATUS_SUBMITTED_EFILING = 'submitted_efiling';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_AWAITING_DOCUMENTS,
        self::STATUS_READY_FOR_REVIEW,
        self::STATUS_READY_FOR_SUBMISSION,
        self::STATUS_SUBMITTED_BRANCH,
        self::STATUS_SUBMITTED_EFILING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
    ];

    /**
     * Get the SARS representative associated with this request.
     */
    public function sarsRepresentative(): BelongsTo
    {
        return $this->belongsTo(SarsRepresentative::class, 'sars_representative_id');
    }

    /**
     * Get the documents for this request.
     */
    public function sarsRepDocuments(): HasMany
    {
        return $this->hasMany(SarsRepDocument::class, 'sars_rep_request_id');
    }

    /**
     * Get the audit logs for this request.
     */
    public function sarsRepAuditLogs(): HasMany
    {
        return $this->hasMany(SarsRepAuditLog::class, 'sars_rep_request_id');
    }

    /**
     * Get the Bootstrap badge class based on the current status.
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT                 => 'badge-secondary',
            self::STATUS_AWAITING_DOCUMENTS    => 'badge-warning',
            self::STATUS_READY_FOR_REVIEW      => 'badge-info',
            self::STATUS_READY_FOR_SUBMISSION  => 'badge-primary',
            self::STATUS_SUBMITTED_BRANCH      => 'badge-info',
            self::STATUS_SUBMITTED_EFILING     => 'badge-info',
            self::STATUS_APPROVED              => 'badge-success',
            self::STATUS_REJECTED              => 'badge-danger',
            default                            => 'badge-secondary',
        };
    }

    /**
     * Get a human-readable label for the entity type.
     */
    public function getEntityTypeLabel(): string
    {
        $labels = [
            self::ENTITY_TYPE_COMPANY              => 'Company',
            self::ENTITY_TYPE_TRUST                => 'Trust',
            self::ENTITY_TYPE_NPC                  => 'Non-Profit Company (NPC)',
            self::ENTITY_TYPE_NPO                  => 'Non-Profit Organisation (NPO)',
            self::ENTITY_TYPE_SOLE_DIRECTOR_COMPANY => 'Sole Director Company',
            self::ENTITY_TYPE_SOLE_TRUSTEE_TRUST   => 'Sole Trustee Trust',
        ];

        return $labels[$this->entity_type] ?? ucfirst(str_replace('_', ' ', $this->entity_type));
    }

    /**
     * Get the required document type keys based on the entity type.
     *
     * All types require: representative_id, representative_address,
     * representative_photo, entity_registration, sars_mandate, resolution, cover_letter.
     *
     * Trust-based types also require: trust_deed, letters_of_authority.
     * NPC/NPO types also include: npo_certificate (optional).
     *
     * @return array<string, bool> Keys are document type strings, values indicate if required (true) or optional (false).
     */
    public function getRequiredDocumentTypes(): array
    {
        $documents = [
            'representative_id'      => true,
            'representative_address' => true,
            'representative_photo'   => true,
            'entity_registration'    => true,
            'sars_mandate'           => true,
            'resolution'             => true,
            'cover_letter'           => true,
        ];

        // Trust-based entity types require additional documents.
        if (in_array($this->entity_type, [
            self::ENTITY_TYPE_TRUST,
            self::ENTITY_TYPE_SOLE_TRUSTEE_TRUST,
        ])) {
            $documents['trust_deed']           = true;
            $documents['letters_of_authority'] = true;
        }

        // NPC/NPO entity types include an optional certificate.
        if (in_array($this->entity_type, [
            self::ENTITY_TYPE_NPC,
            self::ENTITY_TYPE_NPO,
        ])) {
            $documents['npo_certificate'] = false;
        }

        return $documents;
    }

    /**
     * Calculate the percentage of required documents that have been uploaded.
     */
    public function getCompletionPercentage(): float
    {
        $requiredTypes = $this->getRequiredDocumentTypes();

        // Only count mandatory (required = true) document types.
        $requiredKeys = array_keys(array_filter($requiredTypes, fn ($required) => $required === true));

        if (count($requiredKeys) === 0) {
            return 100.0;
        }

        $uploadedTypes = $this->sarsRepDocuments()
            ->pluck('document_type')
            ->unique()
            ->toArray();

        $uploadedRequiredCount = count(array_intersect($requiredKeys, $uploadedTypes));

        return round(($uploadedRequiredCount / count($requiredKeys)) * 100, 2);
    }

    /**
     * Check if all required documents have been uploaded, making the request ready for submission.
     */
    public function isReadyForSubmission(): bool
    {
        $requiredTypes = $this->getRequiredDocumentTypes();

        $requiredKeys = array_keys(array_filter($requiredTypes, fn ($required) => $required === true));

        $uploadedTypes = $this->sarsRepDocuments()
            ->pluck('document_type')
            ->unique()
            ->toArray();

        foreach ($requiredKeys as $key) {
            if (! in_array($key, $uploadedTypes)) {
                return false;
            }
        }

        return true;
    }
}
