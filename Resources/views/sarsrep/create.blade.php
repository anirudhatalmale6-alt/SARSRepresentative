@extends('smartdash::layouts.default')

@section('title', isset($sarsRepRequest) ? 'Edit SARS Representative Registration' : 'New SARS Representative Registration')

@push('styles')
<link href="/public/smartdash/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .tax-types-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1.25rem;
        padding: 0.75rem 0;
    }
    .tax-types-row .form-check {
        min-width: 130px;
    }
    .tax-types-row .form-check-label {
        font-weight: 500;
        cursor: pointer;
    }
    .tax-types-row .form-check-input {
        cursor: pointer;
    }
    .form-section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #4E3F6B;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
        margin-top: 1.5rem;
    }
    .form-section-title i {
        margin-right: 0.5rem;
        color: #886CC0;
    }
    .form-section-title:first-of-type {
        margin-top: 0;
    }
</style>
@endpush

@php
    $isEdit = isset($sarsRepRequest);
    $rep = $isEdit ? $sarsRepRequest->sarsRepresentative : null;
    $currentTaxTypes = old('tax_types', $isEdit ? ($sarsRepRequest->tax_types ?? []) : []);
    if (!is_array($currentTaxTypes)) {
        $currentTaxTypes = [];
    }
@endphp

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row page-titles">
        <div class="d-flex align-items-center justify-content-between">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="fs-2" style="color:#000" href="javascript:void(0)">CIMS</a></li>
                <li class="breadcrumb-item"><a class="fs-2" style="color:#17A2B8" href="/cims/sarsrep">SARS Representative</a></li>
                <li class="breadcrumb-item active"><a class="fs-2" style="color:#009688" href="javascript:void(0)">{{ $isEdit ? 'Edit Registration' : 'New Registration' }}</a></li>
            </ol>
            <a href="/cims/sarsrep" class="btn btn-outline-secondary btn-lg">
                <i class="fa fa-list"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card smartdash-form-card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-user-shield"></i>
                        {{ $isEdit ? 'Edit SARS Representative Registration: ' . $sarsRepRequest->entity_name : 'New SARS Representative Registration' }}
                    </h4>
                </div>
                <div class="card-body">
                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ $isEdit ? '/cims/sarsrep/' . $sarsRepRequest->id : '/cims/sarsrep' }}" method="POST" id="sarsrep_form">
                        @csrf
                        @if($isEdit)
                            @method('PUT')
                        @endif

                        {{-- ============================================================
                             SECTION 1: ENTITY DETAILS
                        ============================================================= --}}
                        <div class="form-section-title">
                            <i class="fa fa-building"></i> Entity Details
                        </div>

                        {{-- Row 1: Entity Name, Reg Number, Entity Type --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="entity_name" class="form-label">Entity Name <span class="text-danger">*</span></label>
                                    <input type="text" id="entity_name" name="entity_name" class="form-control @error('entity_name') is-invalid @enderror" value="{{ old('entity_name', $sarsRepRequest->entity_name ?? '') }}" required>
                                    @error('entity_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="entity_reg_number" class="form-label">Entity Registration Number</label>
                                    <input type="text" id="entity_reg_number" name="entity_reg_number" class="form-control @error('entity_reg_number') is-invalid @enderror" value="{{ old('entity_reg_number', $sarsRepRequest->entity_reg_number ?? '') }}" placeholder="YYYY/NNNNNN/NN">
                                    @error('entity_reg_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="entity_type" class="form-label">Entity Type</label>
                                    <select id="entity_type" name="entity_type" class="form-control sd_drop_class @error('entity_type') is-invalid @enderror">
                                        <option value="">-- Select Entity Type --</option>
                                        <option value="company" {{ old('entity_type', $sarsRepRequest->entity_type ?? '') == 'company' ? 'selected' : '' }}>Company</option>
                                        <option value="trust" {{ old('entity_type', $sarsRepRequest->entity_type ?? '') == 'trust' ? 'selected' : '' }}>Trust</option>
                                        <option value="npc" {{ old('entity_type', $sarsRepRequest->entity_type ?? '') == 'npc' ? 'selected' : '' }}>NPC (Non-Profit Company)</option>
                                        <option value="npo" {{ old('entity_type', $sarsRepRequest->entity_type ?? '') == 'npo' ? 'selected' : '' }}>NPO (Non-Profit Organisation)</option>
                                        <option value="sole_director_company" {{ old('entity_type', $sarsRepRequest->entity_type ?? '') == 'sole_director_company' ? 'selected' : '' }}>Sole Director Company</option>
                                        <option value="sole_trustee_trust" {{ old('entity_type', $sarsRepRequest->entity_type ?? '') == 'sole_trustee_trust' ? 'selected' : '' }}>Sole Trustee Trust</option>
                                    </select>
                                    @error('entity_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- Row 2: Entity Registered Address --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="entity_address" class="form-label">Entity Registered Address</label>
                                    <textarea id="entity_address" name="entity_address" class="form-control @error('entity_address') is-invalid @enderror" rows="2">{{ old('entity_address', $sarsRepRequest->entity_address ?? '') }}</textarea>
                                    @error('entity_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- Row 3: Tax Reference Numbers --}}
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="income_tax_ref" class="form-label">Income Tax Ref</label>
                                    <input type="text" id="income_tax_ref" name="income_tax_ref" class="form-control @error('income_tax_ref') is-invalid @enderror" value="{{ old('income_tax_ref', $sarsRepRequest->income_tax_ref ?? '') }}">
                                    @error('income_tax_ref')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="paye_ref" class="form-label">PAYE Ref</label>
                                    <input type="text" id="paye_ref" name="paye_ref" class="form-control @error('paye_ref') is-invalid @enderror" value="{{ old('paye_ref', $sarsRepRequest->paye_ref ?? '') }}">
                                    @error('paye_ref')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="vat_ref" class="form-label">VAT Ref</label>
                                    <input type="text" id="vat_ref" name="vat_ref" class="form-control @error('vat_ref') is-invalid @enderror" value="{{ old('vat_ref', $sarsRepRequest->vat_ref ?? '') }}">
                                    @error('vat_ref')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="uif_sdl_ref" class="form-label">UIF/SDL Ref</label>
                                    <input type="text" id="uif_sdl_ref" name="uif_sdl_ref" class="form-control @error('uif_sdl_ref') is-invalid @enderror" value="{{ old('uif_sdl_ref', $sarsRepRequest->uif_sdl_ref ?? '') }}">
                                    @error('uif_sdl_ref')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- Row 4: Tax Types Checkboxes --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Tax Types to Register</label>
                                    <div class="tax-types-row">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="tax_type_income_tax" name="tax_types[]" value="income_tax" {{ in_array('income_tax', $currentTaxTypes) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tax_type_income_tax">Income Tax</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="tax_type_paye" name="tax_types[]" value="paye" {{ in_array('paye', $currentTaxTypes) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tax_type_paye">PAYE</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="tax_type_vat" name="tax_types[]" value="vat" {{ in_array('vat', $currentTaxTypes) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tax_type_vat">VAT</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="tax_type_uif" name="tax_types[]" value="uif" {{ in_array('uif', $currentTaxTypes) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tax_type_uif">UIF</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="tax_type_sdl" name="tax_types[]" value="sdl" {{ in_array('sdl', $currentTaxTypes) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tax_type_sdl">SDL</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="tax_type_customs" name="tax_types[]" value="customs" {{ in_array('customs', $currentTaxTypes) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tax_type_customs">Customs & Excise</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================
                             SECTION 2: SARS REPRESENTATIVE DETAILS
                        ============================================================= --}}
                        <div class="form-section-title">
                            <i class="fa fa-user-shield"></i> SARS Representative Details
                        </div>

                        {{-- Row 1: Full Name, ID Number, Passport Number --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name (as per ID) <span class="text-danger">*</span></label>
                                    <input type="text" id="full_name" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name', $rep->full_name ?? '') }}" required>
                                    @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="id_number" class="form-label">ID Number</label>
                                    <input type="text" id="id_number" name="id_number" class="form-control @error('id_number') is-invalid @enderror" value="{{ old('id_number', $rep->id_number ?? '') }}" maxlength="13">
                                    @error('id_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="passport_number" class="form-label">Passport Number <small class="text-muted">(foreign nationals)</small></label>
                                    <input type="text" id="passport_number" name="passport_number" class="form-control @error('passport_number') is-invalid @enderror" value="{{ old('passport_number', $rep->passport_number ?? '') }}">
                                    @error('passport_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- Row 2: Capacity, Email, Cell, Submission Method --}}
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="capacity" class="form-label">Capacity</label>
                                    <select id="capacity" name="capacity" class="form-control sd_drop_class @error('capacity') is-invalid @enderror">
                                        <option value="">-- Select Capacity --</option>
                                        <option value="director" {{ old('capacity', $rep->capacity ?? '') == 'director' ? 'selected' : '' }}>Director</option>
                                        <option value="sole_director" {{ old('capacity', $rep->capacity ?? '') == 'sole_director' ? 'selected' : '' }}>Sole Director</option>
                                        <option value="trustee" {{ old('capacity', $rep->capacity ?? '') == 'trustee' ? 'selected' : '' }}>Trustee</option>
                                        <option value="sole_trustee" {{ old('capacity', $rep->capacity ?? '') == 'sole_trustee' ? 'selected' : '' }}>Sole Trustee</option>
                                        <option value="accounting_officer" {{ old('capacity', $rep->capacity ?? '') == 'accounting_officer' ? 'selected' : '' }}>Accounting Officer</option>
                                        <option value="chairperson" {{ old('capacity', $rep->capacity ?? '') == 'chairperson' ? 'selected' : '' }}>Chairperson</option>
                                        <option value="treasurer" {{ old('capacity', $rep->capacity ?? '') == 'treasurer' ? 'selected' : '' }}>Treasurer</option>
                                        <option value="accountant" {{ old('capacity', $rep->capacity ?? '') == 'accountant' ? 'selected' : '' }}>Accountant</option>
                                        <option value="authorised_representative" {{ old('capacity', $rep->capacity ?? '') == 'authorised_representative' ? 'selected' : '' }}>Authorised Representative</option>
                                    </select>
                                    @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $rep->email ?? '') }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="mobile" class="form-label">Cell Number <span class="text-danger">*</span></label>
                                    <input type="tel" id="mobile" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $rep->mobile ?? '') }}" required>
                                    @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="submission_method" class="form-label">Submission Method</label>
                                    <select id="submission_method" name="submission_method" class="form-control sd_drop_class @error('submission_method') is-invalid @enderror">
                                        <option value="">-- Select Method --</option>
                                        <option value="branch" {{ old('submission_method', $sarsRepRequest->submission_method ?? '') == 'branch' ? 'selected' : '' }}>SARS Branch</option>
                                        <option value="efiling" {{ old('submission_method', $sarsRepRequest->submission_method ?? '') == 'efiling' ? 'selected' : '' }}>SARS eFiling</option>
                                        <option value="both" {{ old('submission_method', $sarsRepRequest->submission_method ?? '') == 'both' ? 'selected' : '' }}>Both</option>
                                    </select>
                                    @error('submission_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================
                             SECTION 3: NOTES
                        ============================================================= --}}
                        <div class="form-section-title">
                            <i class="fa fa-sticky-note"></i> Notes
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes <small class="text-muted">(optional)</small></label>
                                    <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $sarsRepRequest->notes ?? '') }}</textarea>
                                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================
                             SUBMIT BUTTONS
                        ============================================================= --}}
                        <div class="mt-4 mb-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-save me-1"></i> Save & Continue to Documents
                            </button>
                            <a href="/cims/sarsrep" class="btn btn-outline-secondary btn-lg">
                                <i class="fa fa-times me-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/public/smartdash/vendor/sweetalert2/sweetalert2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Initialize bootstrap-select for sd_drop_class dropdowns
    if (typeof $.fn.selectpicker !== 'undefined') {
        $('.sd_drop_class').selectpicker({
            style: '',
            styleBase: 'form-control',
            tickIcon: 'fa fa-check'
        });
    }

    // SweetAlert2 flash messages
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
});
</script>
@endpush
