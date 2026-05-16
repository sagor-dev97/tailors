<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'user_id'         => $this->user_id,
            'customer_id'     => $this->customer_id,
            'receiver'        => $this->receiver,
            'order_number'    => $this->order_number,
            'order_date'      => $this->order_date,
            'delivery_date'   => $this->delivery_date,
            'status'          => $this->status,
            'is_reorder'      => $this->is_reorder,
            'parent_order_id' => $this->parent_order_id,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,

            'detail'   => new OrderDetailResource($this->whenLoaded('detail')),
            'user'     => new UserResource($this->whenLoaded('user')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
        ];
    }
}
