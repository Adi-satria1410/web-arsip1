<!DOCTYPE html>
<html>
<head>
    <title>Peminjaman PDF</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .detail {
            margin-bottom: 10px;
        }
        .label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Detail Peminjaman</h1>
    <div class="detail">
        <span class="label">Jenis Dokumen:</span> {{ $peminjaman->jenis_dokumen }}
    </div>
    <div class="detail">
        <span class="label">Nomor Dokumen:</span> {{ $peminjaman->nomor_dokumen }}
    </div>
    <div class="detail">
        <span class="label">Peminjam:</span> {{ $peminjaman->peminjam }}
    </div>
    <div class="detail">
        <span class="label">Tanggal Peminjaman:</span> {{ $peminjaman->tanggal_peminjaman }}
    </div>
    <div class="detail">
        <span class="label">Tanggal Pengembalian:</span> {{ $peminjaman->tanggal_pengembalian }}
    </div>
    <div class="detail">
        <span class="label">Kabupaten:</span> {{ $peminjaman->regency->name }}
    </div>
    <div class="detail">
        <span class="label">Kecamatan:</span> {{ $peminjaman->district->name }}
    </div>
    <div class="detail">
        <span class="label">Desa:</span> {{ $peminjaman->village->name }}
    </div>
    <div class="detail">
        <span class="label">Status Peminjaman:</span> {{ $peminjaman->status_peminjaman }}
    </div>
    <div class="detail">
        <span class="label">Denda:</span> IDR {{ number_format($peminjaman->denda, 0, ',', '.') }}
    </div>
</body>
</html>
