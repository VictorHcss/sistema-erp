<?php
require '../../includes/auth.php';
require '../../config/database.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Clientes - ERP</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>

<body>
    <?php $basePath = '../../';
    include '../../includes/header.php'; ?>

    <main>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Gestão de Clientes</h2>
            <a href="add_client.php" class="btn btn-success"><i class="fas fa-plus"></i> Novo Cliente</a>
        </div>

        <div class="table-container">
            <table id="tableModule">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->prepare("SELECT c.*, uc.name as creator_name, uu.name as updater_name 
                                          FROM clients c 
                                          LEFT JOIN users uc ON c.created_by = uc.id 
                                          LEFT JOIN users uu ON c.updated_by = uu.id 
                                          WHERE c.company_id = ? 
                                          ORDER BY c.id DESC");
                    $stmt->execute([getCompanyId()]);

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                        echo "<td>
                                <div style='display: flex; gap: 0.5rem; align-items: center;'>
                                    <a href='edit_client.php?id={$row['id']}' class='action-btn btn-edit' title='Editar'><i class='fas fa-edit'></i></a>
                                    <a href='generate_client_pdf.php?id={$row['id']}' target='_blank' class='action-btn btn-pdf' title='Gerar PDF' 
                                        style='background: #f8f9fa; color: #e74c3c; border: 1px solid #dee2e6; padding: 5px 8px; border-radius: 4px;'>
                                        <i class='fas fa-file-pdf'></i>
                                    </a>";

                        if (hasRole('admin')) {
                            echo " <a href='delete_client.php?id={$row['id']}' class='action-btn btn-delete' onclick='return confirm(\"Tem certeza?\")' title='Excluir'><i class='fas fa-trash'></i></a>";
                        }
                        echo "</div>";

                        echo "<br><small style='color: #666;'>Criado por: " . htmlspecialchars($row['creator_name'] ?? 'Sistema') . "</small>";
                        if ($row['updater_name']) {
                            echo "<br><small style='color: #666;'>Atualizado por: " . htmlspecialchars($row['updater_name']) . "</small>";
                        }

                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'vendas_vinculadas'): ?>
            <p style="color:red; margin-top:10px;">Erro: Não é possível excluir cliente com vendas vinculadas.</p>
        <?php endif; ?>
    </main>
</body>
</php>