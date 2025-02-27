<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(2)->create();
        \App\Models\Supplier::factory(1)->create([
            'name' => 'Fornecedor Padrão',
            'cnpj' => '30.504.216/0001-58',
            'zip_code' => '37200-000',
            'address' => 'Rua Teste'
        ]);

        // Criar Produtos
        // \App\Models\Product::factory(20)->create();
        

        //Criar Usuários
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 1,
        ]);

        User::create([
            'name' => 'Seller User',
            'email' => 'seller@example.com',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'status' => 1,
        ]);
    }
}
