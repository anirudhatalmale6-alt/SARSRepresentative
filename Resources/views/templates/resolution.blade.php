<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resolution - {{ $sarsRepRequest->entity_name }}</title>
    <style>
        @page {
            size: A4;
            margin: 20mm 20mm 25mm 20mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
            margin: 40px;
        }
        h1 {
            font-size: 16pt;
            text-align: center;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        h2 {
            font-size: 13pt;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }
        .subtitle {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .entity-name-line {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .reg-number-line {
            text-align: center;
            font-size: 10pt;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.details-table td {
            padding: 4px 8px;
            vertical-align: top;
            border: 1px solid #ccc;
        }
        table.details-table td.label {
            width: 35%;
            font-weight: bold;
            background-color: #f5f5f5;
        }
        table.details-table td.value {
            width: 65%;
        }
        .section-body {
            margin-bottom: 15px;
            text-align: justify;
        }
        .section-body p {
            margin-bottom: 8px;
        }
        .tax-type-row {
            display: block;
            margin-bottom: 4px;
            font-size: 11pt;
        }
        .checkbox {
            font-size: 13pt;
            margin-right: 6px;
        }
        .powers-list {
            margin: 10px 0 15px 20px;
            padding: 0;
        }
        .powers-list li {
            margin-bottom: 4px;
        }
        .signature-block {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            width: 280px;
            margin-top: 35px;
            margin-bottom: 3px;
        }
        .signature-label {
            font-size: 10pt;
            color: #333;
        }
        .preamble {
            margin-bottom: 15px;
            text-align: justify;
        }
        .attachments-list {
            margin: 10px 0 15px 20px;
            padding: 0;
        }
        .attachments-list li {
            margin-bottom: 4px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    @php
        $allTaxTypes = [
            'income_tax'       => 'Income Tax',
            'provisional_tax'  => 'Provisional Tax',
            'vat'              => 'Value-Added Tax (VAT)',
            'paye'             => 'Pay-As-You-Earn (PAYE)',
            'uif'              => 'Unemployment Insurance Fund (UIF)',
            'sdl'              => 'Skills Development Levy (SDL)',
            'dividends_tax'    => 'Dividends Tax',
            'withholding_tax'  => 'Withholding Tax',
            'turnover_tax'     => 'Turnover Tax',
            'customs_excise'   => 'Customs and Excise',
            'transfer_duty'    => 'Transfer Duty',
            'estate_duty'      => 'Estate Duty',
            'donations_tax'    => 'Donations Tax',
        ];
        $selectedTypes = is_array($sarsRepRequest->tax_types) ? $sarsRepRequest->tax_types : [];
        $capacityLabel = \Modules\SARSRepresentative\Models\SarsRepresentative::getCapacityLabel($sarsRepRequest->sarsRepresentative->capacity);
        $repName = $sarsRepRequest->sarsRepresentative->full_name;
        $repId = $sarsRepRequest->sarsRepresentative->id_number ?? $sarsRepRequest->sarsRepresentative->passport_number ?? '—';
        $entityName = $sarsRepRequest->entity_name;
        $entityReg = $sarsRepRequest->entity_reg_number ?? '—';
        $currentDate = $sarsRepRequest->submitted_at ? $sarsRepRequest->submitted_at->format('d F Y') : now()->format('d F Y');
        $numDirectors = $numberOfDirectors ?? 2;
    @endphp

    {{-- ============================================================== --}}
    {{-- COMPANY: DIRECTORS' RESOLUTION                                  --}}
    {{-- ============================================================== --}}
    @if($sarsRepRequest->entity_type === 'company')

        <h1>Directors' Resolution</h1>
        <div class="subtitle">of</div>
        <div class="entity-name-line">{{ $entityName }}</div>
        <div class="reg-number-line">(Registration No: {{ $entityReg }})</div>

        <div class="preamble">
            <p>
                A resolution passed by the Board of Directors of <strong>{{ $entityName }}</strong>
                (Registration No: <strong>{{ $entityReg }}</strong>), a company duly incorporated
                in accordance with the laws of the Republic of South Africa (hereinafter referred
                to as "the Company"), at a duly convened meeting of the Board of Directors held on
                <strong>{{ $currentDate }}</strong>, at which a quorum was present, it was resolved that:
            </p>
        </div>

        {{-- 1. APPOINTMENT --}}
        <h2>1. Appointment of SARS Representative</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> <strong>{{ $repName }}</strong>
                (ID/Passport No: <strong>{{ $repId }}</strong>), acting in the capacity of
                <strong>{{ $capacityLabel }}</strong>, be and is hereby appointed as the registered
                SARS Representative of the Company, for purposes of the Tax Administration Act,
                No. 28 of 2011, and all related tax legislation.
            </p>
        </div>

        {{-- 2. AUTHORITY GRANTED --}}
        <h2>2. Authority Granted</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> the appointed SARS Representative be authorised
                to act on behalf of the Company in all dealings with the South African Revenue
                Service in respect of the following tax types:
            </p>
            <div style="margin: 10px 0 15px 0;">
                @foreach($allTaxTypes as $key => $label)
                    <span class="tax-type-row">
                        <span class="checkbox">{{ in_array($key, $selectedTypes) ? '☑' : '☐' }}</span>
                        {{ $label }}
                    </span><br>
                @endforeach
            </div>
            <p>The appointed SARS Representative is specifically authorised to:</p>
            <ol class="powers-list">
                <li>Register, amend, or deregister tax types with SARS on behalf of the Company;</li>
                <li>Submit tax returns, declarations, and supporting documentation;</li>
                <li>Make tax payments and request allocation of payments;</li>
                <li>Request, receive, and process tax refunds;</li>
                <li>Lodge objections and appeals against assessments;</li>
                <li>Apply for tax directives, tax clearance certificates, and compliance status;</li>
                <li>Access, manage, and operate the Company's SARS eFiling profile and any other SARS electronic systems;</li>
                <li>Correspond and communicate with SARS on all matters affecting the Company's tax affairs;</li>
                <li>Attend to any administrative or procedural matters with SARS, including visits to SARS branch offices.</li>
            </ol>
        </div>

        {{-- 3. EXECUTION OF DOCUMENTS --}}
        <h2>3. Execution of Documents</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> the appointed SARS Representative be authorised
                to sign and execute all documents, forms, mandates, and correspondence as may be
                required by the South African Revenue Service to give effect to this resolution,
                including but not limited to the SARS Mandate / Power of Attorney and the SARS
                Branch Cover Letter.
            </p>
        </div>

        {{-- 4. EFFECTIVE DATE --}}
        <h2>4. Effective Date</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> this resolution shall take effect from the date
                of adoption hereof and shall remain in force until revoked by a subsequent
                resolution of the Board of Directors.
            </p>
        </div>

        {{-- 5. RATIFICATION --}}
        <h2>5. Ratification</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> any and all actions previously taken by any
                director or authorised person on behalf of the Company in connection with the
                appointment and registration of a SARS Representative be and are hereby ratified
                and confirmed in all respects.
            </p>
        </div>

        {{-- 6. SIGNATURES --}}
        <h2>6. Signatures of Directors</h2>
        <div class="signature-block">
            <p style="margin-bottom: 5px;">
                The undersigned directors hereby confirm the adoption of this resolution:
            </p>

            <table style="width: 100%; border: none;">
                @for($i = 0; $i < $numDirectors; $i++)
                    @if($i % 2 == 0)
                        <tr>
                    @endif
                    <td style="width: 50%; border: none; padding: 0 {{ $i % 2 == 0 ? '10px 15px 0' : '0 15px 10px' }};">
                        <div class="signature-line" style="width: 100%;"></div>
                        <div class="signature-label">Director {{ $i + 1 }} - Signature</div>
                        <div class="signature-line" style="width: 100%;"></div>
                        <div class="signature-label">Full Name</div>
                        <div class="signature-line" style="width: 100%;"></div>
                        <div class="signature-label">Date</div>
                    </td>
                    @if($i % 2 == 1 || $i == $numDirectors - 1)
                        @if($i % 2 == 0)
                            <td style="width: 50%; border: none;">&nbsp;</td>
                        @endif
                        </tr>
                    @endif
                @endfor
            </table>
        </div>

        {{-- 7. MANDATORY ATTACHMENTS --}}
        <h2>7. Mandatory Attachments</h2>
        <div class="section-body">
            <p>The following documents must accompany this resolution:</p>
            <ol class="attachments-list">
                <li>Certified copy of the Representative's Identity Document or Passport</li>
                <li>Proof of residential address of the Representative (not older than 3 months)</li>
                <li>Passport-size photograph of the Representative</li>
                <li>Company registration documents (COR 14.3 / CoR 39 or CIPC confirmation)</li>
                <li>Mandate to Act as SARS Representative (Power of Attorney)</li>
                <li>SARS Branch Cover Letter</li>
            </ol>
        </div>

    {{-- ============================================================== --}}
    {{-- SOLE DIRECTOR COMPANY: SOLE DIRECTOR RESOLUTION                --}}
    {{-- ============================================================== --}}
    @elseif($sarsRepRequest->entity_type === 'sole_director_company')

        <h1>Sole Director Resolution</h1>
        <div class="subtitle">of</div>
        <div class="entity-name-line">{{ $entityName }}</div>
        <div class="reg-number-line">(Registration No: {{ $entityReg }})</div>

        <div class="preamble">
            <p>
                I, the undersigned, being the sole director of <strong>{{ $entityName }}</strong>
                (Registration No: <strong>{{ $entityReg }}</strong>), a company duly incorporated
                in accordance with the Companies Act, No. 71 of 2008, and the laws of the Republic
                of South Africa, do hereby resolve as follows:
            </p>
        </div>

        {{-- 1. APPOINTMENT --}}
        <h2>1. Appointment of SARS Representative</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> <strong>{{ $repName }}</strong>
                (ID/Passport No: <strong>{{ $repId }}</strong>), acting in the capacity of
                <strong>{{ $capacityLabel }}</strong>, be and is hereby appointed as the registered
                SARS Representative of the Company, for purposes of the Tax Administration Act,
                No. 28 of 2011, and all related tax legislation.
            </p>
        </div>

        {{-- 2. AUTHORITY GRANTED --}}
        <h2>2. Authority Granted</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> the appointed SARS Representative be authorised
                to act on behalf of the Company in all dealings with the South African Revenue
                Service in respect of the following tax types:
            </p>
            <div style="margin: 10px 0 15px 0;">
                @foreach($allTaxTypes as $key => $label)
                    <span class="tax-type-row">
                        <span class="checkbox">{{ in_array($key, $selectedTypes) ? '☑' : '☐' }}</span>
                        {{ $label }}
                    </span><br>
                @endforeach
            </div>
            <p>The appointed SARS Representative is specifically authorised to:</p>
            <ol class="powers-list">
                <li>Register, amend, or deregister tax types with SARS on behalf of the Company;</li>
                <li>Submit tax returns, declarations, and supporting documentation;</li>
                <li>Make tax payments and request allocation of payments;</li>
                <li>Request, receive, and process tax refunds;</li>
                <li>Lodge objections and appeals against assessments;</li>
                <li>Apply for tax directives, tax clearance certificates, and compliance status;</li>
                <li>Access, manage, and operate the Company's SARS eFiling profile and any other SARS electronic systems;</li>
                <li>Correspond and communicate with SARS on all matters affecting the Company's tax affairs;</li>
                <li>Attend to any administrative or procedural matters with SARS, including visits to SARS branch offices.</li>
            </ol>
        </div>

        {{-- 3. EXECUTION OF DOCUMENTS --}}
        <h2>3. Execution of Documents</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> the appointed SARS Representative be authorised
                to sign and execute all documents, forms, mandates, and correspondence as may be
                required by the South African Revenue Service to give effect to this resolution.
            </p>
        </div>

        {{-- 4. EFFECTIVE DATE --}}
        <h2>4. Effective Date</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> this resolution shall take effect from the date
                of adoption hereof and shall remain in force until revoked in writing by the sole
                director.
            </p>
        </div>

        {{-- 5. RATIFICATION --}}
        <h2>5. Ratification</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> any and all actions previously taken in connection
                with the appointment and registration of a SARS Representative on behalf of the
                Company be and are hereby ratified and confirmed in all respects.
            </p>
        </div>

        {{-- 6. SIGNATURE --}}
        <h2>6. Signature of Sole Director</h2>
        <div class="signature-block">
            <div class="signature-line"></div>
            <div class="signature-label">Signature of Sole Director</div>

            <div style="margin-top: 15px;">
                <div class="signature-line"></div>
                <div class="signature-label">Full Name</div>
            </div>

            <div style="margin-top: 15px;">
                <div class="signature-line"></div>
                <div class="signature-label">ID / Passport Number</div>
            </div>

            <div style="margin-top: 15px;">
                <div class="signature-line"></div>
                <div class="signature-label">Date</div>
            </div>

            <div style="margin-top: 15px;">
                <div class="signature-line"></div>
                <div class="signature-label">Place</div>
            </div>
        </div>

        {{-- 7. MANDATORY ATTACHMENTS --}}
        <h2>7. Mandatory Attachments</h2>
        <div class="section-body">
            <p>The following documents must accompany this resolution:</p>
            <ol class="attachments-list">
                <li>Certified copy of the Sole Director's Identity Document or Passport</li>
                <li>Proof of residential address of the Representative (not older than 3 months)</li>
                <li>Passport-size photograph of the Representative</li>
                <li>Company registration documents (COR 14.3 / CoR 39 or CIPC confirmation)</li>
                <li>Mandate to Act as SARS Representative (Power of Attorney)</li>
                <li>SARS Branch Cover Letter</li>
            </ol>
        </div>

    {{-- ============================================================== --}}
    {{-- TRUST: TRUSTEE RESOLUTION                                       --}}
    {{-- ============================================================== --}}
    @elseif($sarsRepRequest->entity_type === 'trust')

        <h1>Trustee Resolution</h1>
        <div class="subtitle">of</div>
        <div class="entity-name-line">{{ $entityName }}</div>
        <div class="reg-number-line">(Registration No: {{ $entityReg }})</div>

        <div class="preamble">
            <p>
                A resolution passed by the Trustees of <strong>{{ $entityName }}</strong>
                (Trust No: <strong>{{ $entityReg }}</strong>), a trust duly registered in
                accordance with the Trust Property Control Act, No. 57 of 1988, and the laws
                of the Republic of South Africa (hereinafter referred to as "the Trust"), at a
                duly convened meeting of the Trustees held on <strong>{{ $currentDate }}</strong>,
                at which a quorum was present, it was resolved that:
            </p>
        </div>

        {{-- 1. APPOINTMENT --}}
        <h2>1. Appointment of SARS Representative</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> <strong>{{ $repName }}</strong>
                (ID/Passport No: <strong>{{ $repId }}</strong>), acting in the capacity of
                <strong>{{ $capacityLabel }}</strong>, be and is hereby appointed as the registered
                SARS Representative of the Trust, for purposes of the Tax Administration Act,
                No. 28 of 2011, and all related tax legislation.
            </p>
        </div>

        {{-- 2. AUTHORITY GRANTED --}}
        <h2>2. Authority Granted</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> the appointed SARS Representative be authorised
                to act on behalf of the Trust in all dealings with the South African Revenue
                Service in respect of the following tax types:
            </p>
            <div style="margin: 10px 0 15px 0;">
                @foreach($allTaxTypes as $key => $label)
                    <span class="tax-type-row">
                        <span class="checkbox">{{ in_array($key, $selectedTypes) ? '☑' : '☐' }}</span>
                        {{ $label }}
                    </span><br>
                @endforeach
            </div>
            <p>The appointed SARS Representative is specifically authorised to:</p>
            <ol class="powers-list">
                <li>Register, amend, or deregister tax types with SARS on behalf of the Trust;</li>
                <li>Submit tax returns, declarations, and supporting documentation;</li>
                <li>Make tax payments and request allocation of payments;</li>
                <li>Request, receive, and process tax refunds;</li>
                <li>Lodge objections and appeals against assessments;</li>
                <li>Apply for tax directives, tax clearance certificates, and compliance status;</li>
                <li>Access, manage, and operate the Trust's SARS eFiling profile and any other SARS electronic systems;</li>
                <li>Correspond and communicate with SARS on all matters affecting the Trust's tax affairs;</li>
                <li>Attend to any administrative or procedural matters with SARS, including visits to SARS branch offices.</li>
            </ol>
        </div>

        {{-- 3. EXECUTION OF DOCUMENTS --}}
        <h2>3. Execution of Documents</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> the appointed SARS Representative be authorised
                to sign and execute all documents, forms, mandates, and correspondence as may be
                required by the South African Revenue Service to give effect to this resolution,
                including but not limited to the SARS Mandate / Power of Attorney and the SARS
                Branch Cover Letter.
            </p>
        </div>

        {{-- 4. EFFECTIVE DATE --}}
        <h2>4. Effective Date</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> this resolution shall take effect from the date
                of adoption hereof and shall remain in force until revoked by a subsequent
                resolution of the Trustees.
            </p>
        </div>

        {{-- 5. RATIFICATION --}}
        <h2>5. Ratification</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> any and all actions previously taken by any
                trustee or authorised person on behalf of the Trust in connection with the
                appointment and registration of a SARS Representative be and are hereby ratified
                and confirmed in all respects.
            </p>
        </div>

        {{-- 6. SIGNATURES --}}
        <h2>6. Signatures of Trustees</h2>
        <div class="signature-block">
            <p style="margin-bottom: 5px;">
                The undersigned trustees hereby confirm the adoption of this resolution:
            </p>

            <table style="width: 100%; border: none;">
                @for($i = 0; $i < $numDirectors; $i++)
                    @if($i % 2 == 0)
                        <tr>
                    @endif
                    <td style="width: 50%; border: none; padding: 0 {{ $i % 2 == 0 ? '10px 15px 0' : '0 15px 10px' }};">
                        <div class="signature-line" style="width: 100%;"></div>
                        <div class="signature-label">Trustee {{ $i + 1 }} - Signature</div>
                        <div class="signature-line" style="width: 100%;"></div>
                        <div class="signature-label">Full Name</div>
                        <div class="signature-line" style="width: 100%;"></div>
                        <div class="signature-label">Date</div>
                    </td>
                    @if($i % 2 == 1 || $i == $numDirectors - 1)
                        @if($i % 2 == 0)
                            <td style="width: 50%; border: none;">&nbsp;</td>
                        @endif
                        </tr>
                    @endif
                @endfor
            </table>
        </div>

        {{-- 7. MANDATORY ATTACHMENTS --}}
        <h2>7. Mandatory Attachments</h2>
        <div class="section-body">
            <p>The following documents must accompany this resolution:</p>
            <ol class="attachments-list">
                <li>Certified copy of the Representative's Identity Document or Passport</li>
                <li>Proof of residential address of the Representative (not older than 3 months)</li>
                <li>Passport-size photograph of the Representative</li>
                <li>Trust Deed</li>
                <li>Letters of Authority issued by the Master of the High Court</li>
                <li>Mandate to Act as SARS Representative (Power of Attorney)</li>
                <li>SARS Branch Cover Letter</li>
            </ol>
        </div>

    {{-- ============================================================== --}}
    {{-- SOLE TRUSTEE TRUST: SOLE TRUSTEE RESOLUTION                    --}}
    {{-- ============================================================== --}}
    @elseif($sarsRepRequest->entity_type === 'sole_trustee_trust')

        <h1>Sole Trustee Resolution</h1>
        <div class="subtitle">of</div>
        <div class="entity-name-line">{{ $entityName }}</div>
        <div class="reg-number-line">(Trust No: {{ $entityReg }})</div>

        <div class="preamble">
            <p>
                I, the undersigned, being the sole trustee of <strong>{{ $entityName }}</strong>
                (Trust No: <strong>{{ $entityReg }}</strong>), a trust duly registered in
                accordance with the Trust Property Control Act, No. 57 of 1988, and the laws of
                the Republic of South Africa, do hereby resolve as follows:
            </p>
        </div>

        {{-- 1. APPOINTMENT --}}
        <h2>1. Appointment of SARS Representative</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> <strong>{{ $repName }}</strong>
                (ID/Passport No: <strong>{{ $repId }}</strong>), acting in the capacity of
                <strong>{{ $capacityLabel }}</strong>, be and is hereby appointed as the registered
                SARS Representative of the Trust, for purposes of the Tax Administration Act,
                No. 28 of 2011, and all related tax legislation.
            </p>
        </div>

        {{-- 2. AUTHORITY GRANTED --}}
        <h2>2. Authority Granted</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> the appointed SARS Representative be authorised
                to act on behalf of the Trust in all dealings with the South African Revenue
                Service in respect of the following tax types:
            </p>
            <div style="margin: 10px 0 15px 0;">
                @foreach($allTaxTypes as $key => $label)
                    <span class="tax-type-row">
                        <span class="checkbox">{{ in_array($key, $selectedTypes) ? '☑' : '☐' }}</span>
                        {{ $label }}
                    </span><br>
                @endforeach
            </div>
            <p>The appointed SARS Representative is specifically authorised to:</p>
            <ol class="powers-list">
                <li>Register, amend, or deregister tax types with SARS on behalf of the Trust;</li>
                <li>Submit tax returns, declarations, and supporting documentation;</li>
                <li>Make tax payments and request allocation of payments;</li>
                <li>Request, receive, and process tax refunds;</li>
                <li>Lodge objections and appeals against assessments;</li>
                <li>Apply for tax directives, tax clearance certificates, and compliance status;</li>
                <li>Access, manage, and operate the Trust's SARS eFiling profile and any other SARS electronic systems;</li>
                <li>Correspond and communicate with SARS on all matters affecting the Trust's tax affairs;</li>
                <li>Attend to any administrative or procedural matters with SARS, including visits to SARS branch offices.</li>
            </ol>
        </div>

        {{-- 3. EXECUTION OF DOCUMENTS --}}
        <h2>3. Execution of Documents</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> the appointed SARS Representative be authorised
                to sign and execute all documents, forms, mandates, and correspondence as may be
                required by the South African Revenue Service to give effect to this resolution.
            </p>
        </div>

        {{-- 4. EFFECTIVE DATE --}}
        <h2>4. Effective Date</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> this resolution shall take effect from the date
                of adoption hereof and shall remain in force until revoked in writing by the
                sole trustee.
            </p>
        </div>

        {{-- 5. RATIFICATION --}}
        <h2>5. Ratification</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> any and all actions previously taken in connection
                with the appointment and registration of a SARS Representative on behalf of the
                Trust be and are hereby ratified and confirmed in all respects.
            </p>
        </div>

        {{-- 6. SIGNATURE --}}
        <h2>6. Signature of Sole Trustee</h2>
        <div class="signature-block">
            <div class="signature-line"></div>
            <div class="signature-label">Signature of Sole Trustee</div>

            <div style="margin-top: 15px;">
                <div class="signature-line"></div>
                <div class="signature-label">Full Name</div>
            </div>

            <div style="margin-top: 15px;">
                <div class="signature-line"></div>
                <div class="signature-label">ID / Passport Number</div>
            </div>

            <div style="margin-top: 15px;">
                <div class="signature-line"></div>
                <div class="signature-label">Date</div>
            </div>

            <div style="margin-top: 15px;">
                <div class="signature-line"></div>
                <div class="signature-label">Place</div>
            </div>
        </div>

        {{-- 7. MANDATORY ATTACHMENTS --}}
        <h2>7. Mandatory Attachments</h2>
        <div class="section-body">
            <p>The following documents must accompany this resolution:</p>
            <ol class="attachments-list">
                <li>Certified copy of the Sole Trustee's Identity Document or Passport</li>
                <li>Proof of residential address of the Representative (not older than 3 months)</li>
                <li>Passport-size photograph of the Representative</li>
                <li>Trust Deed</li>
                <li>Letters of Authority issued by the Master of the High Court</li>
                <li>Mandate to Act as SARS Representative (Power of Attorney)</li>
                <li>SARS Branch Cover Letter</li>
            </ol>
        </div>

    {{-- ============================================================== --}}
    {{-- NPC / NPO: RESOLUTION OF THE BOARD / OFFICE BEARERS            --}}
    {{-- ============================================================== --}}
    @elseif(in_array($sarsRepRequest->entity_type, ['npc', 'npo']))

        <h1>Resolution of the Board / Office Bearers</h1>
        <div class="subtitle">of</div>
        <div class="entity-name-line">{{ $entityName }}</div>
        <div class="reg-number-line">(Registration No: {{ $entityReg }})</div>

        <div class="preamble">
            <p>
                A resolution passed by the Board of Directors / Office Bearers of
                <strong>{{ $entityName }}</strong> (Registration No: <strong>{{ $entityReg }}</strong>),
                a {{ $sarsRepRequest->entity_type === 'npc' ? 'Non-Profit Company incorporated in terms of the Companies Act, No. 71 of 2008' : 'Non-Profit Organisation registered in terms of the Non-Profit Organisations Act, No. 71 of 1997' }},
                and the laws of the Republic of South Africa (hereinafter referred to as
                "the Organisation"), at a duly convened meeting held on
                <strong>{{ $currentDate }}</strong>, at which a quorum was present, it was
                resolved that:
            </p>
        </div>

        {{-- 1. APPOINTMENT --}}
        <h2>1. Appointment of SARS Representative</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> <strong>{{ $repName }}</strong>
                (ID/Passport No: <strong>{{ $repId }}</strong>), acting in the capacity of
                <strong>{{ $capacityLabel }}</strong>, be and is hereby appointed as the registered
                SARS Representative of the Organisation, for purposes of the Tax Administration
                Act, No. 28 of 2011, and all related tax legislation.
            </p>
        </div>

        {{-- 2. AUTHORITY GRANTED --}}
        <h2>2. Authority Granted</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> the appointed SARS Representative be authorised
                to act on behalf of the Organisation in all dealings with the South African
                Revenue Service in respect of the following tax types:
            </p>
            <div style="margin: 10px 0 15px 0;">
                @foreach($allTaxTypes as $key => $label)
                    <span class="tax-type-row">
                        <span class="checkbox">{{ in_array($key, $selectedTypes) ? '☑' : '☐' }}</span>
                        {{ $label }}
                    </span><br>
                @endforeach
            </div>
            <p>The appointed SARS Representative is specifically authorised to:</p>
            <ol class="powers-list">
                <li>Register, amend, or deregister tax types with SARS on behalf of the Organisation;</li>
                <li>Submit tax returns, declarations, and supporting documentation;</li>
                <li>Make tax payments and request allocation of payments;</li>
                <li>Request, receive, and process tax refunds;</li>
                <li>Lodge objections and appeals against assessments;</li>
                <li>Apply for tax directives, tax clearance certificates, and compliance status;</li>
                <li>Apply for and maintain Public Benefit Organisation (PBO) status and section 18A approval, where applicable;</li>
                <li>Access, manage, and operate the Organisation's SARS eFiling profile and any other SARS electronic systems;</li>
                <li>Correspond and communicate with SARS on all matters affecting the Organisation's tax affairs;</li>
                <li>Attend to any administrative or procedural matters with SARS, including visits to SARS branch offices.</li>
            </ol>
        </div>

        {{-- 3. EXECUTION OF DOCUMENTS --}}
        <h2>3. Execution of Documents</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> the appointed SARS Representative be authorised
                to sign and execute all documents, forms, mandates, and correspondence as may be
                required by the South African Revenue Service to give effect to this resolution,
                including but not limited to the SARS Mandate / Power of Attorney and the SARS
                Branch Cover Letter.
            </p>
        </div>

        {{-- 4. EFFECTIVE DATE --}}
        <h2>4. Effective Date</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> this resolution shall take effect from the date
                of adoption hereof and shall remain in force until revoked by a subsequent
                resolution of the Board of Directors / Office Bearers.
            </p>
        </div>

        {{-- 5. RATIFICATION --}}
        <h2>5. Ratification</h2>
        <div class="section-body">
            <p>
                <strong>RESOLVED THAT</strong> any and all actions previously taken by any
                director, office bearer, or authorised person on behalf of the Organisation in
                connection with the appointment and registration of a SARS Representative be and
                are hereby ratified and confirmed in all respects.
            </p>
        </div>

        {{-- 6. SIGNATURES --}}
        <h2>6. Signatures of Board Members / Office Bearers</h2>
        <div class="signature-block">
            <p style="margin-bottom: 5px;">
                The undersigned members of the Board / Office Bearers hereby confirm the adoption
                of this resolution:
            </p>

            <table style="width: 100%; border: none;">
                @for($i = 0; $i < $numDirectors; $i++)
                    @if($i % 2 == 0)
                        <tr>
                    @endif
                    <td style="width: 50%; border: none; padding: 0 {{ $i % 2 == 0 ? '10px 15px 0' : '0 15px 10px' }};">
                        <div class="signature-line" style="width: 100%;"></div>
                        <div class="signature-label">Board Member {{ $i + 1 }} - Signature</div>
                        <div class="signature-line" style="width: 100%;"></div>
                        <div class="signature-label">Full Name</div>
                        <div class="signature-line" style="width: 100%;"></div>
                        <div class="signature-label">Date</div>
                    </td>
                    @if($i % 2 == 1 || $i == $numDirectors - 1)
                        @if($i % 2 == 0)
                            <td style="width: 50%; border: none;">&nbsp;</td>
                        @endif
                        </tr>
                    @endif
                @endfor
            </table>
        </div>

        {{-- 7. MANDATORY ATTACHMENTS --}}
        <h2>7. Mandatory Attachments</h2>
        <div class="section-body">
            <p>The following documents must accompany this resolution:</p>
            <ol class="attachments-list">
                <li>Certified copy of the Representative's Identity Document or Passport</li>
                <li>Proof of residential address of the Representative (not older than 3 months)</li>
                <li>Passport-size photograph of the Representative</li>
                <li>
                    @if($sarsRepRequest->entity_type === 'npc')
                        NPC registration documents (CIPC registration certificate)
                    @else
                        NPO registration certificate issued by the Department of Social Development
                    @endif
                </li>
                @if($sarsRepRequest->entity_type === 'npo')
                    <li>NPO Certificate (if available)</li>
                @endif
                <li>Constitution / Founding Document of the Organisation</li>
                <li>Mandate to Act as SARS Representative (Power of Attorney)</li>
                <li>SARS Branch Cover Letter</li>
            </ol>
        </div>

    @endif

</body>
</html>
