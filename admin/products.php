<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../config.php';
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

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $_POST['id'] ?? '';
        $title = $_POST['title'];
        $slug = $_POST['slug'] ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $description = $_POST['description'];
        $price = (int)($_POST['price'] * 100); // Convert to cents
        $category = $_POST['category'];
        $type = $_POST['type'];
        $image_url = $_POST['image_url'];
        $affiliate_url = $_POST['affiliate_url'] ?? '';

        if (!empty($id)) {
            // UPDATE
            $stmt = $pdo->prepare("UPDATE products SET title=?, slug=?, description=?, price_cents=?, category=?, type=?, image_url=?, affiliate_url=? WHERE id=?");
            $stmt->execute([$title, $slug, $description, $price, $category, $type, $image_url, $affiliate_url, $id]);
            $message = "Produto atualizado com sucesso!";
        } else {
            // INSERT
            $stmt = $pdo->prepare("INSERT INTO products (title, slug, description, price_cents, category, type, image_url, affiliate_url, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'ACTIVE')");
            $stmt->execute([$title, $slug, $description, $price, $category, $type, $image_url, $affiliate_url]);
            $message = "Produto criado com sucesso!";
        }
    } catch (PDOException $e) {
        $error_msg = "Erro ao salvar: " . $e->getMessage();
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    try {
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$_GET['delete']]);
        $message = "Produto removido.";
    } catch (PDOException $e) {
        $error_msg = "Erro ao deletar: " . $e->getMessage();
    }
}

