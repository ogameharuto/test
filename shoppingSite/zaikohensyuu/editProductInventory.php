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
$sql = "SELECT productNumber AS product_code, productName AS product_name, stockQuantity AS stock 
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
            <p>在庫設定の指定方法を選択し、在庫数を入力します。</p>
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
                        <th>商品名/オプション</th>
                        <th>在庫数</th>
                        <th>指定方法</th>
                        <th>設定値</th>
                        <th>在庫数を超えた注文</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['product_code']); ?></td>
                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                        <td>
                            <input type="text" name="stock[<?php echo htmlspecialchars($product['product_code']); ?>]" value="<?php echo htmlspecialchars($product['stock']); ?>">
                        </td>
                        <td>
                            <select name="method[<?php echo htmlspecialchars($product['product_code']); ?>]">
                                <option value="add">足す</option>
                                <option value="subtract">引く</option>
                                <option value="set">値にする</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="value[<?php echo htmlspecialchars($product['product_code']); ?>]">
                        </td>
                        <td>
                            <input type="checkbox" name="allow_overflow[<?php echo htmlspecialchars($product['product_code']); ?>]" value="1"> 注文可能
                            <input type="checkbox" name="disallow_overflow[<?php echo htmlspecialchars($product['product_code']); ?>]" value="0"> 注文不可
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit">在庫を更新</button>
        </form>

        </div>
    </div>
</body>
</html>
