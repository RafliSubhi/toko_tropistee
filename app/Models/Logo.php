<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logo extends Model
{
    protected $table = 'logo_dan_favicon';
    protected $fillable = ['logo_path', 'favicon_path'];
}
