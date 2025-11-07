<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_divisi',
        'kode_divisi',
        'deskripsi'
    ];

    // Relasi: 1 Division punya banyak Projects
    public function projects()
    {
        return $this->hasMany(Project::class, 'pemilik_project_id');
    }

    /**
     * ✅ ACCESSOR: Display name dengan format "Nama Divisi (KODE)"
     * 
     * Usage: $division->display_name
     * Output: "Sumber Daya Manusia (SDM)"
     */
    public function getDisplayNameAttribute()
    {
        return $this->nama_divisi . ' (' . $this->kode_divisi . ')';
    }

    /**
     * ✅ ACCESSOR: Short display dengan kode saja
     * 
     * Usage: $division->short_name
     * Output: "SDM"
     */
    public function getShortNameAttribute()
    {
        return $this->kode_divisi;
    }
}