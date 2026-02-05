<?php
require __DIR__ . '/../config.php';

if (!is_logged_in() || ($_SESSION['user_role'] ?? 'user') !== 'admin') {
    die("Acesso negado.");
}

$message = '';

// Handle Product Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_product'])) {
    $title = $_POST['title'];
    $slug = $_POST['slug'];
    $price = (int)$_POST['price_cents'];
    $type = $_POST['type'];
    $platform = $_POST['platform'];
    $desc = $_POST['description'];

    try {
        $stmt = $pdo->prepare("INSERT INTO products (title, slug, price_cents, type, platform, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $slug, $price, $type, $platform, $desc]);
        $message = "Produto criado com sucesso!";
    } catch (PDOException $e) {
        $message = "Erro: " . $e->getMessage();
    }
}

// Fetch Products
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Gerenciar Produtos | Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>" />
</head>
<body>

  <?php include __DIR__ . '/../includes/navbar.php'; ?>

  <main class="container py-5" style="margin-top: 60px;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 text-white">Produtos</h1>
            <a href="admin/index.php" class="text-white-50 small">&larr; Voltar</a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- List -->
        <div class="col-lg-8">
            <div class="glass-panel">
                <table class="table table-dark table-hover mb-0" style="background: transparent;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Preço (cents)</th>
                            <th>Tipo</th>
                            <th>Platform</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['title']) ?></td>
                            <td><?= $p['price_cents'] ?></td>
                            <td><?= $p['type'] ?></td>
                            <td><?= $p['platform'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form -->
        <div class="col-lg-4">
            <div class="glass-panel">
                <h3 class="h5 text-white mb-3">Novo Produto</h3>
                <form method="POST">
                    <div class="mb-3">
                        <label class="text-white-50 small">Título</label>
                        <input type="text" name="title" class="form-control bg-dark border-secondary text-white" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 small">Slug</label>
                        <input type="text" name="slug" class="form-control bg-dark border-secondary text-white" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 small">Preço (Centavos)</label>
                        <input type="number" name="price_cents" class="form-control bg-dark border-secondary text-white" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 small">Tipo</label>
                        <select name="type" class="form-select bg-dark border-secondary text-white">
                            <option value="OWN_KEY">Key Própria</option>
                            <option value="RANDOM_BOX">Random Box</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 small">Plataforma</label>
                        <input type="text" name="platform" class="form-control bg-dark border-secondary text-white" placeholder="Steam, Epic...">
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 small">Descrição</label>
                        <textarea name="description" class="form-control bg-dark border-secondary text-white" rows="3"></textarea>
                    </div>
                    <button type="submit" name="create_product" class="btn btn-custom w-100">Criar Produto</button>
                </form>
            </div>
        </div>
    </div>

  </main>
</body>
</html>
