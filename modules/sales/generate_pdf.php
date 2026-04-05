<?php
require '../../includes/auth.php';
require '../../config/database.php';

// Verifique se o caminho abaixo existe fisicamente
$fpdfPath = '../../lib/fpdf/fpdf.php';
if (!file_exists($fpdfPath)) {
    die("Erro: O arquivo FPDF não foi encontrado em: " . realpath('../../lib/') . "/fpdf/fpdf.php");
}
require $fpdfPath;

$id = $_GET['id'] ?? 0;
$company_id = getCompanyId();

$stmt = $pdo->prepare("SELECT s.*, c.name as client_name, co.name as company_name
                       FROM sales s
                       LEFT JOIN clients c ON s.client_id = c.id
                       JOIN companies co ON s.company_id = co.id
                       WHERE s.id = ? AND s.company_id = ?");
$stmt->execute([$id, $company_id]);
$venda = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venda)
    die("Venda não encontrada.");

$pdf = new FPDF('P', 'mm', array(80, 150));
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, utf8_decode($venda['company_name']), 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 5, "Cupom #$id", 0, 1, 'C');
$pdf->Ln(5);
$pdf->Cell(60, 5, "Total: R$ " . number_format($venda['total'], 2, ',', '.'), 0, 1, 'L');
$pdf->Output('I', "venda_$id.pdf");