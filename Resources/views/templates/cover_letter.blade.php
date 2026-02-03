<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SARS Branch Cover Letter - {{ $sarsRepRequest->entity_name }}</title>
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
        .date-block {
            text-align: right;
            margin-bottom: 25px;
        }
        .addressee {
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .subject-line {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 20px 0;
            text-decoration: underline;
            text-transform: uppercase;
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
        ol.documents-list {
            margin: 10px 0 15px 20px;
            padding: 0;
        }
        ol.documents-list li {
            margin-bottom: 4px;
        }
        .signature-block {
            margin-top: 40px;
        }
        .signature-block .line {
            border-bottom: 1px solid #000;
            width: 250px;
            margin-top: 40px;
            margin-bottom: 3px;
        }
        .signature-block .label {
            font-size: 10pt;
            color: #333;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    {{-- DATE --}}
    <div class="date-block">
        {{ $sarsRepRequest->submitted_at ? $sarsRepRequest->submitted_at->format('d F Y') : now()->format('d F Y') }}
    </div>

    {{-- ADDRESSEE --}}
    <div class="addressee">
        <strong>To:</strong><br>
        The South African Revenue Service<br>
        SARS Branch Office
    </div>

    {{-- SUBJECT LINE --}}
    <div class="subject-line">
        RE: Appointment and Registration of SARS Representative
    </div>

    {{-- SECTION 1: TAXPAYER DETAILS --}}
    <h2>1. Taxpayer Details</h2>
    <table class="details-table">
        <tr>
            <td class="label">Entity / Taxpayer Name</td>
            <td class="value">{{ $sarsRepRequest->entity_name }}</td>
        </tr>
        <tr>
            <td class="label">Trading Name</td>
            <td class="value">{{ $sarsRepRequest->entity_name }}</td>
        </tr>
        <tr>
            <td class="label">Registration Number</td>
            <td class="value">{{ $sarsRepRequest->entity_reg_number ?? '—' }}</td>
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
            <td class="label">Entity Type</td>
            <td class="value">{{ $sarsRepRequest->getEntityTypeLabel() }}</td>
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
            <td class="label">ID Number / Passport Number</td>
            <td class="value">{{ $sarsRepRequest->sarsRepresentative->id_number ?? $sarsRepRequest->sarsRepresentative->passport_number ?? '—' }}</td>
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

    {{-- SECTION 3: REQUEST TO SARS --}}
    <h2>3. Request to SARS</h2>
    <div class="section-body">
        <p>
            We, the undersigned, hereby formally request the South African Revenue Service to register
            and recognise <strong>{{ $sarsRepRequest->sarsRepresentative->full_name }}</strong>
            (ID/Passport No: <strong>{{ $sarsRepRequest->sarsRepresentative->id_number ?? $sarsRepRequest->sarsRepresentative->passport_number ?? '—' }}</strong>)
            as the duly appointed SARS Representative for
            <strong>{{ $sarsRepRequest->entity_name }}</strong>
            (Registration No: <strong>{{ $sarsRepRequest->entity_reg_number ?? '—' }}</strong>).
        </p>
        <p>
            We respectfully request that the above-named representative be registered and linked to
            all applicable tax types on SARS systems, including but not limited to:
        </p>
        <ul>
            @if(is_array($sarsRepRequest->tax_types))
                @foreach($sarsRepRequest->tax_types as $taxType)
                    <li>{{ strtoupper(str_replace('_', ' / ', $taxType)) }}</li>
                @endforeach
            @endif
        </ul>
        <p>
            The representative is authorised to act on behalf of the entity in all matters relating
            to the above tax types, including filing returns, making payments, receiving refunds,
            lodging disputes, and corresponding with SARS on all matters affecting the taxpayer.
        </p>
    </div>

    {{-- SECTION 4: DOCUMENTS SUBMITTED --}}
    <h2>4. Documents Submitted</h2>
    <div class="section-body">
        <p>The following documents are enclosed herewith in support of this application:</p>
        <ol class="documents-list">
            <li>Completed SARS Branch Cover Letter (this document)</li>
            <li>Mandate to Act as SARS Representative (Power of Attorney)</li>
            <li>Resolution Authorising the Appointment of the SARS Representative</li>
            <li>Entity Registration Documents (Company/Trust/NPO registration certificate)</li>
            <li>Certified copy of the Representative's Identity Document / Passport</li>
            <li>Representative's Proof of Residential Address (not older than 3 months)</li>
            <li>Representative's Passport-size Photograph</li>
            @if(in_array($sarsRepRequest->entity_type, ['trust', 'sole_trustee_trust']))
                <li>Trust Deed</li>
                <li>Letters of Authority issued by the Master of the High Court</li>
            @endif
            @if(in_array($sarsRepRequest->entity_type, ['npc', 'npo']))
                <li>NPO Certificate (if applicable)</li>
            @endif
        </ol>
    </div>

    {{-- SECTION 5: DECLARATION --}}
    <h2>5. Declaration</h2>
    <div class="section-body">
        <p>
            I, the undersigned, hereby declare that the information provided in this letter and in
            all accompanying documents is true, correct, and complete to the best of my knowledge
            and belief. I understand that any false or misleading information may result in the
            rejection of this application and may constitute an offence in terms of the Tax
            Administration Act, No. 28 of 2011.
        </p>
        <p>
            I further confirm that the appointed SARS Representative has been duly authorised by the
            entity in accordance with the relevant governing documents and applicable legislation.
        </p>
    </div>

    {{-- SIGNATURE BLOCK --}}
    <div class="signature-block">
        <div class="line"></div>
        <div class="label">Signature of Authorised Person</div>

        <div style="margin-top: 20px;">
            <div class="line"></div>
            <div class="label">Full Name: {{ $sarsRepRequest->sarsRepresentative->full_name }}</div>
        </div>

        <div style="margin-top: 20px;">
            <div class="line"></div>
            <div class="label">Capacity: {{ \Modules\SARSRepresentative\Models\SarsRepresentative::getCapacityLabel($sarsRepRequest->sarsRepresentative->capacity) }}</div>
        </div>

        <div style="margin-top: 20px;">
            <div class="line"></div>
            <div class="label">Date: {{ $sarsRepRequest->submitted_at ? $sarsRepRequest->submitted_at->format('d F Y') : now()->format('d F Y') }}</div>
        </div>
    </div>

</body>
</html>
