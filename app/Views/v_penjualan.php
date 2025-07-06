<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h3>Daftar Penjualan</h3>
<hr>
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">No</th>
                <th scope="col">Username</th>
                <th scope="col">Total Harga</th>
                <th scope="col">Alamat</th>
                <th scope="col">Ongkir</th>
                <th scope="col">Status</th>
                <th scope="col">Ubah Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="8" class="text-center">No entries found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($transactions as $index => $item): ?>
                    <tr>
                        <th scope="row"><?= $index + 1 ?></th>
                        <td><?= $item['id'] ?></td>
                        <td><?= $item['username'] ?></td>
                        <td><?= number_to_currency($item['total_harga'], 'IDR') ?></td>
                        <td><?= $item['alamat'] ?></td>
                        <td><?= number_to_currency($item['ongkir'], 'IDR') ?></td>
                        <td><?= ($item['status'] == "1") ? "Sudah Selesai" : "Belum Selesai" ?></td>
                        <td>
                            <form action="<?= base_url('transaction/updateStatus/' . $item['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="0" <?= ($item['status'] == "0") ? 'selected' : '' ?>>Belum Selesai</option>
                                    <option value="1" <?= ($item['status'] == "1") ? 'selected' : '' ?>>Sudah Selesai</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div> 
<?= $this->endSection(); ?>