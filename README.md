setelah migration berhasil import backup data terbaru dulu, setelah itu jalankan perintah berikut:

1. Buat copas column path img_produk lama ke tempat baru:

    INSERT INTO `product_images` (`product_id`, `path`, `is_primary`, `created_at`, `updated_at`)
    SELECT `id`, `img_produk`, 1, NOW(), NOW()
    FROM `products`
    WHERE `img_produk` IS NOT NULL AND `img_produk` != '';

2. hapus column img_produk lama

    ALTER TABLE `products` DROP COLUMN `img_produk`;