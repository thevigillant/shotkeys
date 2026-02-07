<?php
require __DIR__ . '/config.php';

try {
    // 1. Create Settings Table for Email Templates
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(50) UNIQUE NOT NULL,
            setting_value TEXT,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // 2. Insert Default Email Template if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = 'email_template'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $defaultTemplate = "Olá {name},\n\nObrigado pela sua compra!\n\nAqui está sua chave de ativação:\n\n{key}\n\nDivirta-se!\nEquipe ShotKeys";
        $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('email_template', ?)")->execute([$defaultTemplate]);
    }

    // 3. Promote User to Admin (Change this email to your account email)
    // We will assume the user currently logged in or a specific email is admin.
    // Let's create a generic admin or update the user based on a GET parameter
    if (isset($_GET['admin_email'])) {
        $email = $_GET['admin_email'];
        $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE email = ?");
        $stmt->execute([$email]);
        echo "Usuário $email promovido para ADMIN.<br>";
    }

    echo "Tabela de configurações criada com sucesso.<br>";
    echo "Para promover seu usuário a admin, acesse: setup_admin.php?admin_email=SEU_EMAIL";

} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}
