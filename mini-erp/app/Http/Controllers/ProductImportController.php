<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductImportRequest;
use App\Jobs\ProcessProductImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImportController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $path = $request->file('file')->store('imports'); // Salva o arquivo no storage

        // Dispara um Job para processar o arquivo em background
        ProcessProductImport::dispatch(auth()->user(), $path);

        return response()->json(['message' => 'Upload iniciado! Você receberá um email ao final.']);
    }
}
