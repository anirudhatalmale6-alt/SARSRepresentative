@extends('smartdash::layouts.default')

@section('title', 'SARS Representative')

@push('styles')
<link href="/public/smartdash/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<link href="/public/smartdash/css/smartdash-forms.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* Stats Cards */
.stats-row { margin-bottom: 28px; }
.stat-card {
    border-radius: 12px;
    padding: 20px;
    color: #fff;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    min-height: 120px;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
}
.stat-card.total { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-card.draft { background: linear-gradient(135deg, #6c757d 0%, #495057 100%); }
.stat-card.awaiting { background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%); }
.stat-card.submitted { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.stat-card.approved { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.stat-card.rejected { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }
.stat-card .stat-label {
    font-size: 13px;
    font-weight: 500;
    opacity: 0.9;
    margin-bottom: 8px;
}
.stat-card .stat-number {
    font-size: 32px;
    font-weight: 700;
    margin: 0;
    line-height: 1.1;
}
.stat-card .stat-icon {
    position: absolute;
    right: 18px;
    bottom: 15px;
    font-size: 50px;
    opacity: 0.3;
}

/* Table Styling */
.sarsrep-table {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07);
}
.sarsrep-table thead {
    background: linear-gradient(135deg, #0d3d56 0%, #17A2B8 100%);
}
.sarsrep-table thead th {
    color: #fff !important;
    font-weight: 600;
    padding: 14px 16px;
    border: none;
    font-size: 14px;
    white-space: nowrap;
}
.sarsrep-table tbody td {
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
    vertical-align: middle;
}
.sarsrep-table tbody tr:hover {
    background-color: #f8fafc;
}

/* Status Badges */
.status-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 12px;
    text-transform: capitalize;
    white-space: nowrap;
}

/* Entity Type Badges */
.entity-type-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
    color: #4f46e5;
}

/* Progress Bar */
.completion-bar {
    height: 8px;
    border-radius: 4px;
    background: #e2e8f0;
    overflow: hidden;
    min-width: 80px;
}
.completion-bar .bar-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s ease;
}
.completion-text {
    font-size: 12px;
    font-weight: 600;
    margin-top: 3px;
}

/* Action Buttons */
.btn-action {
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 13px;
    border: none;
    transition: all 0.2s ease;
}
.btn-action:hover {
    transform: translateY(-1px);
}
.btn-action-view {
    background: linear-gradient(135deg, #17A2B8, #138496);
    color: #fff;
}
.btn-action-view:hover { color: #fff; box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3); }
.btn-action-edit {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #fff;
}
.btn-action-edit:hover { color: #fff; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
.btn-action-delete {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #fff;
}
.btn-action-delete:hover { color: #fff; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }

/* New Registration Button */
.btn-new-reg {
    background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
    border: none;
    color: #fff;
    padding: 12px 24px;
    font-weight: 700;
    border-radius: 12px;
    font-size: 15px;
    letter-spacing: 0.3px;
    box-shadow: 0 4px 14px rgba(13, 148, 136, 0.4);
    transition: all 0.3s ease;
}
.btn-new-reg:hover {
    background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(13, 148, 136, 0.5);
    color: #fff;
}

/* Empty State */
.empty-state { text-align: center; padding: 60px 20px; }
.empty-state i { font-size: 60px; color: #17A2B8; margin-bottom: 20px; display: block; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Title / Breadcrumb -->
    <div class="row page-titles">
        <div class="d-flex align-items-center justify-content-between">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="fs-2" style="color:#000" href="javascript:void(0)">CIMS</a></li>
                <li class="breadcrumb-item"><a class="fs-2" style="color:#17A2B8" href="{{ route('sarsrep.index') }}">SARS Representative</a></li>
                <li class="breadcrumb-item active"><a class="fs-2" style="color:#009688" href="javascript:void(0)">List</a></li>
            </ol>
            <a href="{{ route('sarsrep.create') }}" class="btn btn-new-reg">
                <i class="fa fa-plus me-2"></i> New Registration
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    @php
        $total      = $stats['total'] ?? 0;
        $draft      = $stats['draft'] ?? 0;
        $awaiting   = $stats['awaiting'] ?? 0;
        $submitted  = $stats['submitted'] ?? 0;
        $approved   = $stats['approved'] ?? 0;
        $rejected   = $stats['rejected'] ?? 0;
    @endphp
    <div class="row stats-row">
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
            <div class="stat-card total">
                <div class="stat-label">Total Requests</div>
                <div class="stat-number">{{ $total }}</div>
                <i class="fa fa-folder-open stat-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
            <div class="stat-card draft">
                <div class="stat-label">Draft</div>
                <div class="stat-number">{{ $draft }}</div>
                <i class="fa fa-pencil stat-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
            <div class="stat-card awaiting">
                <div class="stat-label">Awaiting Docs</div>
                <div class="stat-number">{{ $awaiting }}</div>
                <i class="fa fa-clock stat-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
            <div class="stat-card submitted">
                <div class="stat-label">Submitted</div>
                <div class="stat-number">{{ $submitted }}</div>
                <i class="fa fa-paper-plane stat-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
            <div class="stat-card approved">
                <div class="stat-label">Approved</div>
                <div class="stat-number">{{ $approved }}</div>
                <i class="fa fa-check-circle stat-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
            <div class="stat-card rejected">
                <div class="stat-label">Rejected</div>
                <div class="stat-number">{{ $rejected }}</div>
                <i class="fa fa-times-circle stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="row">
        <div class="col-12">
            <div class="card smartdash-form-card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-user-shield"></i>
                        SARS Representative Registration Requests
                    </h4>
                </div>
                <div class="card-body">
                    @if($requests->count() > 0)
                        <div class="table-responsive">
                            <table class="table sarsrep-table" id="sarsRepTable">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Entity Name</th>
                                        <th>Entity Type</th>
                                        <th>Representative</th>
                                        <th>Status</th>
                                        <th style="width: 140px;">Completion</th>
                                        <th>Created</th>
                                        <th style="width: 160px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $index => $request)
                                        @php
                                            // Determine status badge color
                                            $statusColors = [
                                                'draft'               => 'secondary',
                                                'awaiting_documents'  => 'warning',
                                                'ready_for_review'    => 'info',
                                                'ready_for_submission'=> 'primary',
                                                'submitted_branch'    => 'primary',
                                                'submitted_efiling'   => 'primary',
                                                'approved'            => 'success',
                                                'rejected'            => 'danger',
                                            ];
                                            $badgeColor = $statusColors[$request->status] ?? 'secondary';
                                            $statusLabel = ucwords(str_replace('_', ' ', $request->status));

                                            // Completion percentage
                                            $pct = $request->getCompletionPercentage();
                                            if ($pct >= 100) {
                                                $barColor = '#10b981';
                                            } elseif ($pct >= 60) {
                                                $barColor = '#3b82f6';
                                            } elseif ($pct >= 30) {
                                                $barColor = '#f59e0b';
                                            } else {
                                                $barColor = '#ef4444';
                                            }

                                            // Representative name
                                            $repName = $request->sarsRepresentative
                                                ? $request->sarsRepresentative->full_name
                                                : 'N/A';
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $request->entity_name }}</strong>
                                            </td>
                                            <td>
                                                <span class="entity-type-badge">{{ $request->entity_type ?? 'N/A' }}</span>
                                            </td>
                                            <td>{{ $repName }}</td>
                                            <td>
                                                <span class="badge bg-{{ $badgeColor }} status-badge">{{ $statusLabel }}</span>
                                            </td>
                                            <td>
                                                <div class="completion-bar">
                                                    <div class="bar-fill" style="width: {{ $pct }}%; background: {{ $barColor }};"></div>
                                                </div>
                                                <div class="completion-text" style="color: {{ $barColor }};">{{ $pct }}%</div>
                                            </td>
                                            <td>
                                                {{ $request->created_at ? $request->created_at->format('d M Y') : '-' }}
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('sarsrep.show', $request->id) }}" class="btn btn-action btn-action-view" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('sarsrep.edit', $request->id) }}" class="btn btn-action btn-action-edit" title="Edit">
                                                        <i class="fa fa-pen"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-action btn-action-delete" title="Delete" onclick="confirmDelete({{ $request->id }})">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $request->id }}" action="{{ route('sarsrep.destroy', $request->id) }}" method="POST" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fa fa-user-shield"></i>
                            <h5>No registration requests yet</h5>
                            <p class="text-muted">Create your first SARS Representative registration request to get started.</p>
                            <a href="{{ route('sarsrep.create') }}" class="btn btn-new-reg">
                                <i class="fa fa-plus me-2"></i> New Registration
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/public/smartdash/vendor/sweetalert2/sweetalert2.min.js"></script>
<script>
// CSRF Token Setup
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// Initialize DataTables if available
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#sarsRepTable').DataTable({
            order: [[6, 'desc']],
            pageLength: 25,
            language: {
                search: "Search requests:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ requests",
                emptyTable: "No registration requests found"
            },
            columnDefs: [
                { orderable: false, targets: [7] }
            ]
        });
    }
});

// Delete Confirmation
function confirmDelete(id) {
    Swal.fire({
        title: 'Delete Registration Request?',
        text: "This action cannot be undone. Are you sure you want to delete this request?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

// SweetAlert2 Flash Messages
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
