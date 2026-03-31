<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplyEdit extends Model
{
    use HasFactory;

    protected $fillable = [
        'reply_id',
        'old_content',
    ];

    public function reply()
    {
        return $this->belongsTo(Reply::class);
    }
}
