<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $cash_register_id
 * @property string $start_at
 * @property string|null $end_at
 * @property-read \App\Models\CashRegister|null $cashRegister // Corrected related model type hint
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereCashRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereUserId($value)
 * @mixin \Eloquent
 */
class Shift extends Model
{
    use HasFactory;
    protected $table='shifts';
    protected $fillable=[
        'user_id',
        'cash_register_id',
        'start_at',
        'end_at',
    ];

    public function user():BelongsTo{
        return $this->belongsTo(User::class,'user_id');
    }
    public function cashRegister():BelongsTo{
        return $this->belongsTo(CashRegister::class,'cash_register_id');
    }
} 