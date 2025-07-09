<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transaction';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'username', 'total_harga', 'alamat', 'ongkir', 'status', 'bukti_transfer', 'created_at', 'updated_at'
    ];
    
    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }
}