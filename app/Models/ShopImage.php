<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopImage extends Model
{
    use HasFactory;

    // 編集可能な項目
    protected $fillable = [
        'shop_id',
        'image_path',
        'image_type',
    ];

    // 画像タイプの定数
    const TYPE_EXTERIOR = 'exterior';
    const TYPE_INTERIOR = 'interior';
    const TYPE_MENU = 'menu';
    const TYPE_ATMOSPHERE = 'atmosphere';

    // 利用可能な画像タイプ
    public static $availableTypes = [
        self::TYPE_EXTERIOR => '外観',
        self::TYPE_INTERIOR => '内装',
        self::TYPE_MENU => 'メニュー',
        self::TYPE_ATMOSPHERE => '雰囲気',
    ];

    // リレーションシップ
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
