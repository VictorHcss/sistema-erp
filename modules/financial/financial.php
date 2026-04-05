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
            <h2 style="margin: 0; font-size: 1.5rem; color: #2c3e50; font-weight: 600;"><i class="fas fa-wallet" style="margin-right: 10px;"></i> Fluxo de Caixa</h2>
            <div style="display: flex; gap: 0.75rem; align-items: center;">
                <a href="generate_report.php?data_inicio=<?= $data_inicio ?>&data_fim=<?= $data_fim ?>" target="_blank" class="btn"
                    style="background: #fff; color: #495057; border: 1px solid #dcdde1; text-decoration: none; padding: 0.5rem 1rem; border-radius: 6px; display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 500; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    <i class="fas fa-file-pdf" style="color: #e74c3c;"></i> Relatório PDF
                </a>
                <div style="width: 1px; height: 24px; background: #eee; margin: 0 0.25rem;"></div>
                <a href="add_transaction.php?type=revenue" class="btn" style="background: #27ae60; color: white; padding: 0.5rem 1.2rem; font-size: 0.85rem; font-weight: 600; border-radius: 6px; box-shadow: 0 2px 6px rgba(39, 174, 96, 0.2);"><i class="fas fa-plus"></i>
                    Receita</a>
                <a href="add_transaction.php?type=expense" class="btn" style="background: #e74c3c; color: white; padding: 0.5rem 1.2rem; font-size: 0.85rem; font-weight: 600; border-radius: 6px; box-shadow: 0 2px 6px rgba(231, 76, 60, 0.2);"><i
                        class="fas fa-minus"></i> Despesa</a>
            </div>
        </div>

        <div class="dashboard-cards"
            style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1.25rem; margin-bottom: 2.5rem;">
            <div class="card" style="background: #fff; padding: 1.25rem; border-radius: 8px; border: 1px solid #edf2f7; border-top: 4px solid #2ecc71; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                <h3 style="font-size: 0.8rem; text-transform: uppercase; color: #718096; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Recebido</h3>
                <p style="color: #2d3748; font-size: 1.4rem; font-weight: 700; margin: 0;">R$
                    <?= number_format($resumo['total_recebido'], 2, ',', '.') ?>
                </p>
            </div>
            <div class="card" style="background: #fff; padding: 1.25rem; border-radius: 8px; border: 1px solid #edf2f7; border-top: 4px solid #e74c3c; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                <h3 style="font-size: 0.8rem; text-transform: uppercase; color: #718096; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Pago</h3>
                <p style="color: #2d3748; font-size: 1.4rem; font-weight: 700; margin: 0;">R$
                    <?= number_format($resumo['total_pago'], 2, ',', '.') ?>
                </p>
            </div>
            <div class="card" style="background: #fff; padding: 1.25rem; border-radius: 8px; border: 1px solid #edf2f7; border-top: 4px solid #3498db; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                <h3 style="font-size: 0.8rem; text-transform: uppercase; color: #718096; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Saldo (Caixa)</h3>
                <p style="color: #2d3748; font-size: 1.4rem; font-weight: 700; margin: 0;">R$
                    <?= number_format($saldo_real, 2, ',', '.') ?>
                </p>
            </div>
            <div class="card" style="background: #fff; padding: 1.25rem; border-radius: 8px; border: 1px solid #edf2f7; border-top: 4px solid #9b59b6; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                <h3 style="font-size: 0.8rem; text-transform: uppercase; color: #718096; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Ticket Médio</h3>
                <p style="color: #2d3748; font-size: 1.4rem; font-weight: 700; margin: 0;">
                    R$
                    <?= number_format($resumo['ticket_medio'] ?? 0, 2, ',', '.') ?>
                </p>
                <small style="color: #a0aec0; font-size: 0.75rem;">Média por venda</small>
            </div>
            <div class="card" style="background: #fff; padding: 1.25rem; border-radius: 8px; border: 1px solid #edf2f7; border-top: 4px solid #f39c12; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                <h3 style="font-size: 0.8rem; text-transform: uppercase; color: #718096; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Previsto</h3>
                <div style="display: flex; flex-direction: column; gap: 2px;">
                    <p style="color: #2d3748; font-size: 1rem; font-weight: 600; margin: 0;">Rec: <span style="color: #38a169;">R$ <?= number_format($resumo['total_a_receber'], 2, ',', '.') ?></span></p>
                    <p style="color: #2d3748; font-size: 1rem; font-weight: 600; margin: 0;">Pag: <span style="color: #e53e3e;">R$ <?= number_format($resumo['total_a_pagar'], 2, ',', '.') ?></span></p>
                </div>
            </div>
        </div>

        <div class="table-container" style="margin-bottom: 1.5rem; padding: 1.25rem; background: #fff; border-radius: 8px; border: 1px solid #edf2f7; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <form method="GET" style="display: flex; gap: 1.25rem; align-items: flex-end;">
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label style="font-size: 0.8rem; font-weight: 600; color: #4a5568;">Data Início</label>
                    <input type="date" name="data_inicio" class="form-control" value="<?= $data_inicio ?>" style="padding: 0.5rem 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.9rem;">
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label style="font-size: 0.8rem; font-weight: 600; color: #4a5568;">Data Fim</label>
                    <input type="date" name="data_fim" class="form-control" value="<?= $data_fim ?>" style="padding: 0.5rem 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.9rem;">
                </div>
                <button type="submit" class="btn" style="background: #4361ee; color: white; padding: 0.5rem 1.5rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600;"><i class="fas fa-filter"></i> Filtrar</button>
            </form>
        </div>

        <div class="table-container" style="background: #fff; border-radius: 8px; border: 1px solid #edf2f7; box-shadow: 0 4px 6px rgba(0,0,0,0.02); padding: 0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #edf2f7;">
                        <th style="padding: 1rem; text-align: left; color: #4a5568; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Vencimento</th>
                        <th style="padding: 1rem; text-align: left; color: #4a5568; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Descrição</th>
                        <th style="padding: 1rem; text-align: center; color: #4a5568; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Tipo</th>
                        <th style="padding: 1rem; text-align: right; color: #4a5568; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Valor</th>
                        <th style="padding: 1rem; text-align: center; color: #4a5568; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Status</th>
                        <th style="padding: 1rem; text-align: center; color: #4a5568; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transacoes as $t): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1rem; color: #4a5568;"><?= date('d/m/Y', strtotime($t['due_date'])) ?></td>
                            <td style="padding: 1rem;">
                                <div style="color: #2d3748; font-weight: 500;"><?= htmlspecialchars($t['description']) ?></div>
                                <?php if ($t['sale_id']): ?>
                                    <small style="background: #ebf8ff; color: #3182ce; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: 600; margin-top: 4px; display: inline-block;">
                                        <a href="../sales/sale_view.php?id=<?= $t['sale_id'] ?>" target="_blank" style="text-decoration: none; color: inherit;">
                                            <i class="fas fa-tag"></i> Venda #<?= $t['sale_id'] ?>
                                        </a>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background: <?= $t['type'] == 'revenue' ? '#f0fff4' : '#fff5f5' ?>; color: <?= $t['type'] == 'revenue' ? '#38a169' : '#e53e3e' ?>;">
                                    <i class="fas <?= $t['type'] == 'revenue' ? 'fa-arrow-up' : 'fa-arrow-down' ?>" style="font-size: 0.7rem;"></i>
                                    <?= $t['type'] == 'revenue' ? 'Receita' : 'Despesa' ?>
                                </span>
                            </td>
                            <td style="padding: 1rem; text-align: right; font-weight: 700; color: #2d3748;">
                                R$ <?= number_format($t['amount'], 2, ',', '.') ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; background: <?= $t['status'] == 'paid' ? '#38a169' : '#ecc94b' ?>; color: white;">
                                    <?= $t['status'] == 'paid' ? 'Pago' : 'Pendente' ?>
                                </span>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <a href="edit_transaction.php?id=<?= $t['id'] ?>" style="color: #cbd5e0; transition: color 0.2s; font-size: 1.1rem;" onmouseover="this.style.color='#4361ee'" onmouseout="this.style.color='#cbd5e0'">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>