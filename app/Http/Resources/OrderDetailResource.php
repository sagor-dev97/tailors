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
        ];
    }
}
