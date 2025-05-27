<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierOrder extends Model
{
    use HasFactory;

    protected $table = 'supplier_orders';

    protected $fillable = [
        'product_id',
        'supplier_id',
        'supermarket_id',
        'quantity_ordered',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => 'string', // Ensures status is treated as a string
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function supermarket(): BelongsTo
    {
        return $this->belongsTo(Supermarket::class, 'supermarket_id');
    }
} 