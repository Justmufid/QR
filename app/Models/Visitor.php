<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{

    protected $fillable = ['id_conference', 'name', 'email', 'affiliation', 'qr_code'];
    

    
}

