<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../config.php';
require __DIR__ . '/../services/KeyService.php';
require_login();

// Admin Permission Check
$userEmail = $_SESSION['user_email'] ?? '';
$userRole = $_SESSION['user_role'] ?? '';
$isSuperAdmin = ($userEmail === 'brunosantanareisfc@gmail.com');
$isAdminInfo = ($userRole === 'admin');

if (!$isSuperAdmin && !$isAdminInfo) {
     die("Acesso Negado.");
}

$message = '';
$error_msg = '';

// Fetch products suitable for key import (Digital Keys or Random Boxes)
// Note: 'OWN_KEY' is likely 'KEY' in our enum. Let's check products.php enums. 
// products.php uses: KEY, RANDOM_BOX, AFFILIATE. Affiliate doesn't need stock.
$products = $pdo->query("SELECT id, title, type FROM products WHERE type IN ('KEY', 'RANDOM_BOX') ORDER BY id DESC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int)($_POST['product_id'] ?? 0);
    $csvFile = $_FILES['csv_file'] ?? null;

    if ($productId && $csvFile && $csvFile['error'] === UPLOAD_ERR_OK) {
        $file = $csvFile['tmp_name'];
        if (is_uploaded_file($file)) {
            try {
                $handle = fopen($file, "r");
                $count = 0;
                
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO product_keys (product_id, key_encrypted, hash_partial, status) VALUES (?, ?, ?, 'available')");

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Assume CSV format: KEY_VALUE (single column)
                    $plainKey = trim($data[0]);
                    if (empty($plainKey)) continue;

                    $encrypted = KeyService::encrypt($plainKey);
                    $hash = KeyService::generateHashPartial($plainKey); // Used to identify duplicates without decrypting

                    $stmt->execute([$productId, $encrypted, $hash]);
                    $count++;
                }
                
                $pdo->commit();
                fclose($handle);
                $message = "Sucesso! $count keys importadas para o estoque.";

            } catch (Exception $e) {
                $pdo->rollBack();
                $error_msg = "Erro na importação: " . $e->getMessage();
            }
        } else {
             $error_msg = "Erro no upload do arquivo.";
        }
    } else {
        $error_msg = "Selecione um produto e um arquivo CSV válido.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Importar Keys | ShotKeys Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="https://shotkeys.store/admin/" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Rajdhani:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    /* Admin UI v2 Consistency */
    :root { --neon-blue: #00f3ff; --neon-purple: #bc13fe; --dark-bg: #050510; --panel-bg: rgba(20, 20, 35, 0.95); --border-color: rgba(255, 255, 255, 0.1); }
    body { background-color: var(--dark-bg); color: #fff; font-family: 'Rajdhani', sans-serif; margin: 0; background-image: radial-gradient(circle at 50% 50%, rgba(0, 50, 100, 0.1) 0%, transparent 50%); }
    .admin-container { display: flex; min-height: 100vh; }
    .sidebar { width: 260px; background: #0a0a0f; border-right: 1px solid var(--border-color); padding: 2rem; display: flex; flex-direction: column; }
    .nav-item { display: block; padding: 1rem; color: #888; text-decoration: none; margin-bottom: 0.5rem; transition: 0.3s; border-radius: 8px; font-weight: 500; }
    .nav-item:hover, .nav-item.active { background: rgba(0, 243, 255, 0.1); color: var(--neon-blue); border-left: 3px solid var(--neon-blue); }
    .main-content { flex: 1; padding: 3rem; overflow-x: auto; }
    
    .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .page-title { font-family: 'Orbitron'; font-size: 2rem; text-shadow: 0 0 10px rgba(255,255,255,0.2); margin: 0; }

    /* Form Styles */
    .config-panel { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 2rem; max-width: 600px; margin: 0 auto; }
    .form-label { display: block; margin-bottom: 0.5rem; color: var(--neon-blue); font-size: 0.9rem; }
    .form-control, .form-select { width: 100%; padding: 10px; background: rgba(0,0,0,0.5); border: 1px solid var(--border-color); color: #fff; border-radius: 4px; font-family: 'Rajdhani'; }
    .form-control:focus, .form-select:focus { outline: none; border-color: var(--neon-blue); box-shadow: 0 0 10px rgba(0, 243, 255, 0.2); background: rgba(0,0,0,0.7); color: #fff; }
    
    .btn-create { background: var(--neon-blue); color: #000; padding: 10px 20px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-family: 'Orbitron'; width: 100%; margin-top: 1rem; transition: 0.3s; }
    .btn-create:hover { box-shadow: 0 0 20px rgba(0, 243, 255, 0.4); }

    .key-stats { font-size: 0.9rem; color: #888; margin-bottom: 2rem; text-align: center; }
  </style>
</head>
<body>

<div class="admin-container">
  <?php include __DIR__ . '/includes/sidebar.php'; ?>

  <main class="main-content">
    
    <?php if ($message): ?>
        <div style="background: rgba(0, 255, 153, 0.2); border: 1px solid #00ff99; color: #00ff99; padding: 1rem; margin-bottom: 2rem; border-radius: 8px;">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
        <div style="background: rgba(255, 0, 85, 0.2); border: 1px solid #ff0055; color: #ff0055; padding: 1rem; margin-bottom: 2rem; border-radius: 8px;">
            <?= htmlspecialchars($error_msg) ?>
        </div>
    <?php endif; ?>

    <div class="panel-header">
        <h1 class="page-title text-center w-100">IMPORTAR ESTOQUE (KEYS)</h1>
    </div>

    <div class="config-panel">
        <p class="key-stats">
            Cadastre novas chaves de ativação enviando um arquivo .CSV (uma chave por linha).
        </p>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="form-label">Selecione o Produto (Jogo/Software)</label>
                <select name="product_id" class="form-select" required>
                    <option value="">-- Escolha --</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>">
                            <?= htmlspecialchars($p['title']) ?> (<?= $p['type'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Arquivo CSV (.csv)</label>
                <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                <div style="font-size: 0.8rem; color: #666; margin-top: 5px;">
                    Formato: Apenas uma coluna com as chaves. Sem cabeçalho.
                </div>
            </div>

            <button type="submit" class="btn-create">
                IMPORTAR AGORA
            </button>
        </form>
    </div>

  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
