<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;
use Dompdf\Dompdf;

class Cart extends BaseController
{
    protected $cart;
    protected $client;
    protected $apiKey;
    protected $transaction;
    protected $transaction_detail;

        function __construct()
        {
            helper('number');
            helper('form');
            $this->cart = \Config\Services::cart();
            $this->client = new \GuzzleHttp\Client();
            $this->apiKey = env('COST_KEY');
            $this->transaction = new TransactionModel();
            $this->transaction_detail = new TransactionDetailModel();
        }

        public function index()
        {
            $data['items'] = $this->cart->contents();
            $data['total'] = $this->cart->total();
            return view('v_cart', $data);
        }

        public function cart_add()
        {
            $this->cart->insert(array(
                'id'        => $this->request->getPost('id'),
                'qty'       => 1,
                'price'     => $this->request->getPost('harga'),
                'name'      => $this->request->getPost('nama'),
                'options'   => array('foto' => $this->request->getPost('foto'))
            ));
            session()->setflashdata('success', 'Produk berhasil ditambahkan ke Cart. (<a href="' . base_url() . 'cart">Lihat</a>)');
            return redirect()->to(base_url('/'));
        }

        public function cart_clear()
        {
            $this->cart->destroy();
            session()->setflashdata('success', 'Keranjang Berhasil Dikosongkan');
            return redirect()->to(base_url('cart'));
        }

        public function cart_edit()
        {
            $i = 1;
            foreach ($this->cart->contents() as $value) {
                $this->cart->update(array(
                    'rowid' => $value['rowid'],
                    'qty'   => $this->request->getPost('qty' . $i++)
                ));
            }

            session()->setflashdata('success', 'Keranjang Berhasil Diedit');
            return redirect()->to(base_url('cart'));
        }

        public function cart_delete($rowid)
        {
            $this->cart->remove($rowid);
            session()->setflashdata('success', 'Keranjang Berhasil Dihapus');
            return redirect()->to(base_url('cart'));
        }

        public function checkout()
        {
            $data['items'] = $this->cart->contents();
            $data['total'] = $this->cart->total();

            return view('v_checkout', $data);
        }
        public function getLocation()
        {
                //keyword pencarian yang dikirimkan dari halaman checkout
            $search = $this->request->getGet('search');

            $response = $this->client->request(
                'GET', 
                'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination?search='.$search.'&limit=50', [
                    'headers' => [
                        'accept' => 'application/json',
                        'key' => $this->apiKey,
                    ],
                ]
            );

            $body = json_decode($response->getBody(), true); 
            return $this->response->setJSON($body['data']);
        }

