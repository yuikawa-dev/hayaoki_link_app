<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'type',
    ];

    // リアクションタイプの定数
    const TYPE_LIKE = 'like';
    const TYPE_SUPPORT = 'support';
    const TYPE_CHEER = 'cheer';

    // 利用可能なリアクションタイプ
    public static $availableTypes = [
        self::TYPE_LIKE => 'いいね',
        self::TYPE_SUPPORT => '応援',
        self::TYPE_CHEER => '頑張れ',
    ];

    // リレーションシップ
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
