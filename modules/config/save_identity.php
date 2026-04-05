<?php
require '../../includes/auth.php';
require '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_id = getCompanyId();
    $company_name = $_POST['company_name'];
    $company_phone = $_POST['company_phone'];
    $company_address = $_POST['company_address'];
    $pdf_footer = $_POST['pdf_footer'];
    $cropped_logo = $_POST['cropped_logo'];

    try {
        $pdo->beginTransaction();

        $data = [
            'company_name' => $company_name,
            'company_phone' => $company_phone,
            'company_address' => $company_address,
            'pdf_footer' => $pdf_footer
        ];

        foreach ($data as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO system_settings (company_id, meta_key, meta_value) 
                                   VALUES (?, ?, ?) 
                                   ON DUPLICATE KEY UPDATE meta_value = VALUES(meta_value)");
            $stmt->execute([$company_id, $key, $value]);
        }

        if (!empty($cropped_logo)) {
            $imgData = str_replace('data:image/png;base64,', '', $cropped_logo);
            $imgData = str_replace(' ', '+', $imgData);
            $fileData = base64_decode($imgData);

            $fileName = 'logo_' . $company_id . '_' . time() . '.png';
            $filePath = '../../assets/img/' . $fileName;

            if (file_put_contents($filePath, $fileData)) {
                $stmtLogo = $pdo->prepare("INSERT INTO system_settings (company_id, meta_key, meta_value) 
                                           VALUES (?, 'company_logo', ?) 
                                           ON DUPLICATE KEY UPDATE meta_value = VALUES(meta_value)");
                $stmtLogo->execute([$company_id, $fileName]);
            }
        }

        $pdo->commit();
        header("Location: settings.php?success=1");

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao salvar: " . $e->getMessage());
    }
}