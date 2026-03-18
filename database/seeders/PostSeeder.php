<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    public function run()
    {
        DB::table('posts')->insert([
            [
                'topic_id' => 1,
                'title' => 'Bài viết giới thiệu',
                'slug' => 'bai-viet-gioi-thieu',
                'image' => 'post1.jpg',
                'content' => '<p>Chào mừng bạn đến với website của chúng tôi! Đây là nền tảng thương mại điện tử hiện đại được xây dựng bằng Laravel, cung cấp các sản phẩm chất lượng với trải nghiệm mua sắm thuận tiện.</p><p>Chúng tôi cam kết mang đến cho khách hàng những sản phẩm tốt nhất với giá cả cạnh tranh và dịch vụ chăm sóc khách hàng tận tâm.</p>',
                'description' => 'Bài viết giới thiệu website thương mại điện tử',
                'post_type' => 'post',
                'created_at' => now(),
                'created_by' => 1,
                'status' => 1
            ],
            [
                'topic_id' => 1,
                'title' => 'Chính sách đổi trả',
                'slug' => 'chinh-sach-doi-tra',
                'image' => 'policy1.jpg',
                'content' => '<h3>Điều kiện đổi trả</h3><p>Sản phẩm được đổi trả trong vòng 7 ngày kể từ ngày nhận hàng với các điều kiện sau:</p><ul><li>Sản phẩm còn nguyên tem mác, bao bì</li><li>Sản phẩm không bị hư hỏng do người dùng</li><li>Còn đầy đủ phụ kiện đi kèm</li></ul><h3>Quy trình đổi trả</h3><p>Liên hệ hotline 1900-xxxx hoặc gửi email để được hướng dẫn chi tiết.</p>',
                'description' => 'Chi tiết chính sách đổi trả sản phẩm',
                'post_type' => 'post',
                'created_at' => now()->subDays(1),
                'created_by' => 1,
                'status' => 1
            ],
            [
                'topic_id' => 1,
                'title' => 'Hướng dẫn mua hàng',
                'slug' => 'huong-dan-mua-hang',
                'image' => 'guide1.jpg',
                'content' => '<h3>Cách đặt hàng</h3><p>Bước 1: Chọn sản phẩm bạn muốn mua</p><p>Bước 2: Thêm vào giỏ hàng</p><p>Bước 3: Điền thông tin giao hàng</p><p>Bước 4: Thanh toán và xác nhận đơn hàng</p><h3>Phương thức thanh toán</h3><p>Chúng tôi hỗ trợ thanh toán COD, chuyển khoản ngân hàng, và ví điện tử.</p>',
                'description' => 'Hướng dẫn chi tiết cách mua hàng online',
                'post_type' => 'post',
                'created_at' => now()->subDays(2),
                'created_by' => 1,
                'status' => 1
            ],
            [
                'topic_id' => 1,
                'title' => 'Tin tức công nghệ mới nhất',
                'slug' => 'tin-tuc-cong-nghe-moi-nhat',
                'image' => 'tech-news.jpg',
                'content' => '<p>Thế giới công nghệ đang phát triển với tốc độ chóng mặt. Từ trí tuệ nhân tạo đến điện thoại thông minh, chúng ta đang sống trong kỷ nguyên số.</p><p>Các công ty công nghệ hàng đầu như Apple, Google, và Microsoft đang liên tục đổi mới để mang đến những trải nghiệm tốt hơn cho người dùng.</p>',
                'description' => 'Cập nhật tin tức công nghệ và xu hướng mới nhất',
                'post_type' => 'post',
                'created_at' => now()->subDays(3),
                'created_by' => 1,
                'status' => 1
            ],
            [
                'topic_id' => 1,
                'title' => 'Khuyến mãi tháng 1/2026',
                'slug' => 'khuyen-mai-thang-1-2026',
                'image' => 'promo1.jpg',
                'content' => '<h3>Giảm giá lên đến 50%</h3><p>Nhân dịp đầu năm mới, chúng tôi dành tặng khách hàng chương trình khuyến mãi đặc biệt:</p><ul><li>Giảm 30% cho đơn hàng từ 500.000đ</li><li>Giảm 50% cho đơn hàng từ 1.000.000đ</li><li>Miễn phí vận chuyển toàn quốc</li></ul><p>Thời gian áp dụng: 01/01/2026 - 31/01/2026</p>',
                'description' => 'Thông tin khuyến mãi đặc biệt tháng 1/2026',
                'post_type' => 'post',
                'created_at' => now()->subDays(4),
                'created_by' => 1,
                'status' => 1
            ]
        ]);
    }
}
