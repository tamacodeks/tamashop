<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class CallingcardTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($card)
    {
        $tp_config =  \App\Models\TelecomProvider::find($card->id);
        $src_img = $tp_config->getMedia('telecom_providers_cards')->first();
        $img = !empty($src_img) ? asset(optional($src_img)->getUrl()) : asset('images/card_image.png');
        return [
            'card_id' => $card->id,
            'name' => $card->name,
            'face_value' => $card->face_value,
            'image' => $img,
            'description' => $card->description
        ];
    }
}
