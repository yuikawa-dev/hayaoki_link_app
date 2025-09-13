<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'status',
    ];

    // 参加状態の定数
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';

    // 利用可能な参加状態
    public static $availableStatuses = [
        self::STATUS_PENDING => '申込中',
        self::STATUS_CONFIRMED => '参加確定',
        self::STATUS_CANCELLED => 'キャンセル',
    ];

    // リレーションシップ
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 参加確定済みかチェック
    public function isConfirmed()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    // キャンセル済みかチェック
    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    // 申込中かチェック
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }
}
