<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use HasFactory, SoftDeletes;

    // 編集可能な項目
    protected $fillable = [
        'name',
        'description',
        'address',
        'contact',
        'sns_links',
        'opening_time',
        'closing_time',
    ];

    protected $casts = [
        'sns_links' => 'array',
        'opening_time' => 'datetime:H:i',
        'closing_time' => 'datetime:H:i',
    ];

    // リレーションシップ
    public function images()
    {
        return $this->hasMany(ShopImage::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    // 営業時間内かどうかをチェック
    public function isOpen()
    {
        $now = now()->format('H:i');
        return $now >= $this->opening_time && $now <= $this->closing_time;
    }

    // メイン画像を取得
    public function getMainImage()
    {
        return $this->images()->where('image_type', 'exterior')->first()
            ?? $this->images()->first();
    }

    // 朝から営業しているお店を取得するスコープ
    public function scopeMorningOpen($query, $morningTime = '08:00')
    {
        return $query->where('opening_time', '<=', $morningTime);
    }

    // 名前で検索するスコープ
    public function scopeSearchByName($query, $name)
    {
        if ($name) {
            return $query->where('name', 'like', '%' . $name . '%');
        }
        return $query;
    }

    // 住所で検索するスコープ
    public function scopeSearchByAddress($query, $address)
    {
        if ($address) {
            return $query->where('address', 'like', '%' . $address . '%');
        }
        return $query;
    }
}
