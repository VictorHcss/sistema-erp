<?php
require '../../includes/auth.php';
require '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_id = getCompanyId();
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $unit_cost = $_POST['unit_cost'];
    $supplier = $_POST['supplier'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO purchases (company_id, product_id, quantity, unit_cost, supplier) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$company_id, $product_id, $quantity, $unit_cost, $supplier]);

        $stmtUpdate = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ? AND company_id = ?");
        $stmtUpdate->execute([$quantity, $product_id, $company_id]);

        $total_custo = $quantity * $unit_cost;
        $stmtFin = $pdo->prepare("INSERT INTO financial_transactions (company_id, user_id, description, amount, type, status, due_date) VALUES (?, ?, ?, ?, 'expense', 'paid', CURRENT_DATE)");
        $stmtFin->execute([$company_id, $_SESSION['user_id'], "Compra de estoque: $supplier", $total_custo]);

        $pdo->commit();
        header("Location: ../../index.php?success=estoque_atualizado");

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao processar entrada: " . $e->getMessage());
    }
}