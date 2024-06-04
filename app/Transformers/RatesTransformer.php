<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class RatesTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($rates)
    {
        return [
            'card_id' => $rates->id,
            'card_name' => $rates->name,
            'description' => $rates->description,
            'face_value' => $rates->face_value,
            'sale_price' => $rates->sale_price
        ];
    }
}
