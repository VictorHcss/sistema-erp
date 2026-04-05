<?php
require '../../includes/auth.php';
require '../../config/database.php';
require '../../includes/pdf_base.php';

$client_id = $_GET['id'] ?? 0;
$company_id = getCompanyId();

// 1. Busca os dados do cliente
$sql = "SELECT c.*, 
               uc.name as creator_name, 
               uu.name as updater_name 
        FROM clients c 
        LEFT JOIN users uc ON c.created_by = uc.id 
        LEFT JOIN users uu ON c.updated_by = uu.id 
        WHERE c.id = ? AND c.company_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$client_id, $company_id]);
$client = $stmt->fetch();

if (!$client) {
    die("Cliente não encontrado.");
}

// 2. Busca histórico de vendas do cliente
$sqlSales = "SELECT * FROM sales WHERE client_id = ? AND company_id = ? ORDER BY created_at DESC LIMIT 10";
$stmtSales = $pdo->prepare($sqlSales);
$stmtSales->execute([$client_id, $company_id]);
$sales = $stmtSales->fetchAll();

$pdf = new ERP_PDF('P', 'mm', 'A4', 'FICHA DO CLIENTE', "Cliente #$client_id");
$pdf->AddPage();

// Título Central
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(33, 37, 41);
$pdf->Cell(0, 10, utf8_decode("FICHA CADASTRAL DO CLIENTE"), 0, 1, 'C');
$pdf->Ln(5);

// Informações Cadastrais
$pdf->SectionTitle('DADOS CADASTRAIS');
$pdf->SetFont('Arial', 'B', 10);

$data = [
    'Nome' => $client['name'],
    'E-mail' => $client['email'] ?: '-',
    'Telefone' => $client['phone'] ?: '-',
    'Data de Cadastro' => date('d/m/Y H:i', strtotime($client['created_at'])),
    'Criado por' => $client['creator_name'] ?: 'Sistema'
];

foreach ($data as $label => $value) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 7, utf8_decode($label . ":"), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 7, utf8_decode($value), 0, 1, 'L');
}

$pdf->Ln(10);

// Histórico Recente de Vendas
$pdf->SectionTitle('ÚLTIMAS 10 VENDAS');
if (empty($sales)) {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, utf8_decode("Nenhuma venda registrada para este cliente."), 0, 1, 'L');
} else {
    $pdf->TableHeader([
        ['width' => 30, 'label' => 'ID', 'align' => 'C'],
        ['width' => 60, 'label' => 'DATA', 'align' => 'C'],
        ['width' => 50, 'label' => 'STATUS', 'align' => 'C'],
        ['width' => 50, 'label' => 'TOTAL', 'align' => 'R']
    ]);

    $pdf->SetFont('Arial', '', 9);
    $fill = false;
    $total_vendas = 0;
    foreach ($sales as $sale) {
        $pdf->SetFillColor(248, 249, 250);
        $pdf->Cell(30, 8, $sale['id'], 1, 0, 'C', $fill);
        $pdf->Cell(60, 8, date('d/m/Y H:i', strtotime($sale['created_at'])), 1, 0, 'C', $fill);
        $pdf->Cell(50, 8, utf8_decode($sale['status']), 1, 0, 'C', $fill);
        $pdf->Cell(50, 8, "R$ " . number_format($sale['total'], 2, ',', '.'), 1, 1, 'R', $fill);
        $total_vendas += $sale['total'];
        $fill = !$fill;
    }
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(140, 10, utf8_decode('TOTAL ACUMULADO (ÚLT. 10)'), 1, 0, 'R');
    $pdf->Cell(50, 10, "R$ " . number_format($total_vendas, 2, ',', '.'), 1, 1, 'R');
}

$pdf->Output('I', "Cliente_$client_id.pdf");
?>