<?php
require '../../includes/auth.php';
require '../../config/database.php';
require '../../includes/pdf_base.php';

$company_id = getCompanyId();
$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
$data_fim = $_GET['data_fim'] ?? date('Y-m-t');

// 1. Busca resumo financeiro
$sqlResumo = "SELECT 
    SUM(CASE WHEN type = 'revenue' AND status = 'paid' THEN amount ELSE 0 END) as recebido,
    SUM(CASE WHEN type = 'expense' AND status = 'paid' THEN amount ELSE 0 END) as pago
    FROM financial_transactions 
    WHERE company_id = ? AND due_date BETWEEN ? AND ?";

$stmt = $pdo->prepare($sqlResumo);
$stmt->execute([$company_id, $data_inicio, $data_fim]);
$resumo = $stmt->fetch(PDO::FETCH_ASSOC);

$saldo = ($resumo['recebido'] ?? 0) - ($resumo['pago'] ?? 0);

$pdf = new ERP_PDF('P', 'mm', 'A4', 'EXTRATO FINANCEIRO', 'Relatório Financeiro Detalhado');
$pdf->AddPage();

// Título e Período
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(33, 37, 41);
$pdf->Cell(0, 10, utf8_decode("EXTRATO FINANCEIRO"), 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, utf8_decode("Período: " . date('d/m/Y', strtotime($data_inicio)) . " até " . date('d/m/Y', strtotime($data_fim))), 0, 1, 'C');
$pdf->Ln(10);

// Resumo em Cards (estilo clean)
$pdf->SetFillColor(248, 249, 250);
$pdf->SetDrawColor(222, 226, 230);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(108, 117, 125);

$width = 63.3;
$pdf->Cell($width, 8, "TOTAL RECEBIDO", 1, 0, 'C', true);
$pdf->Cell($width, 8, "TOTAL PAGO", 1, 0, 'C', true);
$pdf->Cell($width, 8, "SALDO FINAL", 1, 1, 'C', true);

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(40, 167, 69); // Verde para recebido
$pdf->Cell($width, 12, "R$ " . number_format($resumo['recebido'] ?? 0, 2, ',', '.'), 1, 0, 'C');

$pdf->SetTextColor(220, 53, 69); // Vermelho para pago
$pdf->Cell($width, 12, "R$ " . number_format($resumo['pago'] ?? 0, 2, ',', '.'), 1, 0, 'C');

if ($saldo >= 0) {
    $pdf->SetTextColor(40, 167, 69);
} else {
    $pdf->SetTextColor(220, 53, 69);
}
$pdf->Cell($width, 12, "R$ " . number_format($saldo, 2, ',', '.'), 1, 1, 'C');

$pdf->Ln(15);

// Cabeçalho da Tabela
$pdf->TableHeader([
    ['width' => 25, 'label' => 'DATA', 'align' => 'C'],
    ['width' => 85, 'label' => 'DESCRIÇÃO', 'align' => 'L'],
    ['width' => 40, 'label' => 'TIPO', 'align' => 'C'],
    ['width' => 40, 'label' => 'VALOR', 'align' => 'C']
]);

// Dados da Tabela
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(33, 37, 41);

$sqlT = "SELECT * FROM financial_transactions 
         WHERE company_id = ? AND due_date BETWEEN ? AND ?
         ORDER BY due_date ASC";
$stmtT = $pdo->prepare($sqlT);
$stmtT->execute([$company_id, $data_inicio, $data_fim]);

$fill = false;
while ($t = $stmtT->fetch(PDO::FETCH_ASSOC)) {
    $pdf->SetFillColor(248, 249, 250);
    $pdf->Cell(25, 8, date('d/m/Y', strtotime($t['due_date'])), 1, 0, 'C', $fill);
    $pdf->Cell(85, 8, utf8_decode($t['description']), 1, 0, 'L', $fill);
    
    // Cor por tipo
    if ($t['type'] == 'revenue') {
        $pdf->SetTextColor(40, 167, 69);
        $tipo = 'Receita';
    } else {
        $pdf->SetTextColor(220, 53, 69);
        $tipo = 'Despesa';
    }
    
    $pdf->Cell(40, 8, utf8_decode($tipo), 1, 0, 'C', $fill);
    $pdf->SetTextColor(33, 37, 41);
    $pdf->Cell(40, 8, "R$ " . number_format($t['amount'], 2, ',', '.'), 1, 1, 'R', $fill);
    $fill = !$fill; // Zebra striping
}

$pdf->Output('I', "Extrato_" . date('d_m_Y', strtotime($data_inicio)) . ".pdf");