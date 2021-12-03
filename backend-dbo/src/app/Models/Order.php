<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{

    public $table = 'orders';
    const INV_PREV = "INV";

    protected $fillable = [
        'invoice_no',
        'customer_id',
        'order_date',
        'created_by',
        'payment_method',
        'status'
    ];

    public static $rules = [
        'customer_id' => 'required|exists:customers,id',
        'order_date' => 'required|date',
        'payment_method' => 'required|in:CASH,TRANSFER',
        'status' => 'required|in:0,1',
        'created_by' => 'exists:users,id',
        'updated_by' => 'exists:users,id',
    ];

    public static $messages_rules = [
        'payment_method.in' => 'Payment method is only CASH or TRANSFER',
        'status.in' => 'Status is only 0 or 1',
    ];


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model['invoice_no'] = Order::generateInvoiceNumber();
            $model['created_by'] = \JWTAuth::parseToken()->authenticate()->id;
            return $model;
        });
        static::updating(function ($model) {
            return $model['updated_by'] = \JWTAuth::parseToken()->authenticate()->id; 
        });
    }

    public function order_details(){
        return $this->hasMany(\App\Models\OrderDetail::class, 'order_id','id');
    }
    public function customer(){
        return $this->belongsTo(\App\Models\Customer::class, 'customer_id','id')->withTrashed();
    }
    
    public static function generateInvoiceNumber(){
        $data = Order::where('invoice_no','like','%'.date('Ymd').'%')->latest()->first();
        if(!$data){
            return Order::INV_PREV.date('Ymd').'0001';
        }
        return ++$data->invoice_no;
    }

}
