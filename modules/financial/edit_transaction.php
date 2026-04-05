<?php
require '../../includes/auth.php';
require '../../config/database.php';

$id = $_GET['id'] ?? 0;
$company_id = getCompanyId();

$stmt = $pdo->prepare("SELECT * FROM financial_transactions WHERE id = ? AND company_id = ?");
$stmt->execute([$id, $company_id]);
$t = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$t)
    die("Lançamento não lançado");

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];
    $category = $_POST['category'];

    // Lógica de data de pagamento: se mudou para pago agora, registra hoje.
    // Se já estava pago, mantém a data original. Se voltou para pendente, limpa.
    $payment_date = $t['payment_date'];
    if ($status === 'paid' && $t['status'] === 'pending') {
        $payment_date = date('Y-m-d');
    } elseif ($status === 'pending') {
        $payment_date = null;
    }

    try {
        $stmt = $pdo->prepare("UPDATE financial_transactions SET 
            description = ?, amount = ?, status = ?, due_date = ?, 
            payment_date = ?, category = ? 
            WHERE id = ? AND company_id = ?");
        $stmt->execute([$description, $amount, $status, $due_date, $payment_date, $category, $id, $company_id]);

        header("Location: financial.php?sucess=atualizado");
        exit;
    } catch (Exception $e) {
        $msg = "Erro ao atualizar" . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php $basePath = '../../';
    include '../../includes/header.php'; ?>
    <main>
        <h2><i class="fas fa-edit"></i> Editar Lançamento</h2>

        <form method="POST" class="form-container">
            <div class="form-row">
                <div class="form-group" style="flex:2;">
                    <label>Descrição</label>
                    <input type="text" name="description" class="form-control"
                        value="<?= htmlspecialchars($t['description']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Categoria</label>
                    <select name="category" class="form-control">
                        <?php
                        $cats = ['Vendas', 'Infraestrutura', 'Fornecedores', 'Marketing', 'Salários', 'Outros'];
                        foreach ($cats as $c): ?>
                            <option value="<?= $c ?>" <?= $t['category'] == $c ? 'selected' : '' ?>>
                                <?= $c ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-grid-3">
                <div class="form-group">
                    <label>Valor (R$)</label>
                    <input type="number" step="0.01" name="amount" class="form-control" value="<?= $t['amount'] ?>"
                        required>
                </div>
                <div class="form-group">
                    <label>Vencimento</label>
                    <input type="date" name="due_date" class="form-control" value="<?= $t['due_date'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="pending" <?= $t['status'] == 'pending' ? 'selected' : '' ?>>Pendente</option>
                        <option value="paid" <?= $t['status'] == 'paid' ? 'selected' : '' ?>>Recebido</option>
                    </select>
                </div>
            </div>
            <div style="margin-top: 2rem; display: flex; justify-content: space-between;">
                <a href="delete_transaction.php?id=<?= $id ?>" class="btn btn-danger"
                    onclick="return confirm('Excluir este lançamento permanentemente?')"><i class="fas fa-trash"></i>
                    Excluir</a>
                <div style="display: flex; gap: 1rem;">
                    <a href="financial.php" class="btn style = background: #95a5a6; color: white;">Voltar</a>
                    <button type="submit" class="btn btn-success">Salvar Alterações</button>
                </div>
        </form>
    </main>
</body>

</html>