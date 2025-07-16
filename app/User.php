<?php

namespace App;

use App\Models\Commission;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\UserAccess;
use App\Models\UserGroup;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use Notifiable;
    use InteractsWithMedia;
    public $singleFile = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    function orders(){
        return $this->hasMany(Order::class);
    }

    function commissions(){
        return $this->hasMany(Commission::class);
    }

    function group(){
        return $this->belongsTo(UserGroup::class);
    }

    function commission(){
        return $this->hasMany(UserAccess::class);
    }

    function payment_history(){
        return $this->hasMany(Payment::class)->orderBy('id', RECORD_ORDER_BY);
    }

    public function children(){
        return $this->hasMany( User::class, 'parent_id', 'id' );
    }

    public function parent(){
        return $this->hasOne( User::class, 'id', 'parent_id' );
    }

    function balance(){
        return $this->hasMany(Transaction::class)->select('balance');
    }

    public function isOnline()
    {
        return Cache::has('user-is-online-'.$this->id);
    }


    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatar')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('thumb')
                    ->width(150)
                    ->height(150)
                    ->sharpen(10)
                    ->nonQueued();
            });
    }
}
