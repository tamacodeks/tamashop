<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class CallingCard extends Model implements HasMedia
{

    use HasMediaTrait;
    public $singleFile = true;

    protected $fillable = ['telecom_provider_id','service_id','name','face_value','description','validity','access_number','buying_price','comment_1','comment_2','status','activate','number_of_cards','aleda_product_code','bimedia_product_code'];
    public function registerMediaCollections()
    {
        $this
            ->addMediaCollection('calling_cards')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('thumb')
                    ->width(250)
                    ->height(250)
                    ->sharpen(10)
                    ->nonQueued();
            });
    }

    function used_cards()
    {
        return $this->hasMany(CallingCardPin::class,'cc_id','id');
    }
}
