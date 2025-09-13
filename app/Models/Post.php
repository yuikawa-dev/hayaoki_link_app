<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    // 編集可能な項目
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'url',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // リレーションシップ
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    // 投稿に対するリアクションをしたユーザーを取得
    public function reactedUsers()
    {
        return $this->belongsToMany(User::class, 'reactions')
            ->withPivot('type')
            ->withTimestamps();
    }
}
