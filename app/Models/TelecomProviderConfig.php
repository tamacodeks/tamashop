<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class TelecomProviderConfig extends Model implements HasMedia
{
    use HasMediaTrait;
    protected $table="telecom_providers_config";
    public $singleFile = true;
    protected $fillable = ['id','country_id','name','bimedia_card','status'];
    public function registerMediaCollections()
    {
        $this
            ->addMediaCollection('telecom_providers')
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
