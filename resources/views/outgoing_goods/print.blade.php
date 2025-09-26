<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $outgoingGood->no_surat_jalan }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header, .footer {
            overflow: hidden;
            margin-bottom: 20px;
        }
        .header-left {
            float: left;
            width: 60%;
        }
        .header-left img {
            width: 60px;
            height: auto;
            float: left;
            margin-right: 15px;
        }
        .company-details {
            float: left;
        }
        .company-details h3 {
            margin: 0;
            font-weight: bold;
            font-size: 14px;
        }
        .do-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            border: 1px solid #000;
            padding: 5px;
            margin-bottom: 15px;
        }
        .details-section {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .details-section td {
            padding: 2px 5px;
            vertical-align: top;
        }
        .details-left, .details-right {
            width: 50%;
            border: 1px solid #000;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            text-align: center;
        }
        .items-table td:first-child, .items-table td:nth-child(3), .items-table td:nth-child(4) {
            text-align: center;
        }
        .items-table .description-col {
            width: 50%;
        }
        .signature-section {
            width: 100%;
            margin-top: 30px;
        }
        .signature-section td {
            width: 25%;
            text-align: center;
            padding-top: 5px;
            vertical-align: top;
        }
        .signature-name {
            margin-top: 60px;
            text-decoration: underline;
        }
        .notes-section {
            margin-top: 20px;
            font-size: 10px;
        }
        .footer-doc {
            font-size: 9px;
            text-align: right;
            margin-top: 20px;
        }
        .print-button {
            display: block;
            width: 100px;
            margin: 20px auto;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <img src="https://via.placeholder.com/100x100.png?text=LOGO" alt="Company Logo">
                <div class="company-details">
                    <h3>PT. MODANO ENERGI INDONESIA</h3>
                    <span>Your Reliable Partner</span><br>
                    <strong>Office & Workshop :</strong><br>
                    Jl. Raya Jonggol - Cibucil, Sukamanah<br>
                    Jonggol - Bogor 16830<br>
                    Telp: 021 - 89930536<br>
                    Email : info@modano-energi.com
                </div>
            </div>
        </div>

        <div class="do-title">Surat Jalan</div>

        <table class="details-section">
            <tr>
                <td class="details-left">
                    <table>
                        <tr><td><strong>TO</strong></td><td>: {{ $outgoingGood->project }}</td></tr>
                        <tr><td><strong>Phone</strong></td><td>:</td></tr>
                        <tr><td><strong>Fax</strong></td><td>:</td></tr>
                        <tr><td><strong>Attn</strong></td><td>:</td></tr>
                    </table>
                </td>
                <td class="details-right">
                     <table>
                        <tr><td><strong>NO SJ</strong></td><td>: {{ $outgoingGood->no_surat_jalan }}</td></tr>
                        <tr><td><strong>Date</strong></td><td>: {{ \Carbon\Carbon::parse($outgoingGood->tanggal)->format('d F Y') }}</td></tr>
                        <tr><td><strong>Your ref</strong></td><td>:</td></tr>
                        <tr><td><strong>PO. No.</strong></td><td>:</td></tr>
                        <tr><td><strong>Jo. No.</strong></td><td>: {{ $outgoingGood->jo_number }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">NO.</th>
                    <th class="description-col">DESCRIPTION</th>
                    <th style="width: 15%;">QTY</th>
                    <th style="width: 15%;">Satuan</th>
                    <th>REMARK</th>
                </tr>
            </thead>
            <tbody>
                @forelse($outgoingGood->details as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $detail->product->name }}</td>
                    {{-- REVISI 1: QTY dan Satuan dipisah --}}
                    <td>{{ $detail->quantity }}</td>
                    <td>{{ $detail->product->unit }}</td>
                    <td></td>
                </tr>
                @empty
                <tr>
                    {{-- REVISI 2: Colspan diubah menjadi 5 --}}
                    <td colspan="5" style="text-align: center; height: 200px;">- No Items -</td>
                </tr>
                @endforelse
                
                {{-- REVISI 3: Loop untuk baris kosong diubah menjadi 5 kolom --}}
                @for ($i = $outgoingGood->details->count(); $i < 8; $i++)
                <tr>
                    <td style="height: 30px;">&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td> {{-- Kolom ke-5 ditambahkan --}}
                </tr>
                @endfor
            </tbody>
        </table>

        <table class="signature-section">
             <tr>
                <td>Send by</td>
                <td>Checked by</td>
                <td>Received by</td>
                <td>Driver by</td>
             </tr>
             <tr>
                <td>
                    <div class="signature-name">(....................)</div>
                    <div>Name:</div>
                    <div>Date:</div>
                </td>
                <td>
                    <div class="signature-name">(....................)</div>
                    <div>Name:</div>
                    <div>Date:</div>
                </td>
                 <td>
                    <div class="signature-name">(....................)</div>
                    <div>Name:</div>
                    <div>Date:</div>
                </td>
                 <td>
                    <div class="signature-name">(....................)</div>
                    <div>Name:</div>
                    <div>Date:</div>
                </td>
             </tr>
        </table>

        <table style="width: 100%; margin-top: 20px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div class="notes-section">
                        <strong>Catatan / Tembusan</strong><br>
                        Putih : Administrasi<br>
                        Merah : Finance<br>
                        Kuning : Customer<br>
                        Hijau : Security<br>
                        Biru : Arsip
                    </div>
                </td>
                <td style="width: 50%; vertical-align: bottom;">
                    <strong>Ekspedisi :</strong><br>
                    <strong>No.Pol :</strong><br>
                    <strong>No.Tlp :</strong><br>
                </td>
            </tr>
        </table>
        
        <div class="footer-doc">FM-WH-03/Rev00-01.10.2023</div>

        <button onclick="window.print()" class="print-button no-print">Cetak</button>
    </div>
</body>
</html>