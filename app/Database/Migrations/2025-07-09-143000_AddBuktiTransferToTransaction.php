<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBuktiTransferToTransaction extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transaction', [
            'bukti_transfer' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
                'after' => 'status'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transaction', 'bukti_transfer');
    }
}
