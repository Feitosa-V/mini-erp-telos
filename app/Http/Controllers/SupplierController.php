<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::all();
        return Inertia::render('Suppliers/Index', [
            'suppliers' => $suppliers,
            'users' => User::where('role', 'seller')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => ['required', 'string', 'size:14', function ($attribute, $value, $fail) {
                if (!self::validateCNPJ($value)) {
                    $fail('CNPJ inválido.');
                }
            }],
            'zip_code' => 'required|string|size:8',
        ]);

        // Buscar endereço pelo ViaCEP
        $cepData = Http::get("https://viacep.com.br/ws/{$request->zip_code}/json/")->json();
        $address = $cepData['logradouro'] ?? 'Not found';

        $supplier = Supplier::create([
            'name' => $request->name,
            'cnpj' => $request->cnpj,
            'zip_code' => $request->zip_code,
            'address' => $address,
            'status' => $request->status ?? 1,
        ]);

        return response()->json([
            'message' => 'Fornecedor criado com sucesso!',
            'supplier' => $supplier, // Retorna o fornecedor recém-criado
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json(Supplier::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'name' => 'string|max:255',
            'cnpj' => "string|unique:suppliers,cnpj,$id",
            'zip_code' => 'string',
        ]);

        if ($request->has('zip_code')) {
            $cepData = Http::get("https://viacep.com.br/ws/{$request->zip_code}/json/")->json();
            $supplier->address = $cepData['logradouro'] ?? 'Not found';
        }

        $supplier->update($request->all());

        return response()->json($supplier);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Supplier::findOrFail($id)->delete();
        return response()->json(['message' => 'Fornecedor removido com Sucesso']);
    }

    private static function validateCNPJ($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        if (strlen($cnpj) !== 14) return false;

        // Algoritmo de validação do CNPJ
        for ($t = 12; $t < 14; $t++) {
            $d = 0;
            $c = 0;
            for ($m = $t - 8, $n = 0; $m < $t; $m++, $n++) {
                $d += $cnpj[$n] * $m;
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cnpj[$t] != $d) return false;
        }

        return true;
    }
}
