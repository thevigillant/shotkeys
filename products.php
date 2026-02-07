<?php
require __DIR__ . '/config.php';

// Busca produtos do banco
// REMOVED 'image_url' to avoid SQL errors if column does not exist
// Filter Logic
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

// Try to fetch with new columns (Preferred)
try {
    $sql = "SELECT id, slug, title, description, type, price_cents, affiliate_url, category, image_url 
            FROM products 
            WHERE status = 'ACTIVE'";
    $params = [];

    if ($category) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }

    if ($search) {
        $sql .= " AND title LIKE ?";
        $params[] = "%$search%";
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

} catch (PDOException $e) {
    // FALLBACK: If columns 'category' or 'image_url' don't exist yet
    // This prevents the 500 Error
    $stmt = $pdo->prepare("SELECT id, slug, title, description, type, price_cents, affiliate_url FROM products WHERE status = 'ACTIVE' ORDER BY created_at DESC");
    $stmt->execute();
    $products = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Produtos | ShotKeys</title>

  <!-- Base URL -->
  <base href="https://shotkeys.store" />

  <!-- Google Fonts -->
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Momo+Trust+Display&display=swap");
    @import url("https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap");
    @import url("https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap");
  </style>

  <!-- FavIcon -->
  <link rel="icon" href="assets/icons/favicon/logo-Shot-Keys.ico" type="image/x-icon" />

  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous"
  />

  <!-- CSS Principal - Versionado para evitar cache -->
  <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>" />
  
  <style>
    /* Ajuste específico para a página de produtos */
    .catalog-header {
      padding-top: 100px;
      padding-bottom: 40px;
      text-align: center;
    }
  </style>
</head>

<body>

  <!-- Navbar Global -->
  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <!-- Cabeçalho do Catálogo -->
  <header class="container catalog-header">
    <h1 class="archivofont text-uppercase mb-3" style="font-size: 3rem; color: var(--color-accent); text-shadow: 0 0 20px rgba(230,0,230,0.4);">
      Arsenal Disponível
    </h1>
    <p class="lead text-white robotofont opacity-75" style="max-width: 700px; margin: 0 auto;">
      Escolha sua arma. Domine o jogo. Entrega imediata e garantida.
    </p>
  </header>

  <!-- Grid de Produtos -->
  <main class="container pb-5">
  <!-- Search & Filter Section -->
  <section class="container mb-5">
    <div class="glass-panel p-4">
        <form action="products.php" method="GET" class="row g-3 align-items-center">
            
            <!-- Search Bar -->
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-dark-transparent border-0 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                    <input type="text" name="search" class="form-control bg-dark-transparent border-0 text-white" placeholder="Buscar jogo..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
            </div>

            <!-- Categories -->
            <div class="col-md-6">
                <div class="d-flex gap-2 flex-wrap justify-content-md-end">
                    <?php 
                        $cats = ['AAA', 'Indie', 'FPS', 'RPG', 'Random']; 
                        $currentCat = $_GET['category'] ?? '';
                    ?>
                    <a href="products.php" class="btn btn-sm <?= $currentCat == '' ? 'btn-custom' : 'btn-outline-light' ?>">Todos</a>
                    <?php foreach($cats as $c): ?>
                        <a href="products.php?category=<?= $c ?>" class="btn btn-sm <?= $currentCat == $c ? 'btn-custom' : 'btn-outline-light' ?>"><?= $c ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </form>
    </div>
  </section>

  <!-- Grid de Produtos -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      
      <?php if (count($products) > 0): ?>
        <?php foreach ($products as $p): ?>
          <div class="col">
            <div class="product-card h-100">
              <!-- Imagem (Fallback inteligente) -->
              <?php 
                $imgUrl = 'assets/keys/glock/glock.png'; // Default
                if (!empty($p['image_url'])) {
                    $imgUrl = $p['image_url'];
                } else {
                    // Smart Placeholder mapping based on title
                    $titleLower = strtolower($p['title']);
                    if (strpos($titleLower, 'mw3') !== false || strpos($titleLower, 'duty') !== false) $imgUrl = 'assets/keys/ak47/keyak47.png';
                    elseif (strpos($titleLower, 'cyberpunk') !== false) $imgUrl = 'assets/keys/awp/awp.png';
                    elseif (strpos($titleLower, 'valorant') !== false) $imgUrl = 'assets/keys/glock/glock.png';
                }
                
                // Categoria Badge Color
                $catColor = 'bg-secondary';
                $catName = $p['category'] ?? 'Geral';
                if ($catName === 'AAA') $catColor = 'bg-danger';
                if ($catName === 'Indie') $catColor = 'bg-info text-dark';
                if ($catName === 'FPS') $catColor = 'bg-warning text-dark';
              ?>
              
              <div class="ratio ratio-16x9 bg-dark-transparent" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                  <span class="badge <?= $catColor ?>" style="position: absolute; top: 10px; right: 10px; z-index: 10; box-shadow: 0 0 10px rgba(0,0,0,0.5);"><?= $catName ?></span>
                  <img
                    src="<?= htmlspecialchars($imgUrl) ?>"
                    alt="<?= htmlspecialchars($p['title']) ?>"
                    loading="lazy"
                    class="card-img-top w-100 h-100"
                    style="object-fit: cover; object-position: center;"
                  />
              </div>
              
              <div class="card-body d-flex flex-column">
                <h5 class="card-title text-truncate"><?= htmlspecialchars($p['title']) ?></h5>
                <p class="card-text flex-grow-1 small opacity-75">
                  <?= htmlspecialchars($p['description']) ?>
                </p>
                
                <div class="mt-auto pt-3 border-top border-secondary">
                    <?php if ($p['type'] === 'AFFILIATE'): ?>
                      <a href="<?= htmlspecialchars($p['affiliate_url']) ?>" target="_blank" class="btn btn-custom w-100">
                        Ver Oferta Externa
                      </a>

                    <?php elseif ($p['type'] === 'RANDOM_BOX'): ?>
                      <div class="d-flex justify-content-between align-items-center mb-3">
                         <span class="badge bg-secondary">Random Key</span>
                         <span class="fs-4 fw-bold text-white">R$ <?= number_format($p['price_cents']/100, 2, ',', '.') ?></span>
                      </div>
                      <a href="checkout.php?product=<?= $p['slug'] ?>" class="btn btn-custom w-100">
                        Tentara a Sorte
                      </a>

                    <?php else: ?>
                       <div class="d-flex justify-content-between align-items-center mb-2">
                         <span class="fs-4 fw-bold text-accent" style="font-family: 'Orbitron'">R$ <?= number_format($p['price_cents']/100, 2, ',', '.') ?></span>
                      </div>
                      <a href="checkout.php?product=<?= $p['slug'] ?>" class="btn btn-custom w-100 d-flex justify-content-between align-items-center">
                        <span>Comprar</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                      </a>
                    <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center py-5">
           <h3 class="text-white opacity-50">Nenhum produto encontrado com esses filtros.</h3>
           <a href="products.php" class="btn btn-outline-light mt-3">Limpar Filtros</a>
        </div>
      <?php endif; ?>

    </div>
  </main>

  <!-- Rodapé -->
  <footer class="text-center text-white py-4 mt-auto">
    <div class="container">
      <p class="robotofont mb-2">
        © 2025 ShotKeys. Todos os direitos reservados.
      </p>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"
  ></script>
</body>
</html>
