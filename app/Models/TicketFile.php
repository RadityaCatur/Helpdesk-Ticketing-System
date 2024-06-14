<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TicketFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'filename',
        'extension'
    ];

    protected $appends = [
        'fileUrl'
    ];

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }

    public function getFileUrlAttribute() {
        return Storage::url('uploads/' . $this->filename);
    }
}
