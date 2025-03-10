<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacationLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'vacation_id',
        'user_id',
    ];


    public function vacation()
    {
        return $this->belongsTo(Vacation::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
