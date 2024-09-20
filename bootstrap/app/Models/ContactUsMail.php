<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUsMail extends Model
{
    use HasFactory;

    protected $table = '_contact_us_mail';

    protected $primaryKey = 'mailId';

    public $timestamps = true;

    protected $fillable = [
        'userName',
        'userPhone',
        'userEmail',
        'message',
    ];
}