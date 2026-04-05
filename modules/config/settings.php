<?php
require '../../includes/auth.php';
require '../../config/database.php';

$company_id = getCompanyId();

$stmt = $pdo->prepare("SELECT meta_key, meta_value FROM system_settings WHERE company_id = ?");
$stmt->execute([$company_id]);
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Configurações de Identidade</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
</head>
<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --bg-light: #f8fafc;
        --text-main: #2d3748;
        --text-muted: #718096;
        --border-color: #e2e8f0;
        --white: #ffffff;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    body {
        background-color: var(--bg-light);
        color: var(--text-main);
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }

    main {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem;
    }

    .table-container {
        background: var(--white);
        padding: 2.5rem;
        border-radius: 12px;
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
    }

    h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    h2 i {
        color: var(--primary-color);
    }

    .table-container>p {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin-bottom: 2rem;
    }

    .form-section-flex {
        display: flex;
        gap: 3rem;
        align-items: flex-start;
        margin-bottom: 2rem;
    }

    .logo-upload-section {
        flex: 0 0 220px;
        text-align: center;
    }

    .logo-upload-section label {
        display: block;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 1rem;
        color: var(--text-main);
    }

    #current-logo-display {
        width: 160px;
        height: 160px;
        margin: 0 auto 1.5rem;
        border: 2px dashed var(--border-color);
        background: #fdfdfd;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    #current-logo-display:hover {
        border-color: var(--primary-color);
        background: #f0f4ff;
    }

    #current-logo-display img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--text-main);
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 0.95rem;
        color: var(--text-main);
        background: var(--white);
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        border: none;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--secondary-color);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.25);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .preview-container {
        max-width: 100%;
        background: #f8fafc;
        padding: 1rem;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        margin: 1.5rem 0;
        display: none;
    }

    #image-to-crop {
        display: block;
        max-width: 100%;
        border-radius: 4px;
    }

    @media (max-width: 768px) {
        .form-section-flex {
            flex-direction: column;
            gap: 2rem;
        }

        .logo-upload-section {
            width: 100%;
            flex: none;
        }

        .form-row-flex {
            flex-direction: column;
            gap: 0 !important;
        }
    }
</style>

<body>
    <?php $basePath = '../../';
    include '../../includes/header.php'; ?>

    <main>
        <div class="table-container">
            <h2><i class="fas fa-id-card"></i> Identidade da Empresa</h2>
            <p>Configure como sua empresa aparece nos documentos e relatórios.</p>
            <br>

            <form action="save_identity.php" method="POST" enctype="multipart/form-data">
                <div class="form-section-flex">
                    <div class="logo-upload-section">
                        <label>Logotipo da Empresa</label>
                        <div id="current-logo-display">
                            <?php if (!empty($settings['company_logo'])): ?>
                                <img src="../../assets/img/<?= $settings['company_logo'] ?>">
                            <?php else: ?>
                                <i class="fas fa-user-tie fa-4x" style="color: #cbd5e0;"></i>
                            <?php endif; ?>
                        </div>

                        <input type="file" id="input-logo" accept="image/*" class="form-control"
                            style="margin-bottom: 10px;">
                        <input type="hidden" name="cropped_logo" id="cropped_logo">
                    </div>

                    <div style="flex: 1;">
                        <div class="form-group">
                            <label>Nome Fantasia</label>
                            <input type="text" name="company_name" class="form-control"
                                value="<?= $settings['company_name'] ?? '' ?>" placeholder="Nome da sua empresa">
                        </div>
                        <div class="form-row-flex" style="display: flex; gap: 15px;">
                            <div class="form-group" style="flex: 1;">
                                <label>Telefone</label>
                                <input type="text" name="company_phone" class="form-control"
                                    value="<?= $settings['company_phone'] ?? '' ?>" placeholder="(00) 00000-0000">
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label>Endereço</label>
                                <input type="text" name="company_address" class="form-control"
                                    value="<?= $settings['company_address'] ?? '' ?>"
                                    placeholder="Rua, número, bairro, cidade">
                            </div>
                        </div>
                    </div>
                </div>

                <div id="editor-wrapper" class="preview-container">
                    <img id="image-to-crop">
                    <button type="button" id="btn-crop" class="btn btn-primary"
                        style="margin-top: 15px; width: 100%; justify-content: center;">
                        <i class="fas fa-crop"></i> Confirmar Recorte
                    </button>
                </div>

                <div class="form-group">
                    <label>Rodapé dos Documentos (Mensagem Final)</label>
                    <textarea name="pdf_footer" class="form-control" rows="3"
                        placeholder="Ex: Agradecemos a preferência! Volte sempre."><?= $settings['pdf_footer'] ?? '' ?></textarea>
                </div>

                <div
                    style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 2rem; text-align: right;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Identidade
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
<script src="../../js/main.js"></script>
<script>
    let cropper;
    const inputLogo = document.getElementById('input-logo');
    const imageToCrop = document.getElementById('image-to-crop');
    const editorWrapper = document.getElementById('editor-wrapper');
    const croppedInput = document.getElementById('cropped_logo');

    inputLogo.addEventListener('change', function (e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const reader = new FileReader();
            reader.onload = function (event) {
                imageToCrop.src = event.target.result;
                editorWrapper.style.display = 'block';

                if (cropper) cropper.destroy();

                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    guides: true,
                });
            };
            reader.readAsDataURL(files[0]);
        }
    });

    document.getElementById('btn-crop').addEventListener('click', function () {
        const canvas = cropper.getCroppedCanvas({ width: 300, height: 300 });
        const base64Image = canvas.toDataURL('image/png');
        croppedInput.value = base64Image;
        document.getElementById('current-logo-display').innerHTML = `<img src="${base64Image}" style="width: 100%; height: 100%; object-fit: cover;">`;

        editorWrapper.style.display = 'none';
        alert('Imagem recortada com sucesso! Não esqueça de clicar em Salvar Identidade.');
    });
</script>

</html>