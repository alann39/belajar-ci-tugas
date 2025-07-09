<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;

class Home extends BaseController
{
    protected $product;
    protected $transaction;
    protected $transaction_detail;

    public function __construct()
    {
        helper('number');
        helper('form');
        $this->product = new ProductModel();
        $this->transaction = new TransactionModel();
        $this->transaction_detail = new TransactionDetailModel();
    }

    public function index()
    {
        $products = $this->product->findAll();
        $data = [
            'products' => $products,
        ];
        return view('v_home', $data);
    }

    public function profile()
    {
        $username = session()->get('username');
        $data['username'] = $username;

        $buy = $this->transaction->where('username', $username)->findAll();
        $data['buy'] = $buy;

        $product = [];

        if (!empty($buy)) {
            foreach ($buy as $item) {
                $detail = $this->transaction_detail->select('transaction_detail.*, product.nama, product.harga, product.foto')->join('product', 'transaction_detail.product_id=product.id')->where('transaction_id', $item['id'])->findAll();

                if (!empty($detail)) {
                    $product[$item['id']] = $detail;
                }
            }
        }

        $data['product'] = $product;

        return view('v_profile', $data);
    }

    public function uploadBuktiTransfer()
    {
        $transactionId = $this->request->getPost('transaction_id');
        $file = $this->request->getFile('bukti_transfer');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau tidak ditemukan');
        }

        // Validasi file
        $validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($file->getMimeType(), $validTypes)) {
            return redirect()->back()->with('error', 'Hanya file gambar yang diizinkan (JPEG, JPG, PNG, GIF)');
        }

        // Maksimal 5MB
        if ($file->getSize() > 5 * 1024 * 1024) {
            return redirect()->back()->with('error', 'Ukuran file maksimal 5MB');
        }

        // Generate nama file unik
        $newName = 'bukti_' . $transactionId . '_' . time() . '.' . $file->getExtension();
        
        // Pastikan direktori upload ada
        $uploadPath = FCPATH . 'assets/images/bukti_transfer/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Pindahkan file
        if ($file->move($uploadPath, $newName)) {
            // Update database
            $this->transaction->update($transactionId, ['bukti_transfer' => $newName]);
            return redirect()->back()->with('success', 'Bukti transfer berhasil diupload');
        } else {
            return redirect()->back()->with('error', 'Gagal mengupload file');
        }
    }
}
