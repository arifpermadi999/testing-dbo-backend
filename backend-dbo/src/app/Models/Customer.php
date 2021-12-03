<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{

    use SoftDeletes;

    public $table = 'customers';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'province',
        'city',
        'zip_code',
        'address',
    ];

    public static $rules = [
        'name' => 'required|max:100',
        'email' => 'required|max:50|email|unique:customers,email',
        'phone' => 'required|max:13',
        'province' => 'required|max:50',
        'city' => 'required|max:50',
        'zip_code' => 'required|max:10',
        'address' => 'required',
    ];
    public static $rules_update = [
        'name' => 'required|max:100',
        'email' => 'required|max:50|email',
        'phone' => 'required|max:13',
        'province' => 'required|max:50',
        'city' => 'required|max:50',
        'zip_code' => 'required|max:10',
        'address' => 'required',

        'created_by' => 'exists:users,id',
        'updated_by' => 'exists:users,id',
        'deleted_by' => 'exists:users,id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            return $model['created_by'] = \JWTAuth::parseToken()->authenticate()->id;
        });
        static::updating(function ($model) {
            return $model['updated_by'] = \JWTAuth::parseToken()->authenticate()->id; 
        });
    }
}
