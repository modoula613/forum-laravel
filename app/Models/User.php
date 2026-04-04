<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    public function followingUsers()
    {
        return $this->belongsToMany(User::class, 'user_follows', 'follower_id', 'followed_id')->withTimestamps();
    }

    public function followerUsers()
    {
        return $this->belongsToMany(User::class, 'user_follows', 'followed_id', 'follower_id')->withTimestamps();
    }

    public function sentFollowRequests()
    {
        return $this->belongsToMany(User::class, 'follow_requests', 'requester_id', 'requested_id')->withTimestamps();
    }

    public function receivedFollowRequests()
    {
        return $this->belongsToMany(User::class, 'follow_requests', 'requested_id', 'requester_id')->withTimestamps();
    }

    public function isFollowing(User $user): bool
    {
        return $this->followingUsers()->where('users.id', $user->id)->exists();
    }

    public function isFollowedBy(User $user): bool
    {
        return $this->followerUsers()->where('users.id', $user->id)->exists();
    }

    public function isFriendWith(User $user): bool
    {
        return $this->isFollowing($user) && $this->isFollowedBy($user);
    }

    public function hasPendingFollowRequestTo(User $user): bool
    {
        return $this->sentFollowRequests()->where('users.id', $user->id)->exists();
    }

    public function hasPendingFollowRequestFrom(User $user): bool
    {
        return $this->receivedFollowRequests()->where('users.id', $user->id)->exists();
    }

    public function friendsCount(): int
    {
        return $this->followingUsers()
            ->whereIn('users.id', $this->followerUsers()->select('users.id'))
            ->count();
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

    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->buildProfilePhotoDataUri(),
        );
    }

    protected function buildProfilePhotoDataUri(): string
    {
        $seed = abs(crc32(strtolower($this->email.'|'.$this->name)));

        $backgrounds = [
            ['#f5e9ff', '#c084fc'],
            ['#ede9fe', '#8b5cf6'],
            ['#fdf2f8', '#ec4899'],
            ['#ecfeff', '#14b8a6'],
            ['#eff6ff', '#3b82f6'],
            ['#fef3c7', '#f59e0b'],
        ];
        $skins = ['#f5c7a9', '#e9b391', '#d89b74', '#bf7e56'];
        $hairs = ['#2b1d16', '#4a2f27', '#6b3e2e', '#2f2739', '#5b4636'];
        $shirts = ['#8b5cf6', '#0f766e', '#2563eb', '#be185d', '#ea580c', '#1f2937'];
        $accents = ['#ffffff', '#e9d5ff', '#fde68a', '#bfdbfe'];

        [$bgStart, $bgEnd] = $backgrounds[$seed % count($backgrounds)];
        $skin = $skins[intdiv($seed, 7) % count($skins)];
        $hair = $hairs[intdiv($seed, 13) % count($hairs)];
        $shirt = $shirts[intdiv($seed, 17) % count($shirts)];
        $accent = $accents[intdiv($seed, 19) % count($accents)];
        $eyeTone = intdiv($seed, 23) % 2 === 0 ? '#2b211d' : '#3a302b';
        $hairStyle = $seed % 3;

        $hairPath = match ($hairStyle) {
            0 => '<path d="M25 46c1-14 12-25 31-25s30 11 31 25c-4-5-10-8-16-8-2 0-5 0-8 1-4-6-11-9-18-9-8 0-15 3-20 9-3-1-5-1-8-1-5 0-9 2-12 8Z" fill="'.$hair.'"/>',
            1 => '<path d="M22 48c3-16 16-28 34-28 17 0 31 11 34 28-6-4-11-5-16-5-3 0-6 0-10 1-4-5-10-8-18-8-8 0-15 3-20 8-3-1-6-1-9-1-6 0-11 2-15 5Z" fill="'.$hair.'"/>',
            default => '<path d="M24 47c2-15 14-27 32-27 18 0 31 12 32 27-4-3-8-5-13-5-3 0-7 0-10 1-5-5-11-8-19-8s-15 3-20 8c-3-1-6-1-9-1-5 0-9 2-13 5Z" fill="'.$hair.'"/>',
        };

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96" fill="none">
  <defs>
    <linearGradient id="bg" x1="12" y1="10" x2="82" y2="88" gradientUnits="userSpaceOnUse">
      <stop stop-color="{$bgStart}"/>
      <stop offset="1" stop-color="{$bgEnd}"/>
    </linearGradient>
  </defs>
  <rect width="96" height="96" rx="28" fill="url(#bg)"/>
  <circle cx="74" cy="22" r="14" fill="{$accent}" opacity=".35"/>
  <path d="M18 96c2-18 15-28 30-28s28 10 30 28H18Z" fill="{$shirt}"/>
  <path d="M41 62c0 5 3 10 7 10s7-5 7-10v-6H41v6Z" fill="{$skin}"/>
  <ellipse cx="48" cy="45" rx="19" ry="22" fill="{$skin}"/>
  {$hairPath}
  <path d="M31 49c1-13 9-23 17-23s16 10 17 23c-3-2-7-3-10-3-5 0-10 2-14 6-4-4-8-6-13-6-2 0-4 0-6 3Z" fill="{$hair}" opacity=".92"/>
  <circle cx="41" cy="46" r="2.2" fill="{$eyeTone}"/>
  <circle cx="55" cy="46" r="2.2" fill="{$eyeTone}"/>
  <path d="M41 56c2 2 4 3 7 3s5-1 7-3" stroke="#8a4b39" stroke-linecap="round" stroke-width="2.2"/>
  <path d="M33 74c4-5 9-8 15-8s11 3 15 8" stroke="{$accent}" stroke-opacity=".55" stroke-linecap="round" stroke-width="3"/>
</svg>
SVG;

        return 'data:image/svg+xml;charset=UTF-8,'.rawurlencode($svg);
    }
}
