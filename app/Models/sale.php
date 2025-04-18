<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class sale extends Model
{
    /** @use HasFactory<\Database\Factories\SaleFactory> */
    use HasFactory;

    protected $table = 'sales';

    protected $fillable = [
        'payment_method',
    ];

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(cashRegister::class, 'cash_register_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(product::class, 'sale_items', 'sale_id', 'product_id')->withPivot('quantity');
    }
}
