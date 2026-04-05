<?php
require '../../includes/auth.php';
require '../../config/database.php';

$company_id = getCompanyId();
$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01'); // Início do mês
$data_fim = $_GET['data_fim'] ?? date('Y-m-t');      // Fim do mês

// 1. Busca resumo financeiro (Cards)
$sqlResumo = "SELECT 
    SUM(CASE WHEN type = 'revenue' AND status = 'paid' THEN amount ELSE 0 END) as total_recebido,
    SUM(CASE WHEN type = 'revenue' AND status = 'pending' THEN amount ELSE 0 END) as total_a_receber,
    SUM(CASE WHEN type = 'expense' AND status = 'paid' THEN amount ELSE 0 END) as total_pago,
    SUM(CASE WHEN type = 'expense' AND status = 'pending' THEN amount ELSE 0 END) as total_a_pagar,
    -- Nova linha para o Ticket Médio:
    AVG(CASE WHEN type = 'revenue' THEN amount END) as ticket_medio
    FROM financial_transactions 
    WHERE company_id = ? AND due_date BETWEEN ? AND ?";

$stmtResumo = $pdo->prepare($sqlResumo);
$stmtResumo->execute([$company_id, $data_inicio, $data_fim]);
$resumo = $stmtResumo->fetch(PDO::FETCH_ASSOC);

$saldo_real = $resumo['total_recebido'] - $resumo['total_pago'];

// 2. Busca todas as transações para a tabela
$sqlTransacoes = "SELECT t.*, u.name as user_name 
                  FROM financial_transactions t
                  LEFT JOIN users u ON t.user_id = u.id
                  WHERE t.company_id = ? AND t.due_date BETWEEN ? AND ?
                  ORDER BY t.due_date DESC";
$stmtTrans = $pdo->prepare($sqlTransacoes);
$stmtTrans->execute([$company_id, $data_inicio, $data_fim]);
$transacoes = $stmtTrans->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Financeiro - ERP</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php $basePath = '../../';
    include '../../includes/header.php'; ?>

    <main>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2><i class="fas fa-wallet"></i> Fluxo de Caixa</h2>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <a href="generate_report.php?data_inicio=<?= $data_inicio ?>&data_fim=<?= $data_fim ?>" target="_blank" class="btn"
                    style="background: #f8f9fa; color: #495057; border: 1px solid #ced4da; text-decoration: none; padding: 0.5rem 0.8rem; border-radius: 4px; display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; transition: all 0.2s;">
                    <i class="fas fa-file-pdf" style="color: #e74c3c;"></i> Relatório PDF
                </a>
                <div style="width: 1px; height: 24px; background: #dee2e6; margin: 0 0.5rem;"></div>
                <a href="add_transaction.php?type=revenue" class="btn btn-success" style="padding: 0.5rem 1rem; font-size: 0.9rem;"><i class="fas fa-plus"></i>
                    Receita</a>
                <a href="add_transaction.php?type=expense" class="btn btn-danger" style="background: #e74c3c; padding: 0.5rem 1rem; font-size: 0.9rem;"><i
                        class="fas fa-minus"></i> Despesa</a>
            </div>
        </div>

        <div class="dashboard-cards"
            style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-bottom: 2rem;">
            <div class="card" style="border-left: 5px solid #2ecc71;">
                <h3>Recebido</h3>
                <p style="color: #2ecc71; font-size: 1.5rem; font-weight: bold;">R$
                    <?= number_format($resumo['total_recebido'], 2, ',', '.') ?>
                </p>
            </div>
            <div class="card" style="border-left: 5px solid #e74c3c;">
                <h3>Pago</h3>
                <p style="color: #e74c3c; font-size: 1.5rem; font-weight: bold;">R$
                    <?= number_format($resumo['total_pago'], 2, ',', '.') ?>
                </p>
            </div>
            <div class="card" style="border-left: 5px solid #3498db;">
                <h3>Saldo (Caixa)</h3>
                <p style="color: #3498db; font-size: 1.5rem; font-weight: bold;">R$
                    <?= number_format($saldo_real, 2, ',', '.') ?>
                </p>
            </div>
            <div class="card" style="border-left: 5px solid #9b59b6;">
                <h3>Ticket Médio</h3>
                <p style="color: #9b59b6; font-size: 1.5rem; font-weight: bold;">
                    R$
                    <?= number_format($resumo['ticket_medio'] ?? 0, 2, ',', '.') ?>
                </p>
                <small style="color: #666;">Média por venda</small>
            </div>
            <div class="card" style="border-left: 5px solid #f39c12;">
                <h3>Previsto (Pendentes)</h3>
                <p style="color: #f39c12; font-size: 1.1rem;">Rec: R$
                    <?= number_format($resumo['total_a_receber'], 2, ',', '.') ?>
                </p>
                <p style="color: #666; font-size: 1.1rem;">Pag: R$
                    <?= number_format($resumo['total_a_pagar'], 2, ',', '.') ?>
                </p>
            </div>
        </div>

        <div class="table-container" style="margin-bottom: 1rem; padding: 1rem;">
            <form method="GET" style="display: flex; gap: 1rem; align-items: flex-end;">
                <div>
                    <label>Início</label>
                    <input type="date" name="data_inicio" class="form-control" value="<?= $data_inicio ?>">
                </div>
                <div>
                    <label>Fim</label>
                    <input type="date" name="data_fim" class="form-control" value="<?= $data_fim ?>">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
            </form>
        </div>

        <div class="table-container">
            <table id="tableModule">
                <thead>
                    <tr>
                        <th>Vencimento</th>
                        <th>Descrição</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transacoes as $t): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($t['due_date'])) ?></td>
                            <td>
                                <?= htmlspecialchars($t['description']) ?>
                                <?php if ($t['sale_id']): ?>
                                    <br><small><a href="../sales/sale_view.php?id=<?= $t['sale_id'] ?>" target="_blank">Venda
                                            #<?= $t['sale_id'] ?></a></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="color: <?= $t['type'] == 'revenue' ? '#2ecc71' : '#e74c3c' ?>">
                                    <i class="fas <?= $t['type'] == 'revenue' ? 'fa-arrow-up' : 'fa-arrow-down' ?>"></i>
                                    <?= $t['type'] == 'revenue' ? 'Receita' : 'Despesa' ?>
                                </span>
                            </td>
                            <td style="font-weight: bold;">R$ <?= number_format($t['amount'], 2, ',', '.') ?></td>
                            <td>
                                <span class="badge"
                                    style="background: <?= $t['status'] == 'paid' ? '#2ecc71' : '#f39c12' ?>; color: white; padding: 4px 8px; border-radius: 4px;">
                                    <?= $t['status'] == 'paid' ? 'Pago' : 'Pendente' ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_transaction.php?id=<?= $t['id'] ?>" class="btn-edit"><i
                                        class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>