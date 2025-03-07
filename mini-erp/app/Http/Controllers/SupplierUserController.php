<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Supplier;

class SupplierUserController extends Controller
{
    public function attachUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        $supplier = Supplier::findOrFail($request->supplier_id);
        $supplier->users()->syncWithoutDetaching([$request->user_id]);

        return response()->json(['message' => 'Usuário vinculado com sucesso!']);
    }

    public function detachUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        $supplier = Supplier::findOrFail($request->supplier_id);
        $supplier->users()->detach($request->user_id);

        return response()->json(['message' => 'Usuário desvinculado com sucesso!']);
    }
}
