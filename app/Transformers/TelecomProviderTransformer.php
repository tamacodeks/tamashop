<?php

namespace App\Transformers;

use App\Models\TelecomProviderConfig;
use League\Fractal\TransformerAbstract;

class TelecomProviderTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($tp)
    {
        $tp_config =  TelecomProviderConfig::find($tp->id);
        $src_img = $tp_config->getMedia('telecom_providers')->first();
        $img = !empty($src_img) ? asset(optional($src_img)->getUrl('thumb')) : asset('images/card_image.png');
        return [
            'provider_id' => $tp->id,
            'name' => $tp->name,
            'image' =>$img
        ];
    }
}
