<?php
require __DIR__ . '/config.php';

// Busca produtos do banco
// REMOVED 'image_url' to avoid SQL errors if column does not exist
$stmt = $pdo->query("
  SELECT id, slug, title, description, type, price_cents, affiliate_url
  FROM products
  WHERE status = 'ACTIVE'
  ORDER BY created_at DESC
");
$products = $stmt->fetchAll();
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
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      
      <?php if (count($products) > 0): ?>
        <?php foreach ($products as $p): ?>
          <div class="col">
            <div class="product-card h-100">
              <!-- Imagem (Fallback se não tiver url) -->
              <?php 
                // Usando somente fallback por enquanto para garantir robustez
                $imgUrl = 'assets/keys/glock/glock.png';
                if (isset($p['image_url']) && !empty($p['image_url'])) {
                    $imgUrl = $p['image_url'];
                }
              ?>
              <img
                src="<?= htmlspecialchars($imgUrl) ?>"
                alt="<?= htmlspecialchars($p['title']) ?>"
                loading="lazy"
                class="card-img-top"
              />
              
              <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?= htmlspecialchars($p['title']) ?></h5>
                <p class="card-text flex-grow-1">
                  <?= htmlspecialchars($p['description']) ?>
                </p>
                
                <div class="mt-auto">
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
                        Comprar Agora
                      </a>

                    <?php else: ?>
                       <div class="d-flex justify-content-between align-items-center mb-3">
                         <span class="badge bg-primary">Produto ShotKeys</span>
                         <span class="fs-4 fw-bold text-white">R$ <?= number_format($p['price_cents']/100, 2, ',', '.') ?></span>
                      </div>
                      <a href="checkout.php?product=<?= $p['slug'] ?>" class="btn btn-custom w-100">
                        Comprar
                      </a>
                    <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center py-5">
           <h3 class="text-white opacity-50">Nenhum produto encontrado no momento.</h3>
           <a href="index.html" class="btn btn-outline-light mt-3">Voltar ao Início</a>
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
