<?php
$products[] = [
    [
        'name' => '【卒業記念品】時計 卒業 記念品 お祝い 名入れ 無料 卒団 卒部 部活 卒業式 卒園式 置き時計 木製 名前入れ メッセージ みんながつがる記念品kizuna',
        'price' => 4000,
        'shipping' => '送料〇〇円',
        'image' => 'https://item-shopping.c.yimg.jp/i/j/kinokurashi-gift_200-3?resolution=2x',
        'url' => '商品詳細URL',
        'store' => '店舗名',
        'store_url' => '店舗名URL'
    ],
];
$products[] = [
    [
        'name' => '【卒業記念品】時計 卒業 記念品 お祝い 名入れ 無料 卒団 卒部 部活 卒業式 卒園式 置き時計 木製 名前入れ メッセージ みんながつがる記念品kizuna',
        'price' => 4000,
        'shipping' => '送料〇〇円',
        'image' => 'https://item-shopping.c.yimg.jp/i/j/kinokurashi-gift_200-3?resolution=2x',
        'url' => '商品詳細URL',
        'store' => '店舗名',
        'store_url' => '店舗名URL'
    ],
];
session_start();
$_SESSION['products'] = $products;

header('Location: productSearchList.php');
?>