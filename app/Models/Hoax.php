<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hoax extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;
    protected $table = 'hoax';
    protected $primaryKey = 'id_hoax';
    protected $fillable = ['hoax'];
}
