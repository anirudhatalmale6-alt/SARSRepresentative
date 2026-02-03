<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mandate to Act as SARS Representative - {{ $sarsRepRequest->entity_name }}</title>
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
            font-size: 10pt;
            color: #333;
            margin-bottom: 25px;
            font-style: italic;
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
        .tax-type-grid {
            margin: 10px 0 15px 0;
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
        .signature-row {
            display: inline-block;
            width: 48%;
            vertical-align: top;
        }
        .witness-block {
            margin-top: 30px;
            page-break-inside: avoid;
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

    {{-- TITLE --}}
    <h1>Mandate to Act as SARS Representative</h1>
    <h1 style="font-size: 13pt; margin-top: 0;">(Power of Attorney)</h1>
    <div class="subtitle">
        Issued in terms of the Tax Administration Act, No. 28 of 2011
    </div>

    {{-- SECTION 1: TAXPAYER / ENTITY DETAILS --}}
    <h2>1. Taxpayer / Entity Details</h2>
    <table class="details-table">
        <tr>
            <td class="label">Entity / Taxpayer Name</td>
            <td class="value">{{ $sarsRepRequest->entity_name }}</td>
        </tr>
        <tr>
            <td class="label">Registration Number</td>
            <td class="value">{{ $sarsRepRequest->entity_reg_number ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Entity Type</td>
            <td class="value">{{ $sarsRepRequest->getEntityTypeLabel() }}</td>
        </tr>
        <tr>
            <td class="label">Income Tax Reference No.</td>
            <td class="value">{{ $sarsRepRequest->income_tax_ref ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">PAYE Reference No.</td>
            <td class="value">{{ $sarsRepRequest->paye_ref ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">VAT Reference No.</td>
            <td class="value">{{ $sarsRepRequest->vat_ref ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">UIF / SDL Reference No.</td>
            <td class="value">{{ $sarsRepRequest->uif_sdl_ref ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Registered Address</td>
            <td class="value">{{ $sarsRepRequest->entity_address ?? '—' }}</td>
        </tr>
    </table>

    {{-- SECTION 2: APPOINTED SARS REPRESENTATIVE --}}
    <h2>2. Appointed SARS Representative</h2>
    <table class="details-table">
        <tr>
            <td class="label">Full Name</td>
            <td class="value">{{ $sarsRepRequest->sarsRepresentative->full_name }}</td>
        </tr>
        <tr>
            <td class="label">Identity Number</td>
            <td class="value">{{ $sarsRepRequest->sarsRepresentative->id_number ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Passport Number</td>
            <td class="value">{{ $sarsRepRequest->sarsRepresentative->passport_number ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Capacity</td>
            <td class="value">{{ \Modules\SARSRepresentative\Models\SarsRepresentative::getCapacityLabel($sarsRepRequest->sarsRepresentative->capacity) }}</td>
        </tr>
        <tr>
            <td class="label">Contact Number</td>
            <td class="value">{{ $sarsRepRequest->sarsRepresentative->mobile ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Email Address</td>
            <td class="value">{{ $sarsRepRequest->sarsRepresentative->email ?? '—' }}</td>
        </tr>
    </table>

    {{-- SECTION 3: SCOPE OF AUTHORITY --}}
    <h2>3. Scope of Authority</h2>
    <div class="section-body">
        <p>
            The Taxpayer / Entity hereby grants the above-named SARS Representative the authority
            to act on its behalf in all dealings with the South African Revenue Service in respect
            of the following tax types:
        </p>

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
        @endphp

        <div class="tax-type-grid">
            @foreach($allTaxTypes as $key => $label)
                <span class="tax-type-row">
                    <span class="checkbox">{{ in_array($key, $selectedTypes) ? '☑' : '☐' }}</span>
                    {{ $label }}
                </span><br>
            @endforeach
        </div>

        <p>The appointed SARS Representative is authorised to perform the following on behalf of the Taxpayer / Entity:</p>
        <ol class="powers-list">
            <li>Register, amend, or deregister tax types with SARS;</li>
            <li>Submit tax returns, declarations, and any supporting schedules or documentation;</li>
            <li>Make tax payments and request the allocation of payments to specific tax liabilities;</li>
            <li>Request, receive, and process tax refunds on behalf of the entity;</li>
            <li>Lodge objections and appeals against SARS assessments and decisions;</li>
            <li>Request the suspension of payment pending the outcome of disputes;</li>
            <li>Apply for tax directives, tax clearance certificates, and compliance status;</li>
            <li>Correspond and communicate with SARS on all matters affecting the taxpayer;</li>
            <li>Access, view, and manage the taxpayer's profile and records on SARS systems;</li>
            <li>Attend to any administrative or procedural matters with SARS, including visits to SARS branch offices;</li>
            <li>Appoint or instruct third parties to assist with the performance of any of the above functions, where necessary;</li>
            <li>Perform any and all acts necessary to give effect to this mandate.</li>
        </ol>
    </div>

    {{-- SECTION 4: ACCESS TO SARS ELECTRONIC SYSTEMS --}}
    <h2>4. Access to SARS Electronic Systems</h2>
    <div class="section-body">
        <p>
            The Taxpayer / Entity hereby authorises the appointed SARS Representative to access
            and utilise the SARS eFiling platform, SARS Online Query System, and any other
            electronic systems or portals operated by SARS, for the purpose of performing the
            functions and exercising the authority set out in this mandate.
        </p>
        <p>
            The Taxpayer / Entity acknowledges that the appointed Representative shall have full
            access to view, download, and manage all tax-related information, correspondence, and
            records available on such electronic systems in respect of the entity's tax affairs.
        </p>
    </div>

    {{-- SECTION 5: DURATION OF MANDATE --}}
    <h2>5. Duration of Mandate</h2>
    <div class="section-body">
        <p>
            This mandate shall take effect from the date of signature hereof and shall remain in
            force until revoked in writing by the Taxpayer / Entity, or until the appointed SARS
            Representative resigns from the appointment by providing written notice to the entity.
        </p>
        <p>
            Revocation of this mandate shall not affect any acts lawfully performed by the SARS
            Representative prior to the date of revocation.
        </p>
    </div>

    {{-- SECTION 6: DECLARATION --}}
    <h2>6. Declaration</h2>
    <div class="section-body">
        <p>
            I/We, the undersigned, being duly authorised to act on behalf of
            <strong>{{ $sarsRepRequest->entity_name }}</strong>, hereby declare that:
        </p>
        <ol style="margin-left: 20px;">
            <li style="margin-bottom: 6px;">
                The information provided in this mandate is true, correct, and complete;
            </li>
            <li style="margin-bottom: 6px;">
                The appointed SARS Representative has been duly authorised by resolution of the
                @if(in_array($sarsRepRequest->entity_type, ['company', 'sole_director_company']))
                    Board of Directors
                @elseif(in_array($sarsRepRequest->entity_type, ['trust', 'sole_trustee_trust']))
                    Trustee(s)
                @elseif(in_array($sarsRepRequest->entity_type, ['npc', 'npo']))
                    Board / Office Bearers
                @else
                    governing body
                @endif
                of the entity;
            </li>
            <li style="margin-bottom: 6px;">
                I/We understand the implications and responsibilities associated with this appointment;
            </li>
            <li style="margin-bottom: 6px;">
                I/We accept responsibility for any actions taken by the SARS Representative within
                the scope of this mandate.
            </li>
        </ol>
    </div>

    {{-- SECTION 7: SIGNATURE(S) --}}
    <h2>7. Signature(s)</h2>
    <div class="signature-block">
        <p style="margin-bottom: 5px;"><strong>Signed on behalf of {{ $sarsRepRequest->entity_name }}:</strong></p>

        <div style="margin-bottom: 25px;">
            <div class="signature-line"></div>
            <div class="signature-label">Signature of Authorised Person</div>
        </div>

        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 50%; border: none; padding: 0 10px 0 0;">
                    <div class="signature-line" style="width: 100%;"></div>
                    <div class="signature-label">Full Name</div>
                </td>
                <td style="width: 50%; border: none; padding: 0 0 0 10px;">
                    <div class="signature-line" style="width: 100%;"></div>
                    <div class="signature-label">Capacity</div>
                </td>
            </tr>
        </table>

        <table style="width: 100%; border: none; margin-top: 15px;">
            <tr>
                <td style="width: 50%; border: none; padding: 0 10px 0 0;">
                    <div class="signature-line" style="width: 100%;"></div>
                    <div class="signature-label">Date</div>
                </td>
                <td style="width: 50%; border: none; padding: 0 0 0 10px;">
                    <div class="signature-line" style="width: 100%;"></div>
                    <div class="signature-label">Place</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- SECTION 8: WITNESS --}}
    <h2>8. Witness</h2>
    <div class="witness-block">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 50%; border: none; padding: 0 10px 0 0;">
                    <p style="margin-bottom: 5px;"><strong>Witness 1:</strong></p>
                    <div class="signature-line" style="width: 100%;"></div>
                    <div class="signature-label">Signature</div>
                    <div class="signature-line" style="width: 100%;"></div>
                    <div class="signature-label">Full Name</div>
                    <div class="signature-line" style="width: 100%;"></div>
                    <div class="signature-label">Date</div>
                </td>
                <td style="width: 50%; border: none; padding: 0 0 0 10px;">
                    <p style="margin-bottom: 5px;"><strong>Witness 2:</strong></p>
                    <div class="signature-line" style="width: 100%;"></div>
                    <div class="signature-label">Signature</div>
                    <div class="signature-line" style="width: 100%;"></div>
                    <div class="signature-label">Full Name</div>
                    <div class="signature-line" style="width: 100%;"></div>
                    <div class="signature-label">Date</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- SECTION 9: REQUIRED ATTACHMENTS --}}
    <h2>9. Required Attachments</h2>
    <div class="section-body">
        <p>The following documents must accompany this mandate:</p>
        <ol class="attachments-list">
            <li>Certified copy of the Representative's Identity Document or Passport</li>
            <li>Proof of residential address of the Representative (not older than 3 months)</li>
            <li>Passport-size photograph of the Representative</li>
            <li>
                @if(in_array($sarsRepRequest->entity_type, ['company', 'sole_director_company']))
                    Company registration documents (COR 14.3 / CoR 39 or CIPC confirmation)
                @elseif(in_array($sarsRepRequest->entity_type, ['trust', 'sole_trustee_trust']))
                    Trust Deed and Letters of Authority issued by the Master of the High Court
                @elseif(in_array($sarsRepRequest->entity_type, ['npc', 'npo']))
                    Entity registration documents and NPO Certificate (if applicable)
                @else
                    Entity registration documents
                @endif
            </li>
            <li>Resolution authorising the appointment of the SARS Representative</li>
            <li>SARS Branch Cover Letter</li>
        </ol>
    </div>

</body>
</html>
