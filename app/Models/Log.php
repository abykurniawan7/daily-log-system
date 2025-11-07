<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Log extends Model
{
use HasFactory;


protected $fillable = [
'user_id','tanggal_input','due_date','nama_project','urgency',
'sifat_pekerjaan','deskripsi','pemilik_project','jenis_kegiatan',
'status','lampiran','locked'
];


public function user()
{
return $this->belongsTo(User::class);
}
}
