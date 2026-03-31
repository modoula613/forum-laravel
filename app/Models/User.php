<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'warning_count',
        'is_blocked',
        'is_banned',
        'banned_until',
        'level',
        'experience',
        'reputation',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'warning_count' => 'integer',
            'is_blocked' => 'boolean',
            'is_banned' => 'boolean',
            'banned_until' => 'datetime',
            'level' => 'integer',
            'experience' => 'integer',
            'reputation' => 'integer',
        ];
    }

    public function addExperience($points)
    {
        $this->experience += $points;

        while ($this->experience >= ($this->level * 100)) {
            $this->experience -= $this->level * 100;
            $this->level++;
        }

        $this->save();
    }

    public function addReputation($points)
    {
        $this->reputation += $points;
        $this->save();
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function likedReplies()
    {
        return $this->hasMany(ReplyLike::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function unreadMessages()
    {
        return $this->receivedMessages()->where('is_read', false);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class);
    }

    public function followedTags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function bookmarkedReplies()
    {
        return $this->belongsToMany(Reply::class, 'reply_bookmarks')->withTimestamps();
    }

    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    public function adminLogs()
    {
        return $this->hasMany(AdminLog::class, 'admin_id');
    }

    public function topicsCount()
    {
        return $this->topics()->count();
    }

    public function repliesCount()
    {
        return $this->replies()->count();
    }
}
