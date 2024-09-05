<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class game_version extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function score()
    {
        return $this->hasMany(Score::class );
    }
}
