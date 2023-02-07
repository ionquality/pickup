<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'CUSTOMER';
    protected $primaryKey = 'customer_id';
    protected $keyType = 'string';

    protected $fillable = [];
}
