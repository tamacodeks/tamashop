<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class TelecomProvider extends Model implements HasMedia
{
    use HasMediaTrait;
    public $singleFile = true;
    protected $fillable = ['id','tp_config_id','name','description','face_value','bimedia_card','is_card','status','ordering'];
    public function registerMediaCollections()
    {
        $this
            ->addMediaCollection('telecom_providers_cards')
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
