<?php
session_start();

// 商品一覧ページから送信された選択された商品のIDを取得
if (isset($_POST['product'])) {
    $_SESSION['selected_products'] = $_POST['product'];
}

// セッションから選択された商品のIDを取得
$selectedProductNumbers = isset($_SESSION['selected_products']) ? $_SESSION['selected_products'] : [];

// データベース接続設定
require_once('../utilConnDB.php');
$utilConnDB = new UtilConnDB();
$pdo = $utilConnDB->connect();

// 選択された商品データを取得するクエリ
$sql = "SELECT productNumber AS product_code, productImageURL AS product_image, productName AS product_name, price AS special_price, dateAdded AS start_date, releaseDate AS end_date
        FROM product 
        WHERE productNumber IN (" . implode(',', array_fill(0, count($selectedProductNumbers), '?')) . ")";
$statement = $pdo->prepare($sql);
$statement->execute($selectedProductNumbers);
$products = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Edit Inventory</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>在庫編集</h1>
            <p>商品情報を編集します。</p>
        </div>
        <div class="buttons">
            <button type="submit">確認</button>
            <button type="button" onclick="window.history.back();">キャンセル</button>
        </div>
        <div class="content">
            <form action="updateInventory.php" method="POST">
                <table class="edit-table">
                    <thead>
                        <tr>
                            <th>商品コード</th>
                            <th>商品画像</th>
                            <th>商品名</th>
                            <th>特価</th>
                            <th>販売開始日</th>
                            <th>販売終了日</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['product_code']); ?></td>
                            <td>
                                <img src="<?php echo htmlspecialchars($product['product_image']); ?>" alt="Product Image" style="width: 50px; height: auto;">
                            </td>
                            <td>
                                <input type="text" name="product_name[<?php echo htmlspecialchars($product['product_code']); ?>]" value="<?php echo htmlspecialchars($product['product_name']); ?>">
                            </td>
                            <td>
                                <input type="text" name="special_price[<?php echo htmlspecialchars($product['product_code']); ?>]" value="<?php echo htmlspecialchars($product['special_price']); ?>">
                            </td>
                            <td>
                                <input type="text" name="start_date[<?php echo htmlspecialchars($product['product_code']); ?>]" value="<?php echo htmlspecialchars($product['start_date']); ?>">
                            </td>
                            <td>
                                <input type="text" name="end_date[<?php echo htmlspecialchars($product['product_code']); ?>]" value="<?php echo htmlspecialchars($product['end_date']); ?>">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit">更新</button>
            </form>
        </div>
    </div>
</body>
</html>
