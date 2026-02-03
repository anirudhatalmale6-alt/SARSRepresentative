<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Index - {{ $sarsRepRequest->entity_name }}</title>
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
        .header-line {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 3px;
        }
        .divider {
            border-top: 2px solid #000;
            margin: 15px 0 25px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.summary-table td {
            padding: 4px 8px;
            vertical-align: top;
            border: 1px solid #ccc;
        }
        table.summary-table td.label {
            width: 35%;
            font-weight: bold;
            background-color: #f5f5f5;
        }
        table.summary-table td.value {
            width: 65%;
        }
        table.index-table {
            margin-top: 15px;
        }
        table.index-table th {
            background-color: #333;
            color: #fff;
            padding: 8px 10px;
            text-align: left;
            font-size: 10pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        table.index-table th.col-no {
            width: 8%;
            text-align: center;
        }
        table.index-table th.col-desc {
            width: 62%;
        }
        table.index-table th.col-status {
            width: 30%;
            text-align: center;
        }
        table.index-table td {
            padding: 8px 10px;
            border: 1px solid #ccc;
            vertical-align: top;
            font-size: 10.5pt;
        }
        table.index-table td.col-no {
            text-align: center;
            font-weight: bold;
        }
        table.index-table td.col-status {
            text-align: center;
        }
        table.index-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-included {
            color: #155724;
            font-weight: bold;
        }
        .status-not-provided {
            color: #856404;
            font-style: italic;
        }
        .footer-note {
            margin-top: 30px;
            font-size: 9pt;
            color: #555;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    @php
        // Collect all uploaded document types for status checking
        $uploadedTypes = $sarsRepRequest->sarsRepDocuments
            ? $sarsRepRequest->sarsRepDocuments->pluck('document_type')->unique()->toArray()
            : [];

        // Define the document index entries
        // Each entry: key => [number, description, document_type_key(s) to check]
        $indexEntries = [
            [
                'no'          => 1,
                'description' => 'SARS Branch Cover Letter',
                'check_keys'  => ['cover_letter'],
            ],
            [
                'no'          => 2,
                'description' => 'Mandate to Act as SARS Representative (Power of Attorney)',
                'check_keys'  => ['sars_mandate'],
            ],
            [
                'no'          => 3,
                'description' => 'Resolution Authorising Appointment of SARS Representative',
                'check_keys'  => ['resolution'],
            ],
            [
                'no'          => 4,
                'description' => 'Entity Registration Documents',
                'check_keys'  => ['entity_registration'],
            ],
            [
                'no'          => 5,
                'description' => 'Representative Identity Document (Certified Copy)',
                'check_keys'  => ['representative_id'],
            ],
            [
                'no'          => 6,
                'description' => 'Representative Proof of Residential Address',
                'check_keys'  => ['representative_address'],
            ],
            [
                'no'          => 7,
                'description' => 'Representative Passport-size Photograph',
                'check_keys'  => ['representative_photo'],
            ],
        ];

        // Add trust-specific documents
        if (in_array($sarsRepRequest->entity_type, ['trust', 'sole_trustee_trust'])) {
            $indexEntries[] = [
                'no'          => count($indexEntries) + 1,
                'description' => 'Trust Deed',
                'check_keys'  => ['trust_deed'],
            ];
            $indexEntries[] = [
                'no'          => count($indexEntries) + 1,
                'description' => 'Letters of Authority (Master of the High Court)',
                'check_keys'  => ['letters_of_authority'],
            ];
        }

        // Add NPC/NPO-specific documents
        if (in_array($sarsRepRequest->entity_type, ['npc', 'npo'])) {
            $indexEntries[] = [
                'no'          => count($indexEntries) + 1,
                'description' => 'NPO Certificate',
                'check_keys'  => ['npo_certificate'],
            ];
        }

        // Check for any additional/supporting documents not in standard list
        $standardKeys = ['cover_letter', 'sars_mandate', 'resolution', 'entity_registration',
                         'representative_id', 'representative_address', 'representative_photo',
                         'trust_deed', 'letters_of_authority', 'npo_certificate'];
        $additionalDocs = $sarsRepRequest->sarsRepDocuments
            ? $sarsRepRequest->sarsRepDocuments->filter(function ($doc) use ($standardKeys) {
                return !in_array($doc->document_type, $standardKeys);
            })
            : collect([]);

        $hasAdditional = $additionalDocs->count() > 0;

        // Add the supporting/additional row
        $indexEntries[] = [
            'no'          => count($indexEntries) + 1,
            'description' => 'Supporting / Additional Documents',
            'check_keys'  => [],
            'is_additional' => true,
        ];
    @endphp

    {{-- TITLE --}}
    <h1>SARS Representative Registration</h1>
    <h1 style="font-size: 14pt; margin-top: 0;">Document Index</h1>

    <div class="divider"></div>

    {{-- ENTITY AND REPRESENTATIVE SUMMARY --}}
    <table class="summary-table">
        <tr>
            <td class="label">Entity Name</td>
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
            <td class="label">SARS Representative</td>
            <td class="value">{{ $sarsRepRequest->sarsRepresentative->full_name }}</td>
        </tr>
        <tr>
            <td class="label">Representative ID / Passport</td>
            <td class="value">{{ $sarsRepRequest->sarsRepresentative->id_number ?? $sarsRepRequest->sarsRepresentative->passport_number ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Date Prepared</td>
            <td class="value">{{ $sarsRepRequest->submitted_at ? $sarsRepRequest->submitted_at->format('d F Y') : now()->format('d F Y') }}</td>
        </tr>
    </table>

    {{-- DOCUMENT INDEX TABLE --}}
    <h2>Document Bundle Contents</h2>

    <table class="index-table">
        <thead>
            <tr>
                <th class="col-no">No.</th>
                <th class="col-desc">Document Description</th>
                <th class="col-status">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($indexEntries as $entry)
                @php
                    if (!empty($entry['is_additional'])) {
                        $isIncluded = $hasAdditional;
                    } else {
                        $isIncluded = false;
                        foreach ($entry['check_keys'] as $key) {
                            if (in_array($key, $uploadedTypes)) {
                                $isIncluded = true;
                                break;
                            }
                        }
                    }
                @endphp
                <tr>
                    <td class="col-no">{{ $entry['no'] }}</td>
                    <td>
                        {{ $entry['description'] }}
                        @if(!empty($entry['is_additional']) && $hasAdditional)
                            <br>
                            <span style="font-size: 9pt; color: #555;">
                                @foreach($additionalDocs as $addDoc)
                                    &bull; {{ $addDoc->original_filename ?? \Modules\SARSRepresentative\Models\SarsRepDocument::getDocumentTypeLabel($addDoc->document_type) }}@if(!$loop->last), @endif
                                @endforeach
                            </span>
                        @endif
                    </td>
                    <td class="col-status">
                        @if($isIncluded)
                            <span class="status-included">Included</span>
                        @else
                            <span class="status-not-provided">Not Provided</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TAX TYPES SUMMARY --}}
    <h2>Tax Types Requested</h2>
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

    <table class="index-table">
        <thead>
            <tr>
                <th class="col-no">No.</th>
                <th class="col-desc">Tax Type</th>
                <th class="col-status">Selected</th>
            </tr>
        </thead>
        <tbody>
            @php $taxNo = 1; @endphp
            @foreach($allTaxTypes as $key => $label)
                <tr>
                    <td class="col-no">{{ $taxNo++ }}</td>
                    <td>{{ $label }}</td>
                    <td class="col-status">
                        @if(in_array($key, $selectedTypes))
                            <span class="status-included">{{ '☑' }} Yes</span>
                        @else
                            <span style="color: #999;">{{ '☐' }} No</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- REFERENCE NUMBERS --}}
    <h2>SARS Reference Numbers</h2>
    <table class="summary-table">
        <tr>
            <td class="label">Income Tax Reference</td>
            <td class="value">{{ $sarsRepRequest->income_tax_ref ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">PAYE Reference</td>
            <td class="value">{{ $sarsRepRequest->paye_ref ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">VAT Reference</td>
            <td class="value">{{ $sarsRepRequest->vat_ref ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">UIF / SDL Reference</td>
            <td class="value">{{ $sarsRepRequest->uif_sdl_ref ?? '—' }}</td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <div class="footer-note">
        This document index was automatically generated on
        {{ now()->format('d F Y \a\t H:i') }}.
        Please verify that all listed documents are present in the bundle before submission to SARS.
    </div>

</body>
</html>
