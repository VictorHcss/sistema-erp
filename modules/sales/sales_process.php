<?php
require '../../includes/auth.php';
require '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: sales.php');
    exit;
}

$company_id = getCompanyId();
$client_id = $_POST['client_id'] ?? null;
$status = $_POST['status'] ?? 'Finalizada';
$items = json_decode($_POST['items'] ?? '[]', true);

if (!$client_id || empty($items)) {
    die('Cliente e itens da venda são obrigatórios.');
}

try {
    // Inicia transação única para garantir integridade total
    $pdo->beginTransaction();

    // 1. Calcula total da venda
    $total = 0;
    foreach ($items as $item) {
        $total += $item['quantity'] * $item['unit_price'];
    }

    // 2. Insere a venda
    $stmt = $pdo->prepare("
        INSERT INTO sales (company_id, client_id, total, status, user_id)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$company_id, $client_id, $total, $status, $_SESSION['user_id']]);
    $sale_id = $pdo->lastInsertId();

    // 3. Itens e Estoque
    $stmtItem = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
    $stmtStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND company_id = ?");

    // Preparar histórico de estoque (Kardex)
    $stmtKardex = $pdo->prepare("INSERT INTO stock_movements (company_id, product_id, quantity, type, user_id, description) VALUES (?, ?, ?, 'saida', ?, ?)");

    foreach ($items as $item) {
        // Insere item da venda
        $stmtItem->execute([$sale_id, $item['product_id'], $item['quantity'], $item['unit_price']]);

        // Baixa no estoque
        $stmtStock->execute([$item['quantity'], $item['product_id'], $company_id]);

        // Regista no histórico de estoque
        $descKardex = "Venda #$sale_id";
        $stmtKardex->execute([$company_id, $item['product_id'], $item['quantity'], $_SESSION['user_id'], $descKardex]);
    }

    // 4. INTEGRAÇÃO FINANCEIRA
    // Criamos uma conta a receber automaticamente
    $stmtFin = $pdo->prepare("
        INSERT INTO financial_transactions 
        (company_id, sale_id, description, amount, type, status, due_date, category, user_id) 
        VALUES (?, ?, ?, ?, 'revenue', ?, CURRENT_DATE, 'Vendas', ?)
    ");

    // Se a venda estiver 'Finalizada', assumimos que já foi paga (ou pode ajustar conforme sua regra)
    $finStatus = ($status === 'Finalizada') ? 'paid' : 'pending';
    $paymentDate = ($finStatus === 'paid') ? date('Y-m-d') : null;
    $descFin = "Recebimento Venda #$sale_id";

    $stmtFin->execute([
        $company_id,
        $sale_id,
        $descFin,
        $total,
        $finStatus,
        $_SESSION['user_id']
    ]);

    // Se foi pago, atualizamos a data de pagamento
    if ($finStatus === 'paid') {
        $pdo->prepare("UPDATE financial_transactions SET payment_date = NOW() WHERE id = ?")
            ->execute([$pdo->lastInsertId()]);
    }

    // Confirmar tudo
    $pdo->commit();
    header("Location: sales.php?success=venda_concluida");

} catch (Exception $e) {
    // Se algo falhou (estoque, financeiro ou venda), desfaz tudo
    $pdo->rollBack();
    die("Erro ao processar venda: " . $e->getMessage());
}