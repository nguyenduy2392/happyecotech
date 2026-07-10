<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Tin công ty', 'tin-cong-ty'],
            ['Sản phẩm', 'san-pham'],
            ['Kiến thức', 'kien-thuc'],
        ];

        $categoryModels = [];
        foreach ($categories as $i => [$name, $slug]) {
            $categoryModels[$slug] = PostCategory::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'sort_order' => $i],
            );
        }

        $tags = ['minh-bach', 'cong-nghe', 'truy-xuat-nguon-goc'];
        $tagModels = [];
        foreach ($tags as $slug) {
            $tagModels[$slug] = Tag::firstOrCreate(['slug' => $slug], ['name' => str_replace('-', ' ', $slug)]);
        }

        $author = User::whereHas('roles', fn ($q) => $q->where('name', 'SuperAdmin'))->first();

        // Một bài viết khởi tạo, nội dung trung tính, có thể sửa/xoá trong admin.
        $post = Post::firstOrCreate(
            ['slug' => 'chao-mung-den-voi-happy-ecotech'],
            [
                'title' => 'Chào mừng đến với Happy Ecotech',
                'excerpt' => 'Happy Ecotech xây dựng hệ sinh thái công nghệ minh bạch dữ liệu, số hóa kiểm định và kết nối doanh nghiệp với người tiêu dùng.',
                'content' => '<p>Happy Ecotech là đơn vị phát triển phần mềm với hệ sinh thái sản phẩm gồm SmartLabel, TaskFlow, MiniCRM và Farm2Home. Chúng tôi hướng tới sự minh bạch, chính trực và công nghệ vì con người.</p><p>Trang tin tức này sẽ cập nhật các thông tin về sản phẩm, hoạt động của công ty và kiến thức công nghệ.</p>',
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now(),
                'author_id' => $author?->id,
            ],
        );

        $post->categories()->syncWithoutDetaching([$categoryModels['tin-cong-ty']->id]);
        $post->tags()->syncWithoutDetaching([$tagModels['minh-bach']->id, $tagModels['cong-nghe']->id]);
    }
}
