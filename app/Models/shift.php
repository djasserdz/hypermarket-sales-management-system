<?php

namespace App\Models;

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
 * @property-read \App\Models\supermarket|null $supermarket
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|shift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|shift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|shift query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|shift whereCashRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|shift whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|shift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|shift whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|shift whereUserId($value)
 * @mixin \Eloquent
 */
class shift extends Model
{
    protected $table='shifts';
    protected $fillable=[
        'supermarket_id',
        'user_id',
    ];

    public function user():BelongsTo{
        return $this->belongsTo(User::class,'user_id');
    }
    public function supermarket():BelongsTo{
        return $this->belongsTo(supermarket::class,'supermarket_id');
    }
}
