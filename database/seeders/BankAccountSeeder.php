<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bankAccounts = [
            [
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
                'account_holder_name' => 'Kafkot Reserve',
                'is_active' => true,
                'is_primary' => true,
                'notes' => 'Rekening utama untuk pembayaran reservasi',
            ],
            [
                'bank_name' => 'Mandiri',
                'account_number' => '0987654321',
                'account_holder_name' => 'Kafkot Reserve',
                'is_active' => true,
                'is_primary' => false,
                'notes' => 'Rekening alternatif',
            ],
        ];

        foreach ($bankAccounts as $account) {
            BankAccount::create($account);
        }
    }
}
