<?php
require '../../includes/auth.php';
require '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_POST['client_id'];
    $company_id = getCompanyId();
    $user_id = $_SESSION['user_id'];
    $status = $_POST['status'];
    $items = json_decode($_POST['items'], true); 

    if (empty($items)) {
        die("Erro: A venda deve ter pelo menos um item.");
    }

    try {
        $pdo->beginTransaction();

        $total_venda = 0;
        foreach ($items as $item) {
            $total_venda += $item['quantity'] * $item['unit_price'];
        }

        $stmtSale = $pdo->prepare("INSERT INTO sales (client_id, user_id, company_id, total, status) VALUES (?, ?, ?, ?, ?)");
        $stmtSale->execute([$client_id, $user_id, $company_id, $total_venda, $status]);
        $sale_id = $pdo->lastInsertId();

        foreach ($items as $item) {
            $stmtItem = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            $stmtItem->execute([$sale_id, $item['product_id'], $item['quantity'], $item['unit_price']]);

            if ($status !== 'Cancelada') {
                $stmtUpdateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND company_id = ?");
                $stmtUpdateStock->execute([$item['quantity'], $item['product_id'], $company_id]);
            }
        }

        $stmtFin = $pdo->prepare("INSERT INTO financial_transactions (company_id, user_id, sale_id, description, amount, type, status, due_date) 
                                 VALUES (?, ?, ?, ?, ?, 'revenue', ?, CURRENT_DATE)");
        $finStatus = ($status === 'Finalizada') ? 'paid' : 'pending';
        $stmtFin->execute([$company_id, $user_id, $sale_id, "Venda #$sale_id", $total_venda, $finStatus]);

        $pdo->commit();

        header("Location: sales.php?success=1");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao processar venda: " . $e->getMessage());
    }
}