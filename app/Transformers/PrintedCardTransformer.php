<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class PrintedCardTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($card)
    {
        return [
            'card_name' => $card->card_name,
            'face_value' => $card->face_value,
            'pin' => $card->pin,
            'serial' => $card->serial,
            'timestamp' => $card->time_printed,
            'validity' => isset($card->validity) ? $card->validity : ""
        ];
    }
}
