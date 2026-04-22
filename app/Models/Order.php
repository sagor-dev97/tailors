<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function orderDetail()
{
    return $this->hasOne(OrderDetail::class);
}
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function detail()
{
    return $this->hasOne(OrderDetail::class);
}

}
