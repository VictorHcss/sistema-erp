<?php
if (!isset($basePath)) {
    $basePath = '';
}
?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-cubes logo-icon"></i>
        <span class="logo-text">ERP Sistema</span>
        <div class="toggle-sidebar" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </div>
    </div>

    <nav>
        <a href="<?= $basePath ?>index.php" title="Dashboard">
            <i class="fas fa-home"></i>
            <span class="menu-text">Dashboard</span>
        </a>

        <?php if (hasRole('admin')): ?>
            <a href="<?= $basePath ?>modules/users/users.php" title="Usuários">
                <i class="fas fa-user-shield"></i>
                <span class="menu-text">Usuários</span>
            </a>
        <?php endif; ?>

        <a href="<?= $basePath ?>modules/clients/clients.php" title="Clientes">
            <i class="fas fa-users"></i>
            <span class="menu-text">Clientes</span>
        </a>

        <a href="<?= $basePath ?>modules/products/products.php" title="Produtos">
            <i class="fas fa-box"></i>
            <span class="menu-text">Produtos</span>
        </a>

        <a href="<?= $basePath ?>modules/sales/sales.php" title="Vendas">
            <i class="fas fa-shopping-cart"></i>
            <span class="menu-text">Vendas</span>
        </a>

        <a href="<?= $basePath ?>modules/financial/financial.php" title="Financeiro">
            <i class="fas fa-donate"></i>
            <span class="menu-text">Financeiro</span>
        </a>

        <a href="<?= $basePath ?>modules/stock/stock.php" title="Estoque">
            <i class="fas fa-boxes"></i>
            <span class="menu-text">Estoque</span>
        </a>

        <?php if (hasRole('admin')): ?>
            <a href="<?= $basePath ?>modules/config/settings.php" title="Configurações">
                <i class="fas fa-cogs"></i>
                <span class="menu-text">Configurações</span>
            </a>
        <?php endif; ?>

        <a href="<?= $basePath ?>logout.php" title="Sair" class="logout-link">
            <i class="fas fa-sign-out-alt"></i>
            <span class="menu-text">Sair</span>
        </a>
    </nav>
</aside>
<script src="<?= $basePath ?>js/main.js?v=<?= time(); ?>"></script>