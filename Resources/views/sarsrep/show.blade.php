@extends('clientmaster::layouts.default')

@section('title', 'SARS Representative: ' . $sarsRepRequest->entity_name)
@section('header_title', 'SARS Representative')

@push('styles')
<link href="/public/smartdash/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<link href="/public/smartdash/css/smartdash-forms.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* Detail Section Card */
.detail-section {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
    border-left: 4px solid #17A2B8;
}
.section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e2e8f0;
}
.section-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.section-icon.entity   { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #2563eb; }
.section-icon.rep      { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #059669; }
.section-icon.docs     { background: linear-gradient(135deg, #ffedd5 0%, #fed7aa 100%); color: #ea580c; }
.section-icon.generate { background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #4f46e5; }
.section-icon.bundle   { background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%); color: #db2777; }
.section-icon.status   { background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%); color: #0891b2; }
.section-icon.audit    { background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); color: #475569; }
.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

/* Detail Row */
.detail-row {
    display: flex;
    padding: 10px 0;
    border-bottom: 1px solid #f1f5f9;
}
.detail-row:last-child { border-bottom: none; }
.detail-label {
    width: 40%;
    font-weight: 600;
    color: #64748b;
    font-size: 13px;
}
.detail-value {
    width: 60%;
    color: #1e293b;
    font-weight: 500;
    font-size: 14px;
}
.detail-value.empty {
    color: #94a3b8;
    font-style: italic;
}

/* Header Area */
.entity-name-display {
    font-size: 28px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 5px;
}
.status-badge-lg {
    display: inline-block;
    padding: 8px 20px;
    border-radius: 25px;
    font-weight: 700;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Tax Type Badge */
.tax-type-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 12px;
    background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
    color: #4f46e5;
    margin-right: 4px;
    margin-bottom: 4px;
}

/* Document Checklist */
.doc-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-radius: 10px;
    margin-bottom: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}
.doc-item:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}
.doc-item.uploaded {
    background: linear-gradient(145deg, #f0fdf4 0%, #dcfce7 100%);
    border-color: #86efac;
}
.doc-item.missing {
    background: linear-gradient(145deg, #fef2f2 0%, #fee2e2 100%);
    border-color: #fca5a5;
}
.doc-item.expired {
    background: linear-gradient(145deg, #fffbeb 0%, #fef3c7 100%);
    border-color: #fcd34d;
}
.doc-status-icon {
    font-size: 20px;
    margin-right: 12px;
    flex-shrink: 0;
}
.doc-status-icon.uploaded { color: #16a34a; }
.doc-status-icon.missing  { color: #dc2626; }
.doc-status-icon.expired  { color: #d97706; }
.doc-label {
    font-weight: 600;
    font-size: 14px;
    color: #1e293b;
    flex: 1;
}
.doc-meta {
    font-size: 12px;
    color: #64748b;
    margin-top: 2px;
}
.doc-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

/* Upload Form */
.upload-form {
    display: flex;
    align-items: center;
    gap: 8px;
}
.upload-form .form-control {
    font-size: 13px !important;
    min-height: 38px !important;
    border: 2px solid #17A2B8 !important;
    border-radius: 8px !important;
    padding: 4px 8px;
    max-width: 220px;
}

/* Progress Bar */
.completion-progress {
    height: 12px;
    border-radius: 6px;
    background: #e2e8f0;
    overflow: hidden;
    margin-bottom: 8px;
}
.completion-progress .fill {
    height: 100%;
    border-radius: 6px;
    transition: width 0.3s ease;
}

/* Generate Buttons */
.btn-generate {
    padding: 14px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    border: none;
    color: #fff;
    transition: all 0.3s ease;
    width: 100%;
}
.btn-generate:hover {
    transform: translateY(-2px);
    color: #fff;
}
.btn-generate.cover-letter { background: linear-gradient(135deg, #3b82f6, #2563eb); box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4); }
.btn-generate.cover-letter:hover { box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5); }
.btn-generate.mandate { background: linear-gradient(135deg, #8b5cf6, #7c3aed); box-shadow: 0 4px 14px rgba(139, 92, 246, 0.4); }
.btn-generate.mandate:hover { box-shadow: 0 6px 20px rgba(139, 92, 246, 0.5); }
.btn-generate.resolution { background: linear-gradient(135deg, #0d9488, #0f766e); box-shadow: 0 4px 14px rgba(13, 148, 136, 0.4); }
.btn-generate.resolution:hover { box-shadow: 0 6px 20px rgba(13, 148, 136, 0.5); }

/* Bundle Button */
.btn-bundle {
    background: linear-gradient(135deg, #db2777, #be185d);
    border: none;
    color: #fff;
    padding: 16px 32px;
    font-weight: 700;
    border-radius: 12px;
    font-size: 16px;
    box-shadow: 0 4px 14px rgba(219, 39, 119, 0.4);
    transition: all 0.3s ease;
}
.btn-bundle:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(219, 39, 119, 0.5);
    color: #fff;
}
.btn-bundle:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Action Buttons */
.btn-back {
    background: #f1f5f9;
    border: 2px solid #e2e8f0;
    color: #475569;
    padding: 10px 24px;
    font-weight: 600;
    border-radius: 12px;
    transition: all 0.2s ease;
}
.btn-back:hover { background: #e2e8f0; color: #1e293b; }
.btn-edit {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border: none;
    color: #fff;
    padding: 10px 24px;
    font-weight: 600;
    border-radius: 12px;
    transition: all 0.2s ease;
}
.btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
    color: #fff;
}

/* Audit Trail */
.audit-table {
    border-radius: 8px;
    overflow: hidden;
}
.audit-table thead {
    background: #0d3d56;
}
.audit-table thead th {
    color: #fff !important;
    font-weight: 600;
    padding: 12px 15px;
    border: none;
    font-size: 13px;
}
.audit-table tbody td {
    padding: 10px 15px;
    border-bottom: 1px solid #eee;
    font-size: 13px;
}

/* Status Update Section */
.status-current {
    font-size: 16px;
    font-weight: 700;
    padding: 8px 20px;
    border-radius: 20px;
    display: inline-block;
}
</style>
@endpush

@php
    // Status badge color mapping
    $statusColors = [
        'draft'                => 'secondary',
        'awaiting_documents'   => 'warning',
        'ready_for_review'     => 'info',
        'ready_for_submission' => 'primary',
        'submitted_branch'     => 'primary',
        'submitted_efiling'    => 'primary',
        'approved'             => 'success',
        'rejected'             => 'danger',
    ];
    $badgeColor  = $statusColors[$sarsRepRequest->status] ?? 'secondary';
    $statusLabel = ucwords(str_replace('_', ' ', $sarsRepRequest->status));

    // Completion bar color
    if ($completionPercentage >= 100) {
        $barColor = '#10b981';
    } elseif ($completionPercentage >= 60) {
        $barColor = '#3b82f6';
    } elseif ($completionPercentage >= 30) {
        $barColor = '#f59e0b';
    } else {
        $barColor = '#ef4444';
    }
@endphp

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row page-titles">
        <div class="d-flex align-items-center justify-content-between">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="fs-2" style="color:#000" href="javascript:void(0)">CIMS</a></li>
                <li class="breadcrumb-item"><a class="fs-2" style="color:#17A2B8" href="{{ route('sarsrep.index') }}">SARS Representative</a></li>
                <li class="breadcrumb-item active"><a class="fs-2" style="color:#009688" href="javascript:void(0)">{{ $sarsRepRequest->entity_name }}</a></li>
            </ol>
            <a href="{{ route('sarsrep.index') }}" class="btn btn-back">
                <i class="fa fa-arrow-left me-2"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Header Area -->
    <div class="text-center mb-4">
        <div class="entity-name-display">{{ $sarsRepRequest->entity_name }}</div>
        <div class="mt-2">
            <span class="status-badge-lg bg-{{ $badgeColor }}" style="color: #fff;">
                <i class="fa fa-circle-dot me-1"></i> {{ $statusLabel }}
            </span>
        </div>
    </div>

    <!-- ================================================================ -->
    <!-- SECTION 1: Entity & Representative Details -->
    <!-- ================================================================ -->
    <div class="row">
        <!-- Left Column: Entity Details -->
        <div class="col-lg-6">
            <div class="detail-section">
                <div class="section-header">
                    <div class="section-icon entity"><i class="fa fa-building"></i></div>
                    <h5 class="section-title">Entity Details</h5>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Entity Name</div>
                    <div class="detail-value">{{ $sarsRepRequest->entity_name }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Registration Number</div>
                    <div class="detail-value {{ !$sarsRepRequest->entity_reg_number ? 'empty' : '' }}">{{ $sarsRepRequest->entity_reg_number ?: 'Not set' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Entity Type</div>
                    <div class="detail-value {{ !$sarsRepRequest->entity_type ? 'empty' : '' }}">{{ $sarsRepRequest->entity_type ?: 'Not set' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Address</div>
                    <div class="detail-value {{ !$sarsRepRequest->entity_address ? 'empty' : '' }}">{{ $sarsRepRequest->entity_address ?: 'Not set' }}</div>
                </div>

                <div class="form-section-title mt-3">
                    <i class="fa fa-file-invoice"></i> Tax References
                </div>
                <div class="detail-row">
                    <div class="detail-label">Income Tax Number</div>
                    <div class="detail-value {{ !$sarsRepRequest->income_tax_ref ? 'empty' : '' }}">{{ $sarsRepRequest->income_tax_ref ?: 'Not set' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">PAYE Reference</div>
                    <div class="detail-value {{ !$sarsRepRequest->paye_ref ? 'empty' : '' }}">{{ $sarsRepRequest->paye_ref ?: 'Not set' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">VAT Number</div>
                    <div class="detail-value {{ !$sarsRepRequest->vat_ref ? 'empty' : '' }}">{{ $sarsRepRequest->vat_ref ?: 'Not set' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">UIF / SDL Number</div>
                    <div class="detail-value {{ !$sarsRepRequest->uif_sdl_ref ? 'empty' : '' }}">{{ $sarsRepRequest->uif_sdl_ref ?: 'Not set' }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Tax Types</div>
                    <div class="detail-value">
                        @if($sarsRepRequest->tax_types && is_array($sarsRepRequest->tax_types))
                            @foreach($sarsRepRequest->tax_types as $taxType)
                                <span class="tax-type-badge">{{ $taxType }}</span>
                            @endforeach
                        @else
                            <span class="empty">None selected</span>
                        @endif
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Submission Method</div>
                    <div class="detail-value {{ !$sarsRepRequest->submission_method ? 'empty' : '' }}">{{ $sarsRepRequest->submission_method ? ucwords(str_replace('_', ' ', $sarsRepRequest->submission_method)) : 'Not set' }}</div>
                </div>
            </div>
        </div>

        <!-- Right Column: Representative Details -->
        <div class="col-lg-6">
            <div class="detail-section">
                <div class="section-header">
                    <div class="section-icon rep"><i class="fa fa-user-tie"></i></div>
                    <h5 class="section-title">Representative Details</h5>
                </div>
                @if($sarsRepRequest->sarsRepresentative)
                    <div class="detail-row">
                        <div class="detail-label">Full Name</div>
                        <div class="detail-value">{{ $sarsRepRequest->sarsRepresentative->full_name ?: 'Not set' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">ID / Passport Number</div>
                        <div class="detail-value {{ !$sarsRepRequest->sarsRepresentative->id_number ? 'empty' : '' }}">{{ $sarsRepRequest->sarsRepresentative->id_number ?: 'Not set' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Capacity</div>
                        <div class="detail-value {{ !$sarsRepRequest->sarsRepresentative->capacity ? 'empty' : '' }}">{{ $sarsRepRequest->sarsRepresentative->capacity ?: 'Not set' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Email</div>
                        <div class="detail-value {{ !$sarsRepRequest->sarsRepresentative->email ? 'empty' : '' }}">{{ $sarsRepRequest->sarsRepresentative->email ?: 'Not set' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Mobile</div>
                        <div class="detail-value {{ !$sarsRepRequest->sarsRepresentative->mobile ? 'empty' : '' }}">{{ $sarsRepRequest->sarsRepresentative->mobile ?: 'Not set' }}</div>
                    </div>
                @else
                    <p class="text-muted mb-0">No representative information linked.</p>
                @endif
            </div>

            <!-- Edit Button -->
            <div class="d-flex justify-content-end mb-4">
                <a href="{{ route('sarsrep.edit', $sarsRepRequest->id) }}" class="btn btn-edit">
                    <i class="fa fa-pen me-2"></i> Edit Details
                </a>
            </div>
        </div>
    </div>

    <!-- ================================================================ -->
    <!-- SECTION 2: Document Checklist & Upload -->
    <!-- ================================================================ -->
    <div class="detail-section">
        <div class="section-header">
            <div class="section-icon docs"><i class="fa fa-folder-open"></i></div>
            <h5 class="section-title">Required Documents</h5>
        </div>

        <!-- Completion Progress -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span style="font-weight: 600; color: #1e293b;">Document Completion</span>
                <span style="font-weight: 700; font-size: 16px; color: {{ $barColor }};">{{ $completionPercentage }}%</span>
            </div>
            <div class="completion-progress">
                <div class="fill" style="width: {{ $completionPercentage }}%; background: {{ $barColor }};"></div>
            </div>
        </div>

        <!-- Document Items -->
        @foreach($documentChecklist as $docTypeKey => $docInfo)
            @php
                $docLabel   = $docInfo['label'];
                $isRequired = $docInfo['required'];
                $isUploaded = $docInfo['uploaded'];
                $document   = $docInfo['document'];
                $isExpired  = $docInfo['expired'];
            @endphp
            <div class="doc-item {{ $isUploaded ? ($isExpired ? 'expired' : 'uploaded') : 'missing' }}">
                <div class="d-flex align-items-center flex-grow-1">
                    @if($isUploaded && !$isExpired)
                        <i class="fa fa-check-circle doc-status-icon uploaded"></i>
                    @elseif($isUploaded && $isExpired)
                        <i class="fa fa-exclamation-circle doc-status-icon expired"></i>
                    @else
                        <i class="fa fa-times-circle doc-status-icon missing"></i>
                    @endif
                    <div>
                        <div class="doc-label">{{ $docLabel }}@if(!$isRequired) <span class="text-muted" style="font-weight: 400; font-size: 12px;">(Optional)</span>@endif</div>
                        @if($isUploaded && $document)
                            <div class="doc-meta">
                                <i class="fa fa-file me-1"></i> {{ $document->original_filename ?? $document->filename ?? 'File' }}
                                <span class="ms-2"><i class="fa fa-calendar me-1"></i> {{ $document->created_at ? $document->created_at->format('d M Y H:i') : '' }}</span>
                                @if($isExpired)
                                    <span class="ms-2 text-warning"><i class="fa fa-exclamation-triangle me-1"></i> Expired</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="doc-actions">
                    @if($isUploaded && $document)
                        <a href="{{ route('sarsrep.download', [$sarsRepRequest->id, $document->id]) }}" class="btn btn-sm sd_btn" title="Download">
                            <i class="fa fa-download"></i>
                        </a>
                        <button type="button" class="btn btn-sm sd_btn_danger" title="Delete" onclick="deleteDocument({{ $document->id }}, '{{ $docLabel }}')">
                            <i class="fa fa-trash"></i>
                        </button>
                    @else
                        <form class="upload-form" onsubmit="return false;">
                            <input type="file" class="form-control" id="file_{{ $docTypeKey }}" accept=".pdf,.jpg,.jpeg,.png">
                            <button type="button" class="btn btn-sm sd_btn" onclick="uploadDocument('{{ $docTypeKey }}', '{{ $docLabel }}')">
                                <i class="fa fa-upload me-1"></i> Upload
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- ================================================================ -->
    <!-- SECTION 3: Generate Documents -->
    <!-- ================================================================ -->
    <div class="detail-section">
        <div class="section-header">
            <div class="section-icon generate"><i class="fa fa-file-pdf"></i></div>
            <h5 class="section-title">Generate Documents</h5>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-3">
                <button type="button" class="btn btn-generate cover-letter" onclick="generateDocument('cover_letter')">
                    <i class="fa fa-envelope me-2"></i> Generate Cover Letter
                </button>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <button type="button" class="btn btn-generate mandate" onclick="generateDocument('mandate')">
                    <i class="fa fa-file-signature me-2"></i> Generate SARS Mandate
                </button>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <button type="button" class="btn btn-generate resolution" onclick="generateDocument('resolution')">
                    <i class="fa fa-gavel me-2"></i> Generate Resolution
                </button>
            </div>
        </div>
    </div>

    <!-- ================================================================ -->
    <!-- SECTION 4: Final PDF Bundle -->
    <!-- ================================================================ -->
    <div class="detail-section">
        <div class="section-header">
            <div class="section-icon bundle"><i class="fa fa-file-zipper"></i></div>
            <h5 class="section-title">Final PDF Bundle</h5>
        </div>
        <div class="text-center">
            <p class="text-muted mb-3">Generate a single indexed PDF containing all uploaded documents and generated letters.</p>
            <button type="button" class="btn btn-bundle" id="btnGenerateBundle" onclick="generateBundle()" {{ $completionPercentage < 100 ? 'disabled' : '' }}>
                <i class="fa fa-file-pdf me-2"></i> Generate Final Indexed PDF
            </button>
            @if($completionPercentage < 100)
                <div class="mt-2">
                    <small class="text-muted"><i class="fa fa-info-circle me-1"></i> All required documents must be uploaded before generating the final bundle.</small>
                </div>
            @endif
        </div>
    </div>

    <!-- ================================================================ -->
    <!-- SECTION 5: Status Management -->
    <!-- ================================================================ -->
    <div class="detail-section">
        <div class="section-header">
            <div class="section-icon status"><i class="fa fa-sliders"></i></div>
            <h5 class="section-title">Status Management</h5>
        </div>
        <div class="row align-items-center">
            <div class="col-md-4 mb-3">
                <label class="form-label" style="font-weight: 600; color: #64748b;">Current Status</label>
                <div>
                    <span class="status-current bg-{{ $badgeColor }}" style="color: #fff;">{{ $statusLabel }}</span>
                </div>
            </div>
            <div class="col-md-5 mb-3">
                <label for="new_status" class="form-label" style="font-weight: 600; color: #64748b;">Change Status To</label>
                <select id="new_status" class="form-select sd_drop_class">
                    <option value="">-- Select New Status --</option>
                    <option value="draft">Draft</option>
                    <option value="awaiting_documents">Awaiting Documents</option>
                    <option value="ready_for_review">Ready for Review</option>
                    <option value="ready_for_submission">Ready for Submission</option>
                    <option value="submitted_branch">Submitted (Branch)</option>
                    <option value="submitted_efiling">Submitted (eFiling)</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="col-md-3 mb-3 d-flex align-items-end">
                <button type="button" class="btn sd_btn w-100" onclick="updateStatus()">
                    <i class="fa fa-refresh me-2"></i> Update Status
                </button>
            </div>
        </div>
    </div>

    <!-- ================================================================ -->
    <!-- SECTION 6: Audit Trail -->
    <!-- ================================================================ -->
    <div class="detail-section">
        <div class="section-header">
            <div class="section-icon audit"><i class="fa fa-history"></i></div>
            <h5 class="section-title">Audit Trail</h5>
        </div>
        @if($sarsRepRequest->sarsRepAuditLogs && $sarsRepRequest->sarsRepAuditLogs->count() > 0)
            <div class="table-responsive">
                <table class="table audit-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sarsRepRequest->sarsRepAuditLogs as $audit)
                            <tr>
                                <td>{{ $audit->created_at ? $audit->created_at->format('d M Y H:i') : '-' }}</td>
                                <td>{{ $audit->user->name ?? $audit->user_name ?? 'System' }}</td>
                                <td><span class="badge bg-info">{{ ucwords(str_replace('_', ' ', $audit->action ?? '')) }}</span></td>
                                <td>{{ $audit->description ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0"><i class="fa fa-info-circle me-1"></i> No audit records available.</p>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="/public/smartdash/vendor/sweetalert2/sweetalert2.min.js"></script>
<script>
// ============================================
// CSRF Token Setup for all AJAX requests
// ============================================
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// ============================================
// Upload Document
// ============================================
function uploadDocument(docTypeKey, docLabel) {
    var fileInput = document.getElementById('file_' + docTypeKey);
    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No File Selected',
            text: 'Please select a file to upload for "' + docLabel + '".',
            confirmButtonColor: '#17A2B8'
        });
        return;
    }

    var file = fileInput.files[0];

    // Validate file type
    var allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
    if (allowedTypes.indexOf(file.type) === -1) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid File Type',
            text: 'Only PDF, JPG, JPEG, and PNG files are accepted.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }

    // Validate file size (max 10MB)
    if (file.size > 10 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'File Too Large',
            text: 'Maximum file size is 10MB.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }

    var formData = new FormData();
    formData.append('file', file);
    formData.append('document_type', docTypeKey);

    Swal.fire({
        title: 'Uploading...',
        text: 'Uploading "' + docLabel + '", please wait.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: function() {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '{{ route("sarsrep.upload", $sarsRepRequest->id) }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Uploaded',
                text: '"' + docLabel + '" has been uploaded successfully.',
                confirmButtonColor: '#28a745',
                timer: 2000,
                timerProgressBar: true
            }).then(function() {
                location.reload();
            });
        },
        error: function(xhr) {
            var errorMsg = 'An error occurred while uploading the document.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Upload Failed',
                text: errorMsg,
                confirmButtonColor: '#dc3545'
            });
        }
    });
}

// ============================================
// Delete Document
// ============================================
function deleteDocument(docId, docLabel) {
    Swal.fire({
        title: 'Delete Document?',
        text: 'Are you sure you want to delete "' + docLabel + '"? This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then(function(result) {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleting...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '/cims/sarsrep/{{ $sarsRepRequest->id }}/document/' + docId,
                type: 'DELETE',
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted',
                        text: 'Document has been deleted successfully.',
                        confirmButtonColor: '#28a745',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(function() {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    var errorMsg = 'An error occurred while deleting the document.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Delete Failed',
                        text: errorMsg,
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}

// ============================================
// Generate Document (Cover Letter, Mandate, Resolution)
// ============================================
function generateDocument(docType) {
    var labels = {
        'cover_letter': 'Cover Letter',
        'mandate': 'SARS Mandate',
        'resolution': 'Resolution'
    };
    var label = labels[docType] || docType;

    Swal.fire({
        title: 'Generate ' + label + '?',
        text: 'This will generate the ' + label + ' document based on the current request data.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#17A2B8',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, generate it!',
        cancelButtonText: 'Cancel'
    }).then(function(result) {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Generating...',
                text: 'Generating ' + label + ', please wait.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '/cims/sarsrep/{{ $sarsRepRequest->id }}/generate/' + docType,
                type: 'POST',
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Generated',
                        text: label + ' has been generated successfully.',
                        confirmButtonColor: '#28a745',
                        timer: 2500,
                        timerProgressBar: true
                    }).then(function() {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    var errorMsg = 'An error occurred while generating the document.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Generation Failed',
                        text: errorMsg,
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}

// ============================================
// Generate Final PDF Bundle
// ============================================
function generateBundle() {
    Swal.fire({
        title: 'Generate Final PDF Bundle?',
        text: 'This will compile all documents into a single indexed PDF file.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#db2777',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, generate bundle!',
        cancelButtonText: 'Cancel'
    }).then(function(result) {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Generating Bundle...',
                text: 'Compiling all documents, please wait.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route("sarsrep.generateBundle", $sarsRepRequest->id) }}',
                type: 'POST',
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Bundle Generated',
                        text: 'The final indexed PDF has been generated successfully.',
                        confirmButtonColor: '#28a745',
                        timer: 2500,
                        timerProgressBar: true
                    }).then(function() {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    var errorMsg = 'An error occurred while generating the bundle.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Bundle Generation Failed',
                        text: errorMsg,
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}

// ============================================
// Update Status
// ============================================
function updateStatus() {
    var newStatus = document.getElementById('new_status').value;
    if (!newStatus) {
        Swal.fire({
            icon: 'warning',
            title: 'No Status Selected',
            text: 'Please select a new status from the dropdown.',
            confirmButtonColor: '#17A2B8'
        });
        return;
    }

    var statusLabels = {
        'draft': 'Draft',
        'awaiting_documents': 'Awaiting Documents',
        'ready_for_review': 'Ready for Review',
        'ready_for_submission': 'Ready for Submission',
        'submitted_branch': 'Submitted (Branch)',
        'submitted_efiling': 'Submitted (eFiling)',
        'approved': 'Approved',
        'rejected': 'Rejected'
    };
    var label = statusLabels[newStatus] || newStatus;

    Swal.fire({
        title: 'Update Status?',
        text: 'Change status to "' + label + '"?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#17A2B8',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'Cancel'
    }).then(function(result) {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Updating...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route("sarsrep.updateStatus", $sarsRepRequest->id) }}',
                type: 'PUT',
                data: {
                    status: newStatus
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Updated',
                        text: 'Status has been changed to "' + label + '".',
                        confirmButtonColor: '#28a745',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(function() {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    var errorMsg = 'An error occurred while updating the status.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: errorMsg,
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}

// ============================================
// Flash Messages on Page Load
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
            confirmButtonText: 'OK',
            confirmButtonColor: '#28a745',
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: '<div style="font-size: 16px;">{!! addslashes(session('error')) !!}</div>',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545',
            allowOutsideClick: false,
            allowEscapeKey: false
        });
    @endif
});
</script>
@endpush
