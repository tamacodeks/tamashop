<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class PrintCardTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($card)
    {
        $tp_config =  \App\Models\TelecomProvider::find($card->tp_id);
        $src_img = $tp_config->getMedia('telecom_providers_cards')->first();
        $img = !empty($src_img) ? asset(optional($src_img)->getUrl('thumb')) : asset('images/card_image.png');
        return [
            'card_id' => $card->cc_id,
            'name' => $card->name,
            'face_value' => $card->face_value,
            'image' => $img,
            'description' => $card->description,
            'validity' => $card->validity,
            'access_number' => $card->access_number,
            'comment_1' => $card->comment_1,
            'comment_2' => $card->comment_2,
            'pin_id' => $card->ccp_id,
//            'telecom_provider_id' => $card->tp_id
        ];
    }
}
