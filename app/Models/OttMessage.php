<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OttMessage extends Model
{
    use HasFactory;


    protected $fillable = ['sender', 'receiver', 'message', 'is_read'];
}
