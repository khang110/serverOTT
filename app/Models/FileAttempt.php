<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileAttempt extends Model
{
    use HasFactory;

    protected $fillable = ['origin_name', 'extension','stored_path'];
}
