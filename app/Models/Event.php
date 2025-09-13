<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'start_time',
        'end_time',
        'location',
        'requirements',
        'fee',
        'contact',
        'capacity',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'fee' => 'integer',
        'capacity' => 'integer',
    ];

    // リレーションシップ
    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_registrations')
            ->withPivot('status')
            ->withTimestamps();
    }

    // 参加確定者数を取得
    public function getConfirmedParticipantsCount()
    {
        return $this->registrations()
            ->where('status', EventRegistration::STATUS_CONFIRMED)
            ->count();
    }

    // 空き枠があるかチェック
    public function hasAvailableSlots()
    {
        return $this->getConfirmedParticipantsCount() < $this->capacity;
    }

    // イベントが終了しているかチェック
    public function isFinished()
    {
        return now() > $this->end_time;
    }

    // イベントが開始しているかチェック
    public function isStarted()
    {
        return now() > $this->start_time;
    }

    // イベント開催中かチェック
    public function isInProgress()
    {
        return $this->isStarted() && !$this->isFinished();
    }

    // 参加費を通貨形式で取得
    public function getFormattedFee()
    {
        return $this->fee > 0 ? '¥' . number_format($this->fee) : '無料';
    }
}
