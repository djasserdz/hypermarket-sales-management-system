<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $manager_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CashRegister> $cashRegister
 * @property-read int|null $cash_register_count
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\User $manager
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supermarket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supermarket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supermarket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supermarket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supermarket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supermarket whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supermarket whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supermarket whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Supermarket extends Model
{
    /** @use HasFactory<\Database\Factories\SupermarketFactory> */
    use HasFactory;

    protected $table = 'supermarkets';
    protected $fillable = [
        'name',
        'manager_id'
    ];

    public function cashRegister(): HasMany
    {
        return $this->hasMany(CashRegister::class, 'supermarket_id');
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class, 'supermarket_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'stocks', 'supermarket_id', 'product_id')->withPivot('quantity');
    }
    public function manager():BelongsTo{
        
        return $this->belongsTo(User::class,'manager_id');
    }
} 