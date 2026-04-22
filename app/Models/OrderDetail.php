<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
    'order_id',

    'single_hand_punjabi',
    'double_hand_punjabi',
    'punjabi',
    'arabian_jubba',
    'kabli',
    'fatwa',
    'salwar',
    'pajama',
    'punjabi_pajama',

    'chest_pocket',
    'collar_button',
    'double_stitch',
    'front_button',
    'side_cut',

    'back_pocket',
    'front_button_pocket',
    'single_pocket_design',
    'double_pocket_design',

    'length',
    'body',
    'belly',
    'sleeves',
    'neck',
    'shoulder',
    'cuff',
    'hip',

    'bottom_length',
    'natural',
    'waist',
    'hi',
    'run',

    'fabric_qty',
    'fabric_price',
    'labor_qty',
    'labor_price',
    'design_qty',
    'design_price',
    'button_qty',
    'button_price',
    'embroidery_qty',
    'embroidery_price',
    'courier_qty',
    'courier_price',

    'total',
    'advance',
    'due',

    'note',

     'botam_no',
    'metal_botam_no',
    'isnaf_botam_no',
    'tira',
    'serowani_kolar',
    'band_kolar',
    'shirt_kolar',

    'book_pocket',
    'book_pocket_sticker',
    'two_pack_ring',
    'kof_hand',
    'koflin_hand',
    'kolar_black_sticker',
    'koflin_hand_pocket',
    'koflin_hand_pocket_sticker',
    'koflin_hand_kolar',
];

}
