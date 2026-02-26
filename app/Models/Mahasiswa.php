<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\NilaiMahasiswa;
use App\Models\Presensi;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $nim
 * @property string|null $email_verified_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Mahasiswa extends Authenticatable
{
    use Notifiable;

    protected $table = 'mahasiswa';

    protected $fillable = ['name', 'email', 'password', 'nim', 'prodi'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function nilai()
    {
        return $this->hasMany(NilaiMahasiswa::class, 'mahasiswa_id');
    }

    public function krs()
    {
        return $this->hasMany(KRS::class, 'mahasiswa_id');
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'mahasiswa_id');
    }
}
