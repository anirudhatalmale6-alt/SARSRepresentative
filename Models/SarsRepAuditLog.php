<?php

namespace Modules\SARSRepresentative\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class SarsRepAuditLog extends Model
{
    protected $table = 'sars_rep_audit_logs';

    protected $fillable = [
        'sars_rep_request_id',
        'performed_by',
        'action',
        'description',
    ];

    /**
     * Get the SARS representative request this audit log belongs to.
     */
    public function sarsRepRequest(): BelongsTo
    {
        return $this->belongsTo(SarsRepRequest::class, 'sars_rep_request_id');
    }

    /**
     * Create an audit log entry for a given request.
     *
     * Uses the currently authenticated user as the performer.
     */
    public static function log(int $requestId, string $action, ?string $description = null): static
    {
        return static::create([
            'sars_rep_request_id' => $requestId,
            'performed_by'        => Auth::id(),
            'action'              => $action,
            'description'         => $description,
        ]);
    }
}
