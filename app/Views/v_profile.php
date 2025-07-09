<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<hr>
<h5>History Transaksi Pembelian <strong><?= $username ?></strong></h5>
<hr>

<!-- Display flash messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <!-- Table with stripped rows -->
    <table class="table datatable">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">ID Pembelian</th>
                <th scope="col">Waktu Pembelian</th>
                <th scope="col">Total Bayar</th>
                <th scope="col">Alamat</th>
                <th scope="col">Status</th>
                <th scope="col">Bukti Transfer</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($buy)) :
                foreach ($buy as $index => $item) :
            ?>
                    <tr>
                        <th scope="row"><?php echo $index + 1 ?></th>
                        <td><?php echo $item['id'] ?></td>
                        <td><?php echo $item['created_at'] ?></td>
                        <td><?php echo number_to_currency($item['total_harga'], 'IDR') ?></td>
                        <td><?php echo $item['alamat'] ?></td>
                        <td><?php echo ($item['status'] == "1") ? "Sudah Selesai" : "Belum Selesai" ?></td>
                        <td>
                            <?php if (!empty($item['bukti_transfer'])): ?>
                                <span class="badge bg-success">Sudah Upload</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Belum Upload</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal-<?= $item['id'] ?>">
                                Detail
                            </button>
                            <?php if (empty($item['bukti_transfer'])): ?>
                                <button type="button" class="btn btn-primary btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#uploadModal-<?= $item['id'] ?>">
                                    Upload
                                </button>
                            <?php else: ?>
                                <a href="<?= base_url('assets/images/bukti_transfer/' . $item['bukti_transfer']) ?>" target="_blank" class="btn btn-info btn-sm ms-1">
                                    Lihat Bukti
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <!-- Detail Modal Begin -->
                    <div class="modal fade" id="detailModal-<?= $item['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Detail Data</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <?php 
                                    if(!empty($product) && isset($product[$item['id']])){
                                        foreach ($product[$item['id']] as $index2 => $item2) : ?>
                                            <?php echo $index2 + 1 . ")" ?>
                                            <?php if ($item2['foto'] != '' and file_exists("assets/images/" . $item2['foto'] . "")) : ?>
                                                <img src="<?php echo base_url() . "assets/images/" . $item2['foto'] ?>" width="100px">
                                            <?php endif; ?>
                                            <strong><?= $item2['nama'] ?></strong>
                                            <?= number_to_currency($item2['harga'], 'IDR') ?>
                                            <br>
                                            <?= "(" . $item2['jumlah'] . " pcs)" ?><br>
                                            <?= number_to_currency($item2['subtotal_harga'], 'IDR') ?>
                                            <hr>
                                        <?php 
                                        endforeach; 
                                    } else {
                                        echo "<p>Tidak ada detail produk yang tersedia.</p>";
                                    }
                                    ?>
                                    Ongkir <?= number_to_currency($item['ongkir'], 'IDR') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Detail Modal End -->
                    
                    <!-- Upload Modal Begin -->
                    <div class="modal fade" id="uploadModal-<?= $item['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Upload Bukti Transfer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="<?= base_url('upload-bukti-transfer') ?>" method="post" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <input type="hidden" name="transaction_id" value="<?= $item['id'] ?>">
                                        <div class="mb-3">
                                            <label for="bukti_transfer_<?= $item['id'] ?>" class="form-label">Pilih File Bukti Transfer</label>
                                            <input class="form-control" type="file" id="bukti_transfer_<?= $item['id'] ?>" name="bukti_transfer" accept="image/*" required>
                                            <div class="form-text">Format yang diizinkan: JPG, JPEG, PNG, GIF. Maksimal 5MB.</div>
                                        </div>
                                        <div class="mb-3">
                                            <p><strong>Detail Transaksi:</strong></p>
                                            <p>ID Transaksi: <?= $item['id'] ?></p>
                                            <p>Total Bayar: <?= number_to_currency($item['total_harga'], 'IDR') ?></p>
                                            <p>Tanggal: <?= $item['created_at'] ?></p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Upload</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Upload Modal End -->
            <?php
                endforeach;
            endif;
            ?>
        </tbody>
    </table>
    <!-- End Table with stripped rows -->
</div>
<?= $this->endSection() ?>