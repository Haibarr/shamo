<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class transaction extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'users_id',
        'address',
        'payment',
        'total_price',
        'shipping_pricce',
        'status',

    ];

    public function user(){
        return $this->belongsTo(User::class,'users_id','id');
    }

    public function items(){
        return $this->hasMany(TransactionItem::class,'id','transaction_id');
    }
}
