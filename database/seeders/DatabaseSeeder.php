<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Menu;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Shop;
use App\Models\ShopImage;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 管理者ユーザーを作成
        User::factory()->create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // 通常ユーザーを10人作成
        User::factory(10)->create();

        // 投稿を作成（各ユーザーが2-5件の投稿を持つ）
        User::all()->each(function ($user) {
            Post::factory(fake()->numberBetween(2, 5))->create([
                'user_id' => $user->id,
            ]);
        });

        // リアクションを作成（各投稿に0-5件のリアクション）
        Post::all()->each(function ($post) {
            $users = User::inRandomOrder()->take(fake()->numberBetween(0, 5))->get();
            foreach ($users as $user) {
                Reaction::factory()->create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                ]);
            }
        });

        // コメントを作成（各投稿に0-3件のコメント）
        Post::all()->each(function ($post) {
            Comment::factory(fake()->numberBetween(0, 3))->create([
                'post_id' => $post->id,
                'user_id' => User::inRandomOrder()->first()->id,
            ]);
        });

        // 店舗を5件作成
        Shop::factory(5)->create()->each(function ($shop) {
            // 各店舗に2-4枚の画像
            ShopImage::factory(fake()->numberBetween(2, 4))->create([
                'shop_id' => $shop->id,
            ]);

            // 各店舗に3-7個のメニュー
            Menu::factory(fake()->numberBetween(3, 7))->create([
                'shop_id' => $shop->id,
            ]);
        });

        // イベントを3件作成
        Event::factory(3)->create()->each(function ($event) {
            // 各イベントに2-10人の参加者
            $users = User::inRandomOrder()->take(fake()->numberBetween(2, 10))->get();
            foreach ($users as $user) {
                EventRegistration::factory()->create([
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                ]);
            }
        });
    }
}
