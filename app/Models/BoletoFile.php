<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoletoFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'file_hash',
    ];
}
