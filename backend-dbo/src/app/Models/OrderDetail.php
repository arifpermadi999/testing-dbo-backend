<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    public $timestamps = false;

    public $table = 'order_details';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_price',
        'product_qty',
    ];

    public static $rules = [
        'product_id' => 'required|exists:products,id',
        'product_qty' => 'required|max:5|regex:/^(\d+(,\d{1,2})?)?$/',
    ];

    public function products(){
        return $this->hasMany(\App\Models\Product::class, 'id','product_id')->withTrashed();
    }
}
