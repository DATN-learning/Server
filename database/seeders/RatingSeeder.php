<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rating; // Đảm bảo đã import model Rating
use Faker\Factory as Faker;

class RatingSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $comments = [
            'Bài học rất hữu ích và dễ hiểu.',
        'Nội dung bài giảng rõ ràng, phong phú.',
        'Tôi thấy phần này cần giải thích thêm.',
        'Kiến thức nền khá vững chắc.',
        'Phần này thực sự khó hiểu, cần hỗ trợ thêm.',
        'Bài giảng quá nhanh, chưa kịp ghi chép.',
        'Tôi thích cách trình bày của bài học này.',
        'Nội dung rất dễ tiếp thu và thú vị.',
        'Bài học cung cấp nhiều kiến thức mới mẻ.',
        'Tôi cảm thấy phần này có thể rút ngắn lại để dễ hiểu hơn.',
        'Ví dụ minh họa rất sinh động và giúp tôi hiểu bài tốt hơn.',
        'Nội dung phù hợp với các kiến thức thực tế.',
        'Tôi đã học được nhiều điều bổ ích từ bài giảng này.',
        'Phần này cần bổ sung thêm tài liệu tham khảo.',
        'Tốc độ bài giảng vừa phải, dễ theo dõi.',
        'Có một số khái niệm khó hiểu, cần giải thích rõ hơn.',
        'Bài học có nhiều kiến thức sâu, rất bổ ích.',
        'Các ví dụ thực tế trong bài giảng rất ấn tượng.',
        'Tôi nghĩ bài giảng này cần thêm một số bài tập thực hành.',
        'Bài giảng rất thú vị và không gây nhàm chán.',
        'Nội dung bài giảng hơi dài, có thể rút ngắn lại một chút.',
        'Phần này rất phù hợp với nhu cầu học tập của tôi.',
        'Tôi đã áp dụng được nhiều điều từ bài học vào thực tế.',
        'Phần này cần có thêm video minh họa để dễ hiểu hơn.',
        'Cách giảng dạy rất sáng tạo và dễ tiếp thu.',
        'Bài học chứa nhiều nội dung bổ ích và chi tiết.',
        'Tôi gặp khó khăn trong việc hiểu một số phần của bài.',
        'Nội dung bài học rất sâu sắc và có tính ứng dụng cao.',
        'Bài giảng giúp tôi củng cố kiến thức đã học.',
        'Phần này hơi phức tạp nhưng rất đáng học.',
        'Bài giảng này rất hữu ích cho công việc của tôi.',
        'Có nhiều ví dụ thực tế, giúp dễ dàng áp dụng vào thực tế.',
        'Nội dung thú vị và mở rộng hiểu biết của tôi.',
        'Tôi rất thích cách bài giảng được trình bày trực quan.',
        'Phần này có một số điểm cần phải làm rõ hơn.',
        'Bài giảng quá nhanh, khó có thể nắm bắt toàn bộ.',
        'Tôi đánh giá cao các thông tin hữu ích trong bài học này.',
        'Tài liệu học rất phong phú và có hệ thống.',
        'Phần này giúp tôi hiểu sâu hơn về chủ đề này.',
        'Bài học khá hấp dẫn và dễ dàng tiếp thu.',
        ];
        foreach (range(1, 500) as $index) {
            Rating::create([
                'rating_id' => $faker->randomNumber(9),
                'user_id' => $faker->numberBetween(1, 9),
                'lesstion_chapter_id' => $faker->numberBetween(62, 79),
                'content' => $comments[array_rand($comments)],
                'rating' => $faker->numberBetween(1, 5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