        public function getCost()
        { 
                //ID lokasi yang dikirimkan dari halaman checkout
            $destination = $this->request->getGet('destination');

                //parameter daerah asal pengiriman, berat produk, dan kurir dibuat statis
            //valuenya => 64999 : PEDURUNGAN TENGAH , 1000 gram, dan JNE
            $response = $this->client->request(
                'POST', 
                'https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                    'multipart' => [
                        [
                            'name' => 'origin',
                            'contents' => '64999'
                        ],
                        [
                            'name' => 'destination',
                            'contents' => $destination
                        ],
                        [
                            'name' => 'weight',
                            'contents' => '1000'
                        ],
                        [
                            'name' => 'courier',
                            'contents' => 'jne'
                        ]
                    ],
                    'headers' => [
                        'accept' => 'application/json',
                        'key' => $this->apiKey,
                    ],
                ]
            );

            $body = json_decode($response->getBody(), true); 
            return $this->response->setJSON($body['data']);
        }

    public function buy()
    {
        if ($this->request->getPost()) { 
            $dataForm = [
                'username' => $this->request->getPost('username'),
                'total_harga' => $this->request->getPost('total_harga'),
                'alamat' => $this->request->getPost('alamat'),
                'ongkir' => $this->request->getPost('ongkir'),
                'status' => 0,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $this->transaction->insert($dataForm);

            $last_insert_id = $this->transaction->getInsertID();

            foreach ($this->cart->contents() as $value) {
                $dataFormDetail = [
                    'transaction_id' => $last_insert_id,
                    'product_id' => $value['id'],
                    'jumlah' => $value['qty'],
                    'diskon' => 0,
                    'subtotal_harga' => $value['qty'] * $value['price'],
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ];

                $this->transaction_detail->insert($dataFormDetail);
            }

            $this->cart->destroy();
    
            return redirect()->to(base_url());
        }
    }

    public function getCartCount()
    {
        $cartCount = $this->cart->totalItems();
        
        return $this->response->setJSON([
            'success' => true,
            'count' => $cartCount
        ]);
    }

    public function updateStatus($id)
    {
        $status = $this->request->getPost('status');
        if ($this->transaction->updateStatus($id, $status)) {
            return redirect()->to(base_url('transaction'))->with('success', 'Status transaksi berhasil diperbarui.');
        } else {
            return redirect()->to(base_url('transaction'))->with('error', 'Gagal memperbarui status transaksi.');
        }
    }

    public function transaction()
    {
        $data['transactions'] = $this->transaction->findAll();
        return view('v_penjualan', $data);
    }

    public function downloadPDF()
    {
        // Get data from database
        $transactions = $this->transaction->findAll();
        
        // Pass data to PDF view
        $html = view('v_penjualanPDF', ['transactions' => $transactions]);
        
        // Set the PDF filename
        $filename = 'Laporan_Penjualan_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Instantiate and use the dompdf class
        $dompdf = new Dompdf();
        
        // Load HTML content
        $dompdf->loadHtml($html);
        
        // Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');
        
        // Render HTML as PDF
        $dompdf->render();
        
        // Output the generated PDF
        $dompdf->stream($filename, array("Attachment" => false));
    }

    public function downloadExcel()
    {
        // Get data from database
        $transactions = $this->transaction->findAll();
        
        // Set filename
        $filename = 'Laporan_Penjualan_' . date('Y-m-d_H-i-s') . '.csv';
        
        // Set headers for CSV download
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        
        // Create output stream
        $output = fopen('php://output', 'w');
        
        // Add BOM for proper UTF-8 encoding in Excel
        fwrite($output, "\xEF\xBB\xBF");
        
        // Add report header
        fputcsv($output, ['LAPORAN PENJUALAN']);
        fputcsv($output, ['Tanggal Cetak: ' . date('d F Y H:i:s')]);
        fputcsv($output, ['']);
        
        // Add summary information
        if (!empty($transactions)) {
            $totalPenjualan = array_sum(array_column($transactions, 'total_harga'));
            $totalOngkir = array_sum(array_column($transactions, 'ongkir'));
            $transaksiSelesai = count(array_filter($transactions, function($t) { return $t['status'] == '1'; }));
            $transaksiBelumSelesai = count($transactions) - $transaksiSelesai;
            
            fputcsv($output, ['RINGKASAN']);
            fputcsv($output, ['Total Transaksi', count($transactions)]);
            fputcsv($output, ['Total Penjualan', 'Rp ' . number_format($totalPenjualan, 0, ',', '.')]);
            fputcsv($output, ['Total Ongkir', 'Rp ' . number_format($totalOngkir, 0, ',', '.')]);
            fputcsv($output, ['Transaksi Selesai', $transaksiSelesai]);
            fputcsv($output, ['Transaksi Belum Selesai', $transaksiBelumSelesai]);
            fputcsv($output, ['']);
        }
        
        // CSV headers
        fputcsv($output, [
            'No',
            'ID Transaksi',
            'Username',
            'Total Harga',
            'Alamat',
            'Ongkir',
            'Status',
            'Tanggal Transaksi'
        ]);
        
        // Add data rows
        if (!empty($transactions)) {
            foreach ($transactions as $index => $item) {
                fputcsv($output, [
                    $index + 1,
                    $item['id'],
                    $item['username'],
                    'Rp ' . number_format($item['total_harga'], 0, ',', '.'),
                    $item['alamat'],
                    'Rp ' . number_format($item['ongkir'], 0, ',', '.'),
                    ($item['status'] == "1") ? "Sudah Selesai" : "Belum Selesai",
                    date('d/m/Y H:i', strtotime($item['created_at']))
                ]);
            }
        }
        
        fclose($output);
        exit;
    }
}
