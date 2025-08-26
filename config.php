<?php
// config.php - إعدادات بسيطة لمشروع المتجر
return [
    // قائمة الإيميلات المسموح لها بإضافة المنتجات
    'allowed_emails' => [
        'youremail@gmail.com',
        // أضف هنا الإيميلات اللي تسمح لهم تَنشِر منتجات
    ],

    // إعداد قاعدة البيانات (لو بدك تربط منتجات للداتابيز لاحقاً)
    'db' => [
        'host' => 'localhost',
        'name' => 'store_db',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
];
