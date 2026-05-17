<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'order_id'            => $this->order_id,
            'single_hand_punjabi' => $this->single_hand_punjabi,
            'double_hand_punjabi' => $this->double_hand_punjabi,
            'punjabi'             => $this->punjabi,
            'fabric_qty'          => $this->fabric_qty,
            'fabric_price'        => $this->fabric_price,
            'labor_qty'           => $this->labor_qty,
            'labor_price'         => $this->labor_price,
            'design_qty'          => $this->design_qty,
            'design_price'        => $this->design_price,
            'button_qty'          => $this->button_qty,
            'button_price'        => $this->button_price,
            'embroidery_qty'      => $this->embroidery_qty,
            'embroidery_price'    => $this->embroidery_price,
            'courier_qty'         => $this->courier_qty,
            'due'                 => $this->due,
            'total'               => $this->total,
            'advance'             => $this->advance,
            'button_price'        => $this->button_price,
        ];
    }
}