// Fetch All Products
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Gerenciar Produtos | ShotKeys Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="https://shotkeys.store/admin/" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Rajdhani:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    /* Same Admin Theme */
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
    .edit-form { background: rgba(255,255,255,0.05); padding: 20px; border-radius: 10px; border: 1px solid var(--border-color); margin-bottom: 2rem; display: none; }
    .form-group { margin-bottom: 1rem; }
    .form-label { display: block; margin-bottom: 0.5rem; color: var(--neon-blue); font-size: 0.9rem; }
    .form-control { width: 100%; padding: 10px; background: rgba(0,0,0,0.5); border: 1px solid var(--border-color); color: #fff; border-radius: 4px; font-family: 'Rajdhani'; }
    .form-control:focus { outline: none; border-color: var(--neon-blue); box-shadow: 0 0 10px rgba(0, 243, 255, 0.2); }
    
    .btn-create { background: var(--neon-blue); color: #000; padding: 10px 20px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-family: 'Orbitron'; }
    .btn-save { background: var(--neon-purple); color: #fff; padding: 10px 20px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; width: 100%; margin-top: 10px; }
    
    table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    th { text-align: left; padding: 1rem; color: var(--neon-blue); font-family: 'Orbitron'; font-size: 0.8rem; text-transform: uppercase; }
    td { padding: 1rem; background: var(--panel-bg); border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); vertical-align: middle; }
    tr td:first-child { border-left: 1px solid var(--border-color); border-radius: 8px 0 0 8px; }
    tr td:last-child { border-right: 1px solid var(--border-color); border-radius: 0 8px 8px 0; }
    
    .action-btn { font-size: 0.8rem; padding: 5px 10px; border-radius: 4px; text-decoration: none; margin-right: 5px; display: inline-block; }
    .edit-btn { background: rgba(0, 243, 255, 0.1); color: var(--neon-blue); border: 1px solid var(--neon-blue); }
    .delete-btn { background: rgba(255, 0, 85, 0.1); color: #ff0055; border: 1px solid #ff0055; }

    img.prod-thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #333; }
  </style>
</head>
<body>

<div class="admin-container">
  <aside class="sidebar">
    <div style="font-family: 'Orbitron'; font-size: 1.5rem; color: var(--neon-blue); margin-bottom: 2rem;">SK // ADMIN</div>
    <nav>
      <a href="dashboard.php" class="nav-item">Dashboard</a>
      <a href="users.php" class="nav-item">Gerenciar Usuários</a>
      <a href="products.php" class="nav-item active">Gerenciar Produtos</a>
      <a href="settings.php" class="nav-item">Config Emails</a>
      <a href="../index.php" class="nav-item">Voltar Loja</a>
    </nav>
  </aside>

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
        <h1 class="page-title">ARSENAL (PRODUTOS)</h1>
        <button class="btn-create" data-bs-toggle="modal" data-bs-target="#productModal" onclick="document.getElementById('f_id').value=''; document.getElementById('mainForm').reset()">+ NOVO ITEM</button>
    </div>

    <!-- Product List -->
    <table>
        <thead>
            <tr>
                <th>Img</th>
                <th>Nome</th>
                <th>Cat / Tipo</th>
                <th>Preço</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td><img src="<?= htmlspecialchars(($p['image_url'] ?? '') ?: '../assets/keys/glock/glock.png') ?>" class="prod-thumb"></td>
                <td>
                    <div style="font-weight: bold;"><?= htmlspecialchars($p['title']) ?></div>
                    <div style="font-size: 0.8rem; color: #666;"><?= htmlspecialchars($p['slug']) ?></div>
                </td>
                <td>
                    <span style="color: var(--neon-blue)"><?= htmlspecialchars($p['category'] ?? 'Geral') ?></span><br>
                    <span style="font-size: 0.8rem; color: #888"><?= htmlspecialchars($p['type']) ?></span>
                </td>
                <td style="color: #00ff99; font-family: 'Orbitron'">
                    R$ <?= number_format($p['price_cents']/100, 2, ',', '.') ?>
                </td>
                <td>
                    <button onclick='editProduct(<?= json_encode($p) ?>)' class="action-btn edit-btn">EDITAR</button>
                    <a href="products.php?delete=<?= $p['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Tem certeza?')">X</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

  </main>
</div>

    <!-- Modal do Produto -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true" style="color: #000;">
      <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: #1a1a2e; border: 1px solid var(--neon-blue); color: #fff;">
          <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
            <h5 class="modal-title" style="font-family: 'Orbitron'">DADOS DO PRODUTO</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" id="mainForm">
                <input type="hidden" name="id" id="f_id">
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Título</label>
                        <input type="text" name="title" id="f_title" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slug (URL)</label>
                        <input type="text" name="slug" id="f_slug" class="form-control" placeholder="Automático se vazio">
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Descrição</label>
                        <textarea name="description" id="f_description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Preço (R$)</label>
                        <input type="number" step="0.01" name="price" id="f_price" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Categoria</label>
                        <select name="category" id="f_category" class="form-control">
                            <option value="AAA">AAA</option>
                            <option value="Indie">Indie</option>
                            <option value="FPS">FPS</option>
                            <option value="RPG">RPG</option>
                            <option value="Random">Random Box</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tipo</label>
                        <select name="type" id="f_type" class="form-control">
                            <option value="KEY">Chave (Digital)</option>
                            <option value="RANDOM_BOX">Random Box</option>
                            <option value="AFFILIATE">Afiliado (Link Externo)</option>
                        </select>
                    </div>

                    <div class="col-12">
                         <label class="form-label">URL da Imagem</label>
                         <input type="text" name="image_url" id="f_image_url" class="form-control">
                    </div>
                    <div class="col-12">
                         <label class="form-label">Link de Afiliado (Se aplicável)</label>
                         <input type="text" name="affiliate_url" id="f_affiliate_url" class="form-control">
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn-save" style="width: auto;">SALVAR DADOS</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>

<script>
function editProduct(p) {
    var myModal = new bootstrap.Modal(document.getElementById('productModal'));
    
    document.getElementById('f_id').value = p.id;
    document.getElementById('f_title').value = p.title;
    document.getElementById('f_slug').value = p.slug;
    document.getElementById('f_description').value = p.description;
    document.getElementById('f_price').value = p.price_cents / 100;
    document.getElementById('f_category').value = p.category || 'AAA';
    document.getElementById('f_type').value = p.type;
    document.getElementById('f_image_url').value = p.image_url || '';
    document.getElementById('f_affiliate_url').value = p.affiliate_url || '';
    
    myModal.show();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
