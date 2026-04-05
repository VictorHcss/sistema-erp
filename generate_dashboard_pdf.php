<?php
require 'includes/auth.php';
require 'config/database.php';
require 'includes/pdf_base.php';

$company_id = getCompanyId();

// 1. Busca contagens básicas
$stmt = $pdo->prepare("SELECT COUNT(*) FROM clients WHERE company_id = ?");
$stmt->execute([$company_id]);
$totalClients = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE company_id = ?");
$stmt->execute([$company_id]);
$totalProducts = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM sales WHERE company_id = ? AND status != 'Cancelada'");
$stmt->execute([$company_id]);
$totalSales = $stmt->fetchColumn();

// 2. Busca o faturamento (Vendas Finalizadas)
$stmt = $pdo->prepare("SELECT SUM(total) FROM sales WHERE company_id = ? AND status = 'Finalizada'");
$stmt->execute([$company_id]);
$totalSalesValue = $stmt->fetchColumn() ?: 0;

// 3. Busca despesas para calcular o Saldo Real
$stmt = $pdo->prepare("SELECT SUM(amount) FROM financial_transactions 
                       WHERE company_id = ? AND type = 'expense' AND status = 'paid'");
$stmt->execute([$company_id]);
$totalExpenses = $stmt->fetchColumn() ?: 0;

$saldoCaixa = $totalSalesValue - $totalExpenses;
$ticketMedio = $totalSales > 0 ? ($totalSalesValue / $totalSales) : 0;

$pdf = new ERP_PDF('P', 'mm', 'A4', 'RELATÓRIO GERENCIAL', 'Resumo Executivo do Dashboard');
$pdf->AddPage();

$pdf->SectionTitle('INDICADORES DE DESEMPENHO');

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(248, 249, 250);

$width = 95;
$pdf->Cell($width, 10, utf8_decode("TOTAL DE CLIENTES"), 1, 0, 'C', true);
$pdf->Cell($width, 10, utf8_decode("TOTAL DE PRODUTOS"), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell($width, 12, $totalClients, 1, 0, 'C');
$pdf->Cell($width, 12, $totalProducts, 1, 1, 'C');

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($width, 10, utf8_decode("VENDAS REALIZADAS"), 1, 0, 'C', true);
$pdf->Cell($width, 10, utf8_decode("SALDO EM CAIXA"), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell($width, 12, $totalSales, 1, 0, 'C');
$pdf->Cell($width, 12, "R$ " . number_format($saldoCaixa, 2, ',', '.'), 1, 1, 'C');

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($width, 10, utf8_decode("TICKET MÉDIO"), 1, 0, 'C', true);
$pdf->Cell($width, 10, utf8_decode("FATURAMENTO TOTAL"), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell($width, 12, "R$ " . number_format($ticketMedio, 2, ',', '.'), 1, 0, 'C');
$pdf->Cell($width, 12, "R$ " . number_format($totalSalesValue, 2, ',', '.'), 1, 1, 'C');

$pdf->Ln(15);

// Últimas Vendas
$pdf->SectionTitle('ÚLTIMAS VENDAS REGISTRADAS');
$pdf->TableHeader([
    ['width' => 20, 'label' => 'ID', 'align' => 'C'],
    ['width' => 80, 'label' => 'CLIENTE', 'align' => 'L'],
    ['width' => 45, 'label' => 'STATUS', 'align' => 'C'],
    ['width' => 45, 'label' => 'VALOR', 'align' => 'R']
]);

$sql = "SELECT s.*, c.name as client_name
        FROM sales s
        LEFT JOIN clients c ON s.client_id = c.id
        WHERE s.company_id = ?
        ORDER BY s.created_at DESC LIMIT 10";
$stmt = $pdo->prepare($sql);
$stmt->execute([$company_id]);

$pdf->SetFont('Arial', '', 9);
$fill = false;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pdf->SetFillColor(248, 249, 250);
    $pdf->Cell(20, 8, "#" . $row['id'], 1, 0, 'C', $fill);
    $pdf->Cell(80, 8, utf8_decode($row['client_name'] ?? 'Consumidor'), 1, 0, 'L', $fill);
    $pdf->Cell(45, 8, utf8_decode($row['status']), 1, 0, 'C', $fill);
    $pdf->Cell(45, 8, "R$ " . number_format($row['total'], 2, ',', '.'), 1, 1, 'R', $fill);
    $fill = !$fill;
}

$pdf->Output('I', "Relatorio_Gerencial_" . date('d_m_Y') . ".pdf");
?>