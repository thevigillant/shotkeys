<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../services/KeyService.php';

if (!is_logged_in() || ($_SESSION['user_role'] ?? 'user') !== 'admin') {
    die("Acesso negado.");
}

$message = '';
$products = $pdo->query("SELECT id, title FROM products WHERE type IN ('OWN_KEY', 'RANDOM_BOX')")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $productId = (int)$_POST['product_id'];
    $file = $_FILES['csv_file']['tmp_name'];

    if ($productId && is_uploaded_file($file)) {
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
                $hash = KeyService::generateHashPartial($plainKey);

                $stmt->execute([$productId, $encrypted, $hash]);
                $count++;
            }
            
            $pdo->commit();
            fclose($handle);
            $message = "Sucesso! $count keys importadas.";

        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "Erro na importação: " . $e->getMessage();
        }
    } else {
        $message = "Selecione um produto e um arquivo CSV válido.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Importar Keys | Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>" />
</head>
<body>

  <?php include __DIR__ . '/../includes/navbar.php'; ?>

  <main class="container py-5" style="margin-top: 60px;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 text-white">Importar Keys</h1>
            <a href="admin/index.php" class="text-white-50 small">&larr; Voltar</a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="glass-panel">
                <p class="text-white-50 mb-4">
                    Faça upload de um arquivo CSV contendo apenas uma coluna com as chaves (sem cabeçalho).
                </p>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="text-white small mb-2">Selecione o Produto</label>
                        <select name="product_id" class="form-select bg-dark border-secondary text-white" required>
                            <option value="">-- Escolha --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['id'] ?>">ID <?= $p['id'] ?> - <?= htmlspecialchars($p['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="text-white small mb-2">Arquivo CSV</label>
                        <input type="file" name="csv_file" class="form-control bg-dark text-white border-secondary" accept=".csv" required>
                    </div>

                    <button type="submit" class="btn btn-custom w-100">
                        Importar Estoque
                    </button>
                </form>
            </div>
        </div>
    </div>

  </main>
</body>
</html>
