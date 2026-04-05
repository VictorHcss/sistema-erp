<?php
require '../../includes/auth.php';
require '../../config/database.php';

// 1. Verificação de Permissão: Apenas Admin
if (!hasRole('admin')) {
    header("Location: sales.php?error=permissao_negada");
    exit;
}

$id = $_GET['id'] ?? 0;

// Busca os dados da venda
$stmt = $pdo->prepare("SELECT * FROM sales WHERE id = ? AND company_id = ?");
$stmt->execute([$id, getCompanyId()]);
$sale = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sale) {
    die("Venda não encontrada.");
}

// Bloqueio de edição: Define se os campos devem estar desabilitados
$pode_editar_campos = ($sale['status'] === 'Pendente');
$readonly = !$pode_editar_campos ? 'disabled' : '';

// Busca clientes
$stmt = $pdo->prepare("SELECT id, name FROM clients WHERE company_id = ?");
$stmt->execute([getCompanyId()]);
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

$msg = "";
$tipo_msg = "error"; // padrão

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo_status = $_POST['status'];
    // Se os campos estavam desabilitados, o POST não envia os valores, então mantemos os originais
    $client_id = $_POST['client_id'] ?? $sale['client_id'];
    $total = $_POST['total'] ?? $sale['total'];

    try {
        $pdo->beginTransaction();

        // Verificar o status atual no banco (segurança extra)
        $stmt = $pdo->prepare("SELECT status FROM sales WHERE id = ? AND company_id = ?");
        $stmt->execute([$id, getCompanyId()]);
        $status_atual = $stmt->fetchColumn();

        if ($status_atual === 'Cancelada') {
            throw new Exception("Esta venda já está cancelada e não pode ser alterada.");
        }

        // Lógica de Cancelamento e Estorno de Estoque
        if ($novo_status === 'Cancelada' && $status_atual !== 'Cancelada') {

            $stmtItems = $pdo->prepare("SELECT product_id, quantity FROM sale_items WHERE sale_id = ?");
            $stmtItems->execute([$id]);
            $itens = $stmtItems->fetchAll();

            foreach ($itens as $item) {
                // Devolve ao estoque
                $updStock = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ? AND company_id = ?");
                $updStock->execute([$item['quantity'], $item['product_id'], getCompanyId()]);

                // Registro no Histórico de Estoque
                $stmtMov = $pdo->prepare("INSERT INTO stock_movements (company_id, product_id, quantity, type, user_id, description) VALUES (?, ?, ?, 'entrada', ?, ?)");
                $desc = "Estorno - Venda #$id Cancelada";
                $stmtMov->execute([getCompanyId(), $item['product_id'], $item['quantity'], $_SESSION['user_id'], $desc]);
            }
        }

        // Atualiza a venda
        $stmtUpdate = $pdo->prepare("UPDATE sales SET client_id = ?, total = ?, status = ?, updated_by = ?, updated_at = NOW() WHERE id = ? AND company_id = ?");
        $stmtUpdate->execute([$client_id, $total, $novo_status, $_SESSION['user_id'], $id, getCompanyId()]);

        $pdo->commit();
        header("Location: sales.php?success=venda_atualizada");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $msg = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Editar Venda - ERP</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php $basePath = '../../';
    include '../../includes/header.php'; ?>

    <main>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2><i class="fas fa-edit"></i> Editar Venda #<?= $id ?></h2>
            <span class="badge" style="padding: 10px; border-radius: 8px; background: #eee;">
                Status Atual: <strong><?= $sale['status'] ?></strong>
            </span>
        </div>

        <?php if ($msg): ?>
            <div class='error-message'><i class='fas fa-exclamation-circle'></i> <?= $msg ?></div>
        <?php endif; ?>

        <form method="POST" class="form-container">
            <?php if (!$pode_editar_campos && $sale['status'] !== 'Cancelada'): ?>
                <div style="background: #fff3cd; color: #856404; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                    <i class="fas fa-info-circle"></i> Vendas <strong>Finalizadas</strong> só podem ter o status alterado
                    para Cancelada.
                </div>
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label>Cliente</label>
                    <select name="client_id" class="form-control" required <?= $readonly ?>>
                        <?php foreach ($clients as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $c['id'] == $sale['client_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-grid-3">
                <div class="form-group">
                    <label>Total (R$)</label>
                    <input type="number" step="0.01" name="total" class="form-control" value="<?= $sale['total'] ?>"
                        required <?= $readonly ?>>
                </div>

                <div class="form-group">
                    <label>Alterar Status</label>
                    <select name="status" class="form-control">
                        <option value="Finalizada" <?= $sale['status'] == 'Finalizada' ? 'selected' : '' ?>>Finalizada
                        </option>
                        <option value="Pendente" <?= $sale['status'] == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                        <option value="Cancelada" <?= $sale['status'] == 'Cancelada' ? 'selected' : '' ?>>Cancelada
                        </option>
                    </select>
                </div>
            </div>

            <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                <a href="sales.php" class="btn btn-danger" style="background-color: #95a5a6;">Voltar</a>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Salvar Alterações</button>
            </div>
        </form>
    </main>
</body>

</html>