<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notification PDF</title>
    <style>
        /* CSS untuk mempercantik tampilan PDF */
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            padding: 10px 0;
            border-bottom: 2px solid #333;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
            color: #007BFF;
        }

        .header p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th, .table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .table td {
            background-color: #fff;
        }

        .section-title {
            font-size: 18px;
            color: #333;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Detail Notifikasi</h1>
        <p>Informasi mengenai peminjaman dokumen</p>
    </div>

    <div class="section-title">Informasi Peminjaman</div>
    <table class="table">
        <tr>
            <th>Jenis Dokumen</th>
            <td>{{ $notification->jenis_dokumen }}</td>
        </tr>
        <tr>
            <th>Nomor Dokumen</th>
            <td>{{ $notification->nomor_dokumen }}</td>
        </tr>
        <tr>
            <th>Nama Peminjam</th>
            <td>{{ $notification->peminjam }}</td>
        </tr>
        <tr>
            <th>Tanggal Peminjaman</th>
            <td>{{ $notification->tanggal_peminjaman }}</td>
        </tr>
        <tr>
            <th>Tanggal Pengembalian</th>
            <td>{{ $notification->tanggal_pengembalian }}</td>
        </tr>
        <tr>
            <th>Status Permohonan</th>
            <td>{{ $notification->status_permohonan }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Produced by Bpn Karawang Admin System   {{ now()->format('d M Y') }}</p>
    </div>
</body>
</html>