<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    // 編集可能な項目
    protected $fillable = [
        'shop_id',
        'name',
        'price',
        'description',
        'image_path',
    ];

    protected $casts = [
        'price' => 'integer',
    ];

    // リレーションシップ
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    // 税込価格を取得
    public function getPriceWithTax()
    {
        return (int)($this->price * 1.1);
    }

    // 価格を通貨形式で取得
    public function getFormattedPrice()
    {
        return '¥' . number_format($this->price);
    }

    // 税込価格を通貨形式で取得
    public function getFormattedPriceWithTax()
    {
        return '¥' . number_format($this->getPriceWithTax());
    }
}
