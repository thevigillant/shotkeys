<?php
require __DIR__ . '/../config.php';
require_login();

// Handle Form Submit
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $template = $_POST['email_template'] ?? '';
    if ($template) {
        // Save to DB
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('email_template', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$template, $template]);
        $message = "Template atualizado com sucesso!";
    }
}

// Fetch Current Template
$currentTemplate = '';
try {
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'email_template'");
    $stmt->execute();
    $currentTemplate = $stmt->fetchColumn() ?: "Olá {name},\n\nAqui está sua key: {key}\n\nObrigado!";
} catch (Exception $e) {
    // Table might not exist yet
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Configuração de Email | ShotKeys Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="https://shotkeys.store/admin/" />
  
  <!-- CSS Reuse from Dashboard -->
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Rajdhani:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    /* Reuse same styles as dashboard.php for consistency */
    :root { --neon-blue: #00f3ff; --neon-purple: #bc13fe; --dark-bg: #050510; --panel-bg: rgba(20, 20, 35, 0.7); --border-color: rgba(255, 255, 255, 0.1); }
    body { background-color: var(--dark-bg); color: #fff; font-family: 'Rajdhani', sans-serif; margin: 0; background-image: radial-gradient(circle at 10% 20%, rgba(188, 19, 254, 0.1) 0%, transparent 20%), radial-gradient(circle at 90% 80%, rgba(0, 243, 255, 0.1) 0%, transparent 20%); }
    .admin-container { display: flex; min-height: 100vh; }
    .sidebar { width: 260px; background: rgba(10, 10, 20, 0.95); border-right: 1px solid var(--border-color); padding: 2rem; display: flex; flex-direction: column; }
    .brand { font-family: 'Orbitron', sans-serif; font-size: 1.5rem; color: var(--neon-blue); margin-bottom: 3rem; }
    .nav-item { display: block; padding: 1rem; color: rgba(255, 255, 255, 0.6); text-decoration: none; margin-bottom: 0.5rem; font-weight: 500; transition: 0.3s; border-radius: 8px; }
    .nav-item:hover, .nav-item.active { background: rgba(0, 243, 255, 0.1); color: var(--neon-blue); border-left: 3px solid var(--neon-blue); }
    .main-content { flex: 1; padding: 3rem; }
    .header-title { font-family: 'Orbitron', sans-serif; font-size: 2rem; margin-bottom: 2rem; background: linear-gradient(90deg, #fff, rgba(255,255,255,0.5)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .config-panel { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 2rem; }
    .form-label { display: block; margin-bottom: 0.5rem; color: var(--neon-blue); font-weight: 500; }
    .form-control { width: 100%; background: rgba(0, 0, 0, 0.3); border: 1px solid var(--border-color); color: #fff; padding: 1rem; border-radius: 8px; font-family: 'Rajdhani', sans-serif; font-size: 1rem; transition: all 0.3s; }
    .form-control:focus { outline: none; border-color: var(--neon-purple); box-shadow: 0 0 15px rgba(188, 19, 254, 0.2); }
    .btn-save { background: linear-gradient(135deg, var(--neon-purple), #9d00e0); border: none; color: #fff; padding: 1rem 2rem; border-radius: 8px; font-family: 'Orbitron', sans-serif; font-weight: 700; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; transition: all 0.3s; margin-top: 1rem; }
    .btn-save:hover { transform: scale(1.05); box-shadow: 0 0 30px rgba(188, 19, 254, 0.4); }
    .alert-success { background: rgba(0, 255, 0, 0.2); color: #5eff5e; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border: 1px solid #00ff00; }
  </style>
</head>
<body>

<div class="admin-container">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>

  <main class="main-content">
    <h1 class="header-title">AUTOMACAÇÃO DE EMAILS</h1>

    <?php if ($message): ?>
        <div class="alert-success"><?= $message ?></div>
    <?php endif; ?>

    <div class="config-panel">
      <form method="post">
        <div class="form-group">
          <label class="form-label">Modelo de E-mail de Entrega</label>
          <p style="color: rgba(255,255,255,0.5); font-size: 0.9rem; margin-bottom: 1rem;">
             Variáveis disponíveis: <code>{name}</code> (Nome do Cliente), <code>{key}</code> (Código do Produto), <code>{product}</code> (Nome do Jogo).
          </p>
          <textarea name="email_template" rows="10" class="form-control" spellcheck="false"><?= htmlspecialchars($currentTemplate) ?></textarea>
        </div>
        
        <button type="submit" class="btn-save">Salvar Alterações</button>
      </form>
    </div>
  </main>
</div>

</body>
</html>
