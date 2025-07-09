<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #333;
            margin: 0;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENJUALAN</h1>
        <p>Periode: <?= date('d F Y') ?></p>
        <p>Dicetak pada: <?= date('d F Y H:i:s') ?></p>
    </div>

    <div class="info">
        <p><strong>Total Transaksi:</strong> <?= count($transactions) ?></p>
        <?php if (!empty($transactions)): ?>
            <?php 
            $totalPenjualan = array_sum(array_column($transactions, 'total_harga'));
            $totalOngkir = array_sum(array_column($transactions, 'ongkir'));
            $transaksiSelesai = count(array_filter($transactions, function($t) { return $t['status'] == '1'; }));
            $transaksiBelumSelesai = count($transactions) - $transaksiSelesai;
            ?>
            <p><strong>Total Penjualan:</strong> Rp <?= number_format($totalPenjualan, 0, ',', '.') ?></p>
            <p><strong>Total Ongkir:</strong> Rp <?= number_format($totalOngkir, 0, ',', '.') ?></p>
            <p><strong>Transaksi Selesai:</strong> <?= $transaksiSelesai ?> | <strong>Belum Selesai:</strong> <?= $transaksiBelumSelesai ?></p>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID Transaksi</th>
                <th>Username</th>
                <th>Total Harga</th>
                <th>Alamat</th>
                <th>Ongkir</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data penjualan</td>
                </tr>
            <?php else: ?>
                <?php 
                $totalPenjualan = 0;
                foreach ($transactions as $index => $item): 
                    $totalPenjualan += $item['total_harga'];
                ?>
                    <tr>
                        <td class="text-center"><?= $index + 1 ?></td>
                        <td><?= $item['id'] ?></td>
                        <td><?= $item['username'] ?></td>
                        <td class="text-right">Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                        <td><?= $item['alamat'] ?></td>
                        <td class="text-right">Rp <?= number_format($item['ongkir'], 0, ',', '.') ?></td>
                        <td><?= ($item['status'] == "1") ? "Sudah Selesai" : "Belum Selesai" ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3" class="text-right"><strong>TOTAL PENJUALAN:</strong></td>
                    <td class="text-right"><strong>Rp <?= number_format($totalPenjualan, 0, ',', '.') ?></strong></td>
                    <td colspan="4"></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem</p>
    </div>
</body>
</html>
