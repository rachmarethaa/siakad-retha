<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TiketResponse extends Model
{
    protected $table = 'tiket_responses';

    protected $fillable = [
        'tiket_mahasiswa_id',
        'sender_role',
        'sender_id',
        'message',
    ];

    public function tiket()
    {
        return $this->belongsTo(TiketMahasiswa::class, 'tiket_mahasiswa_id');
    }
}
