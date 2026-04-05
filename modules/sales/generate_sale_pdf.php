<?php
require '../../includes/auth.php';
require '../../config/database.php';
require '../../includes/pdf_base.php';

$sale_id = $_GET['id'] ?? 0;
$company_id = getCompanyId();

// 1. Busca os dados da venda e cliente
$sql = "SELECT s.*, 
               c.name as client_name, c.email as client_email, c.phone as client_phone
        FROM sales s
        LEFT JOIN clients c ON s.client_id = c.id
        WHERE s.id = ? AND s.company_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$sale_id, $company_id]);
$sale = $stmt->fetch();

if (!$sale) {
    die("Venda não encontrada.");
}

// 2. Busca os itens da venda
$sqlItems = "SELECT si.*, p.name as product_name 
             FROM sale_items si 
             LEFT JOIN products p ON si.product_id = p.id 
             WHERE si.sale_id = ?";
$stmtItems = $pdo->prepare($sqlItems);
$stmtItems->execute([$sale_id]);
$items = $stmtItems->fetchAll();

$pdf = new ERP_PDF('P', 'mm', 'A4', 'DETALHES DA VENDA', "Venda #$sale_id");
$pdf->AddPage();

// Título Central
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(33, 37, 41);
$pdf->Cell(0, 10, utf8_decode("COMPROVANTE DE VENDA #$sale_id"), 0, 1, 'C');
$pdf->Ln(5);

// Informações do Cliente e Resumo em duas colunas
$pdf->SectionTitle('INFORMAÇÕES GERAIS');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 7, utf8_decode("CLIENTE"), 0, 0, 'L');
$pdf->Cell(95, 7, utf8_decode("RESUMO DA VENDA"), 0, 1, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 6, utf8_decode("Nome: " . ($sale['client_name'] ?? 'Cliente Removido')), 0, 0, 'L');
$pdf->Cell(95, 6, utf8_decode("Data: " . date('d/m/Y H:i', strtotime($sale['created_at']))), 0, 1, 'L');

$pdf->Cell(95, 6, utf8_decode("E-mail: " . ($sale['client_email'] ?? '-')), 0, 0, 'L');
$pdf->Cell(95, 6, utf8_decode("Status: " . $sale['status']), 0, 1, 'L');

$pdf->Cell(95, 6, utf8_decode("Telefone: " . ($sale['client_phone'] ?? '-')), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 6, utf8_decode("TOTAL: R$ " . number_format($sale['total'], 2, ',', '.')), 0, 1, 'L');

$pdf->Ln(10);

// Itens do Pedido
$pdf->SectionTitle('ITENS DO PEDIDO');
$pdf->TableHeader([
    ['width' => 90, 'label' => 'PRODUTO', 'align' => 'L'],
    ['width' => 20, 'label' => 'QTD', 'align' => 'C'],
    ['width' => 40, 'label' => 'UNITÁRIO', 'align' => 'R'],
    ['width' => 40, 'label' => 'SUBTOTAL', 'align' => 'R']
]);

$pdf->SetFont('Arial', '', 9);
$fill = false;
foreach ($items as $item) {
    $pdf->SetFillColor(248, 249, 250);
    $pdf->Cell(90, 8, utf8_decode($item['product_name'] ?? 'Não encontrado'), 1, 0, 'L', $fill);
    $pdf->Cell(20, 8, $item['quantity'], 1, 0, 'C', $fill);
    $pdf->Cell(40, 8, "R$ " . number_format($item['unit_price'], 2, ',', '.'), 1, 0, 'R', $fill);
    $pdf->Cell(40, 8, "R$ " . number_format($item['quantity'] * $item['unit_price'], 2, ',', '.'), 1, 1, 'R', $fill);
    $fill = !$fill;
}

// Totalizador no final da tabela
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(150, 10, 'TOTAL GERAL', 1, 0, 'R');
$pdf->Cell(40, 10, "R$ " . number_format($sale['total'], 2, ',', '.'), 1, 1, 'R');

$pdf->Output('I', "Venda_$sale_id.pdf");
?>