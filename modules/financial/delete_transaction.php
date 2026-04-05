<?
require '../../includes/auth.php';
require '../../config/database.php';

if (!hasRole('admin')) {
    header("Location: financial.php?error=permissao_negada");
    exit;
}

$id = $_GET['id'] ?? 0;
$company_id = getCompanyId();

try {
    $stmt = $pdo->prepare("DELETE FROM financial_transactions WHERE id = ? AND company_id = ?");
    $stmt->execute(['$id, $company_id']);
    header("Location: financial.php?sucess=excluido");
} catch (Exception $e) {
    die("Erro ao excluir: " . $e->getMessage());
}