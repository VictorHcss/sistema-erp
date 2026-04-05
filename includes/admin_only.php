<?php
// Requer que auth.php já tenha sido incluído ou a sessão iniciada
if (!hasRole('admin')) {
    // Redireciona para a página anterior ou index com o erro de permissão
    $referrer = $_SERVER['HTTP_REFERER'] ?? $basePath . 'index.php';
    
    // Evita loop de redirecionamento se o referrer já tiver o erro
    if (strpos($referrer, 'error=permissao_negada') === false) {
        $sep = (strpos($referrer, '?') === false) ? '?' : '&';
        header("Location: " . $referrer . $sep . "error=permissao_negada");
    } else {
        header("Location: " . $basePath . "index.php?error=permissao_negada");
    }
    exit;
}
?>