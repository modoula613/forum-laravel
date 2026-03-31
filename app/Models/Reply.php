<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'user_id',
        'content',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(ReplyLike::class);
    }

    public function edits()
    {
        return $this->hasMany(ReplyEdit::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function reportsCount()
    {
        return $this->reports()->count();
    }

    public function bookmarkedBy()
    {
        return $this->belongsToMany(User::class, 'reply_bookmarks')->withTimestamps();
    }
}
