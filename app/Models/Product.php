<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id', 'reference','name','color','price',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('unit_price', 'quantity');
    }

    public function supplier()
{
    return $this->belongsTo(Supplier::class);
}
}
