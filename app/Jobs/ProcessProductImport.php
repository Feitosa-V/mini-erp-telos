<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProductImportCompleted;
use Illuminate\Support\Facades\Storage;

class ProcessProductImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $filePath;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, $filePath)
    {
        $this->user = $user;
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $file = Storage::get($this->filePath);
        $rows = array_map('str_getcsv', explode("\n", $file));

        foreach ($rows as $row) {
            if (count($row) < 4) continue; // Ignorar linhas inválidas

            [$reference, $name, $color, $price] = $row;

            $supplier = Supplier::inRandomOrder()->first(); // Aqui pode ser ajustado para buscar um fornecedor específico

            Product::create([
                'supplier_id' => $supplier->id,
                'reference' => $reference,
                'name' => $name,
                'color' => $color,
                'price' => floatval($price),
                'uploaded_by' => $this->user->id,
            ]);
        }

        // Enviar email para o usuário
        Mail::to($this->user->email)->send(new ProductImportCompleted($this->user));

        // Deletar o arquivo após o processamento
        Storage::delete($this->filePath);
    }
}
