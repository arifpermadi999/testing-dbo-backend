<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{

    use SoftDeletes;

    public $table = 'products';

    protected $fillable = [
        'name',
        'price',
        'unit',
        'description',
        'created_by',
    ];

    public static $rules = [
        'name' => 'required|max:100',
        'price' => 'required|max:11|regex:/^(\d+(,\d{1,2})?)?$/',
        'unit' => 'required|max:20',

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
