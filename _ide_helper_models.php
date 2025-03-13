<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string|null $role
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\cashRegister> $cashRegister
 * @property-read int|null $cash_register_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $supermarket_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\sale> $sales
 * @property-read int|null $sales_count
 * @property-read \App\Models\supermarket $supermarket
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $user
 * @property-read int|null $user_count
 * @method static \Database\Factories\cashRegisterFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|cashRegister newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|cashRegister newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|cashRegister query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|cashRegister whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|cashRegister whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|cashRegister whereSupermarketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|cashRegister whereUpdatedAt($value)
 */
	class cashRegister extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\product> $products
 * @property-read int|null $products_count
 * @method static \Database\Factories\categorieFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|categorie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|categorie newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|categorie query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|categorie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|categorie whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|categorie whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|categorie whereUpdatedAt($value)
 */
	class categorie extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $supermarket_id
 * @property string $street_name
 * @property string $state
 * @property float $latitude
 * @property float $longitude
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\supermarket $supermarket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|location newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|location query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|location whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|location whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|location whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|location whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|location whereStreetName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|location whereSupermarketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|location whereUpdatedAt($value)
 */
	class location extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $barcode
 * @property string $price
 * @property int $category_id
 * @property int $supplier_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\categorie $categorie
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\stock> $stock
 * @property-read int|null $stock_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\supermarket> $supermarket
 * @property-read int|null $supermarket_count
 * @property-read \App\Models\supplier $supplier
 * @method static \Database\Factories\productFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|product whereBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|product whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|product whereUpdatedAt($value)
 */
	class product extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $cash_register_id
 * @property string $payment_method
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\cashRegister $cashRegister
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\product> $products
 * @property-read int|null $products_count
 * @method static \Database\Factories\saleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|sale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|sale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|sale query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|sale whereCashRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|sale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|sale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|sale wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|sale whereUpdatedAt($value)
 */
	class sale extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $supermarket_id
 * @property int $product_id
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\product $product
 * @property-read \App\Models\supermarket $supermarket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|stock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|stock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|stock query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|stock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|stock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|stock whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|stock whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|stock whereSupermarketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|stock whereUpdatedAt($value)
 */
	class stock extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\cashRegister> $cashRegister
 * @property-read int|null $cash_register_count
 * @property-read \App\Models\location|null $location
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\product> $products
 * @property-read int|null $products_count
 * @method static \Database\Factories\supermarketFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|supermarket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|supermarket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|supermarket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|supermarket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|supermarket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|supermarket whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|supermarket whereUpdatedAt($value)
 */
	class supermarket extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $phone_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\product> $products
 * @property-read int|null $products_count
 * @method static \Database\Factories\supplierFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|supplier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|supplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|supplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|supplier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|supplier wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|supplier whereUpdatedAt($value)
 */
	class supplier extends \Eloquent {}
}

