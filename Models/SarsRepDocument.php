<?php

namespace Modules\SARSRepresentative\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SarsRepDocument extends Model
{
    protected $table = 'sars_rep_documents';

    protected $fillable = [
        'sars_rep_request_id',
        'document_type',
        'file_path',
        'original_filename',
        'stored_filename',
        'file_size',
        'mime_type',
        'status',
        'expiry_date',
        'notes',
        'uploaded_by',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    /**
     * Get the SARS representative request this document belongs to.
     */
    public function sarsRepRequest(): BelongsTo
    {
        return $this->belongsTo(SarsRepRequest::class, 'sars_rep_request_id');
    }

    /**
     * Get a human-readable label for a given document type.
     */
    public static function getDocumentTypeLabel(string $type): string
    {
        $labels = [
            'representative_id'      => 'Representative ID Document',
            'representative_address' => 'Representative Proof of Address',
            'representative_photo'   => 'Representative Passport Photo',
            'entity_registration'    => 'Entity Registration Document',
            'sars_mandate'           => 'SARS Mandate / Power of Attorney',
            'resolution'             => 'Resolution',
            'cover_letter'           => 'Cover Letter',
            'trust_deed'             => 'Trust Deed',
            'letters_of_authority'   => 'Letters of Authority',
            'npo_certificate'        => 'NPO Certificate',
        ];

        return $labels[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    /**
     * Check if the document has expired based on its expiry date.
     */
    public function isExpired(): bool
    {
        if (is_null($this->expiry_date)) {
            return false;
        }

        return Carbon::parse($this->expiry_date)->isPast();
    }
}
