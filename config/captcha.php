<?php

return [
    'disable' => env('CAPTCHA_DISABLE', false),
    'characters' => ['2', '3', '4', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'm', 'n', 'p', 'q', 'r', 't', 'u', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'M', 'N', 'P', 'Q', 'R', 'T', 'U', 'X', 'Y', 'Z'],
    // 'default' => [
    //     'length' => 7,
    //     'width' => 140,
    //     'height' => 36,
    //     'quality' => 90,
    //     'math' => false,
    //     'expire' => 60,
    //     'encrypt' => false,
    // ],
    'numeric' => [
    'length' => 5,                       // 5 digit biar konsisten
    'width' => 160,
    'height' => 50,
    'quality' => 100,
    'characters' => ['0','1','2','3','4','5','6','7','8','9'],
    'lines' => 2,                        // cukup 2 garis pengganggu
    'bgImage' => false,                  // jangan pakai background gambar
    'bgColor' => '#ffffff',              // background putih bersih
    'fontColors' => [
        '#FF0000', '#007BFF', '#28A745', // merah, biru, hijau
        '#000000', '#6C757D'             // hitam, abu gelap
    ],                                   // warna font variatif tapi tetap kontras
    'contrast' => 0,                     // no effect
    'sharpen' => 0,                      // no effect
    'blur' => 0,                         // no blur biar tajam

],

    'math' => [
        'length' => 9,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'math' => true,
    ],

    'flat' => [
    'length' => 5,
    'width' => 220,
    'height' => 80,
    'quality' => 50,
    'lines' => 3,
    'bgImage' => true,
    'bgColor' => '#ffffff',
    'fontColors' => ['#000000'],
    'contrast' => 10,
    'sharpen' => 10,
    'blur' => 3,
    ],
    'mini' => [
        'length' => 3,
        'width' => 60,
        'height' => 32,
    ],
    'inverse' => [
        'length' => 5,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'sensitive' => true,
        'angle' => 12,
        'sharpen' => 10,
        'blur' => 2,
        'invert' => true,
        'contrast' => -5,
    ],
    'clear_numeric' => [
    'length' => 6,
    'width' => 150,
    'height' => 50,
    'quality' => 90,
    'characters' => ['0','1','2','3','4','5','6','7','8','9'],
    'lines' => 0,
    'bgImage' => false,
    'bgColor' => '#ffffff',
    'fontColors' => ['#000000'],
],

];
