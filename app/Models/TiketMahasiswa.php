<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mahasiswa;
use App\Models\Staff;

class TiketMahasiswa extends Model
{
    protected $table = 'tiket_mahasiswa';

    protected $casts = [
        'sla_deadline' => 'datetime',
        'processed_at' => 'datetime',
        'responded_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    protected $fillable = [
        'mahasiswa_id',
        'judul',
        'keluhan',
        'assigned_staff_id',
        'assigned_dosen_id',
        'status',
        'sla_deadline',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'assigned_staff_id');
    }

    public function dosen()
    {
        return $this->belongsTo(\App\Models\Dosen::class, 'assigned_dosen_id');
    }

    public function responses()
    {
        return $this->hasMany(\App\Models\TiketResponse::class, 'tiket_mahasiswa_id')->orderBy('created_at','asc');
    }

    /**
     * Determine if the tiket is expired based on SLA deadline or explicit expired_at.
     */
    public function isExpired()
    {
        if ($this->expired_at) {
            return true;
        }

        if ($this->sla_deadline && \Carbon\Carbon::now()->greaterThan($this->sla_deadline)) {
            // If ticket hasn't been responded (or closed), treat as expired
            return $this->status !== 'ditanggapi';
        }

        return false;
    }

}
