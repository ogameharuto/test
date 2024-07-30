<?php
session_start();

// データベース接続設定
require_once('../utilConnDB.php');
$utilConnDB = new UtilConnDB();
$pdo = $utilConnDB->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stocks = $_POST['stock'] ?? [];
    $methods = $_POST['method'] ?? [];
    $values = $_POST['value'] ?? [];

    try {
        // トランザクションが既に開始されている場合はスキップ
        if (!$pdo->inTransaction()) {
            $pdo->beginTransaction();
        }

        foreach ($stocks as $productId => $currentStock) {
            $method = $methods[$productId] ?? 'add';
            $value = $values[$productId] ?? 0;

            // 現在の在庫数を取得
            $sql = "SELECT stockQuantity FROM product WHERE productNumber = :productNumber";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':productNumber', $productId, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            
            $currentStockDb = $result ? $result['stockQuantity'] : 0;

            // 新しい在庫数を計算
            switch ($method) {
                case 'add':
                    $newStock = $currentStockDb + $value;
                    break;
                case 'subtract':
                    $newStock = $currentStockDb - $value;
                    break;
                case 'set':
                    $newStock = $value;
                    break;
                default:
                    $newStock = $currentStockDb;
                    break;
            }

            // 在庫数の更新
            $sql = "UPDATE product SET stockQuantity = :newStock WHERE productNumber = :productNumber";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':newStock', $newStock, PDO::PARAM_INT);
            $statement->bindParam(':productNumber', $productId, PDO::PARAM_INT);
            $statement->execute();
        }

        // トランザクションのコミット
        if ($pdo->inTransaction()) {
            $pdo->commit();
        }
        header('Location: productStructure.php');

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
    }
    exit;
}
?>
