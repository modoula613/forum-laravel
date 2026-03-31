<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category_id',
        'is_draft',
        'is_locked',
        'is_pinned',
    ];

    protected static function booted(): void
    {
        static::creating(function (Topic $topic) {
            if (! $topic->slug) {
                $baseSlug = Str::slug($topic->title) ?: 'sujet';
                $topic->slug = $baseSlug.'-'.Str::lower(Str::random(6));
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_draft' => 'boolean',
            'is_locked' => 'boolean',
            'is_pinned' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function edits()
    {
        return $this->hasMany(TopicEdit::class);
    }
}
