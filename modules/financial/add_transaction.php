<?php
require '../../includes/auth.php';
require '../../config/database.php';

$msg = "";
$type = $_GET['type'] ?? 'expense';
$label = ($type === 'revenue') ? 'Receita' : 'Despesa';
$icon = ($type === 'revenue') ? 'fa-plus-circle' : 'fa-minus-circle';
$color = ($type === 'revenue') ? '#2ecc71' : '#e74c3c';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $type = $_POST['type'];
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];
    $category = $_POST['category'];

    $payment_date = ($status === 'paid') ? date('Y-m-d') : null;

    try {
        $stmt = $pdo->prepare("INSERT INTO financial_transactions 
            (company_id, description, amount, type, status, due_date, payment_date, category, user_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            getCompanyId(),
            $description,
            $amount,
            $type,
            $status,
            $due_date,
            $payment_date,
            $category,
            $_SESSION['user_id']
        ]);

        header("Location: financial.php?success=lancamento_criado");
        exit;
    } catch (Exception $e) {
        $msg = "Erro ao salvar lançamento: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Novo Lançamento - ERP</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php $basePath = '../../';
    include '../../includes/header.php'; ?>

    <main>
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
            <i class="fas <?= $icon ?>" style="font-size: 2rem; color: <?= $color ?>;"></i>
            <h2>Novo Lançamento de <?= $label ?></h2>
        </div>

        <?php if ($msg)
            echo "<div class='error-message'><i class='fas fa-exclamation-triangle'></i> $msg</div>"; ?>

        <form method="POST" class="form-container">
            <input type="hidden" name="type" value="<?= $type ?>">

            <div class="form-row">
                <div class="form-group" style="flex: 2;">
                    <label>Descrição / Histórico *</label>
                    <input type="text" name="description" class="form-control" placeholder="Ex: Pagamento Internet"
                        required autofocus>
                </div>
                <div class="form-group">
                    <label>Categoria</label>
                    <select name="category" class="form-control">
                        <option value="Vendas">Vendas</option>
                        <option value="Infraestrutura">Infraestrutura (Luz, Água...)</option>
                        <option value="Fornecedores">Fornecedores</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Salários">Salários</option>
                        <option value="Outros">Outros</option>
                    </select>
                </div>
            </div>

            <div class="form-grid-3">
                <div class="form-group">
                    <label>Valor (R$)</label>
                    <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label>Data Vencimento</label>
                    <input type="date" step="due_date" name="due_date" class="form-control" value="<?= date('Y-m-d') ?>"
                        required>
                </div>
                <div class="form-group">
                    <label>Status Inicial</label>
                    <select name="status" class="form-control">
                        <option value="pending">Aguardando (Pendente)</option>
                        <option value="paid">Efetivado (Pago/Recebido)</option>
                    </select>
                </div>
            </div>
            <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                <a href="financial.php" class="btn btn-danger" style="background-color: #95a5a6;">Voltar</a>
                <button type="submit" class="btn btn-success"><i class="fas fa-check"></i>Confirmar Lançamento</button>
            </div>
        </form>
    </main>
</body>

</html>