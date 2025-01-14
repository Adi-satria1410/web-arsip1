<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Peminjaman</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 20px;
            padding: 0;
        }

        h1,
        h2 {
            text-align: center;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
            word-wrap: break-word;
            font-size: 9px;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            text-align: center;
            font-size: 8px;
            color: #666;
            margin-top: 20px;
        }

        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <h1>Laporan Peminjaman</h1>
    <h2>{{ $title }}</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Dokumen</th>
                <th>Peminjam</th>
                <th>No.Dokumen</th>
                <th>Tggl Peminjaman</th>
                <th>Tggl Pengembalian</th>
                <th>Status</th>
                <th>Kab</th>
                <th>Kec</th>
                <th>Desa</th>
                <th>Denda</th>
                <th>Status Pengembalian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $index => $report)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $report->jenis_dokumen }}</td>
                    <td>{{ $report->peminjam }}</td>
                    <td>{{ $report->nomor_dokumen }}</td>
                    <td>{{ \Carbon\Carbon::parse($report->tanggal_peminjaman)->format('d M Y') }}</td>
                    <td>{{ $report->tanggal_pengembalian ? \Carbon\Carbon::parse($report->tanggal_pengembalian)->format('d M Y') : '-' }}</td>
                    <td>{{ $report->status }}</td>
                    <td>{{ $report->regency->name ?? 'N/A' }}</td>
                    <td>{{ $report->district->name ?? 'N/A' }}</td>
                    <td>{{ $report->village->name ?? 'N/A' }}</td>
                    <td>{{ $report->denda ? ucfirst($report->denda) : '-' }}</td>
                    <td>{{ $report->status_pengembalian ? 'Dikembalikan' : 'Dipinjam' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated by Laporan System - {{ now()->format('d M Y H:i') }}</p>
    </div>
</body>

</html>
