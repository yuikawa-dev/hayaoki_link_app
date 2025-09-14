<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    // 編集可能な項目
    protected $fillable = [
        'name',
        'description',
        'image_path',
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

    // 画像URLを取得
    public function getImageUrl()
    {
        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
            return asset('storage/' . $this->image_path);
        }
        return null;
    }

    // 画像が存在するかチェック
    public function hasImage()
    {
        return !empty($this->image_path) && Storage::disk('public')->exists($this->image_path);
    }

    // 検索用スコープ
    public function scopeSearchByName($query, $search)
    {
        if (!empty($search)) {
            return $query->where('name', 'like', '%' . $search . '%');
        }
        return $query;
    }

    public function scopeSearchByLocation($query, $location)
    {
        if (!empty($location)) {
            return $query->where('location', 'like', '%' . $location . '%');
        }
        return $query;
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_time', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('start_time', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeByFeeRange($query, $feeRange)
    {
        switch ($feeRange) {
            case 'free':
                return $query->where('fee', 0);
            case 'paid':
                return $query->where('fee', '>', 0);
            case 'low':
                return $query->whereBetween('fee', [1, 1000]);
            case 'medium':
                return $query->whereBetween('fee', [1001, 3000]);
            case 'high':
                return $query->where('fee', '>', 3000);
            default:
                return $query;
        }
    }
}
