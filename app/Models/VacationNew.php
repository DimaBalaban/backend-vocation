<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacationNew extends Model
{
    use HasFactory;

    protected $table = 'vacation_new';

    protected $fillable = [
        'user_id',
        'image',
        'place_name',
        'description',
        'start_date',
        'end_date',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
