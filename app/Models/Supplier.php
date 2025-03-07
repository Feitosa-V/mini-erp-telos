<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'cnpj','zip_code','address','status'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'supplier_user');
    }
}
