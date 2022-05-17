<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WossopMessage extends Model
{
    use HasFactory;


    protected $fillable = ['id','sender', 'receiver', 'message', 'is_read','is_file','is_group'];
}
