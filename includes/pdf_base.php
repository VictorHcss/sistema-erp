<?php
require_once __DIR__ . '/../lib/fpdf/fpdf.php';
require_once __DIR__ . '/auth.php';

class ERP_PDF extends FPDF {
    protected $report_title;
    protected $report_subtitle;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4', $title = 'RELATÓRIO', $subtitle = '') {
        parent::__construct($orientation, $unit, $size);
        $this->report_title = $title;
        $this->report_subtitle = $subtitle;
        $this->AliasNbPages();
        $this->SetAutoPageBreak(true, 20);
    }

    function Header() {
        // Logo ou Nome da Empresa
        $this->SetFont('Arial', 'B', 15);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(0, 10, utf8_decode(getCompanyName()), 0, 1, 'L');
        
        // Subtítulo do Header (tipo de documento)
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 5, utf8_decode($this->report_subtitle ?: 'Documento Gerado pelo Sistema ERP'), 0, 1, 'L');
        
        $this->Ln(5);
        $this->SetDrawColor(200, 200, 200);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->Cell(0, 10, date('d/m/Y H:i'), 0, 0, 'R');
    }

    function SectionTitle($title) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(0, 10, utf8_decode($title), 0, 1, 'L', true);
        $this->Ln(4);
    }

    function StyledTableCell($w, $h, $txt, $border = 0, $ln = 0, $align = '', $fill = false) {
        $this->Cell($w, $h, utf8_decode($txt), $border, $ln, $align, $fill);
    }

    // Helper para cabeçalho de tabela padronizado
    function TableHeader($cols) {
        $this->SetFillColor(233, 236, 239);
        $this->SetTextColor(73, 80, 87);
        $this->SetFont('Arial', 'B', 10);
        foreach ($cols as $col) {
            $this->Cell($col['width'], 10, utf8_decode($col['label']), 1, 0, $col['align'] ?? 'C', true);
        }
        $this->Ln();
    }
}
?>