<?php
require __DIR__ . '/config.php';
require_login();

// FORCE REFRESH ROLE: Ensure session matches DB (in case of manual promotion)
if (is_logged_in()) {
    $stmt = $pdo->prepare("SELECT role, name FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Painel - ShotKeys</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Base URL -->
  <base href="https://shotkeys.store" />

  <!-- FavIcon -->
  <link rel="icon" href="assets/icons/favicon/logo-Shot-Keys.ico" type="image/x-icon" />

  <!-- Google Fonts -->
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Momo+Trust+Display&display=swap");
    @import url("https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap");
    @import url("https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap");
  </style>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>" />
</head>
<body>

  <!-- Navbar Global -->
  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <main class="container py-5" style="margin-top: 40px;">
    <div class="glass-panel">
      <!-- Header do Painel -->
      <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-4 mb-5">
        <div>
          <h1 class="h2 archivofont text-white mb-2">
            Olá, <span class="text-accent"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuário') ?></span>!
          </h1>
          <p class="text-white-50">
            Bem-vindo ao seu QG. Aqui você gerencia seu arsenal.
          </p>
        </div>
        <div class="d-flex gap-3">
        <div class="d-flex gap-3">
             <span class="badge bg-custom rounded-pill d-flex align-items-center px-3" style="background: rgba(230,0,230,0.2); border: 1px solid var(--color-accent); color: var(--color-accent);">
                Conta Ativa
             </span>
             <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a href="admin/dashboard.php" class="btn btn-outline-light rounded-pill px-4" style="border-color: #00f3ff; color: #00f3ff; box-shadow: 0 0 10px rgba(0, 243, 255, 0.3);">
                    👑 Painel Admin
                </a>
             <?php endif; ?>
        </div>
        </div>
      </div>

      <!-- Grid de Ações -->
      <div class="row g-4">
        <!-- Últimos Pedidos -->
        <div class="col-md-4">
          <div class="p-4 rounded-3 h-100 bg-dark-transparent border border-secondary border-opacity-25">
             <div class="d-flex align-items-center gap-2 mb-3 text-accent">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                <h3 class="h5 mb-0 fw-bold">Meus Pedidos</h3>
             </div>
             <p class="text-white-50 small mb-4">
               Acompanhe o status das suas compras e acesse suas chaves de ativação.
             </p>
             <a href="meus_pedidos.php" class="btn btn-outline-light w-100 rounded-pill">Ver histórico</a>
          </div>
        </div>

        <!-- Catálogo -->
        <div class="col-md-4">
          <div class="p-4 rounded-3 h-100 bg-dark-transparent border border-secondary border-opacity-25">
             <div class="d-flex align-items-center gap-2 mb-3 text-accent">
               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
               <h3 class="h5 mb-0 fw-bold">Nova Compra</h3>
             </div>
             <p class="text-white-50 small mb-4">
               Explore o catálogo e encontre novos jogos para sua coleção.
             </p>
             <a href="products.php" class="btn btn-custom w-100">Explorar Loja</a>
          </div>
        </div>

        <!-- Suporte -->
        <div class="col-md-4">
          <div class="p-4 rounded-3 h-100 bg-dark-transparent border border-secondary border-opacity-25">
             <div class="d-flex align-items-center gap-2 mb-3 text-accent">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                <h3 class="h5 mb-0 fw-bold">Suporte</h3>
             </div>
             <p class="text-white-50 small mb-4">
               Teve algum problema com uma chave? Entre em contato conosco.
             </p>
             <button class="btn btn-outline-light w-100 rounded-pill" disabled>Em breve</button>
          </div>
        </div>
      </div>

    </div>
  </main>

  <!-- Floating Admin Button -->
  <?php if (isset($_SESSION['user_id'])): ?>
  <a href="admin/dashboard.php" target="_blank" class="admin-float-btn" title="Acessar Painel Admin">
    👑
  </a>
  <style>
    .admin-float-btn {
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 60px;
      height: 60px;
      background: #00f3ff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 30px;
      box-shadow: 0 0 20px rgba(0, 243, 255, 0.6);
      z-index: 9999;
      text-decoration: none;
      transition: transform 0.3s;
      animation: pulse 2s infinite;
    }
    .admin-float-btn:hover {
      transform: scale(1.1);
    }
    @keyframes pulse {
      0% { box-shadow: 0 0 0 0 rgba(0, 243, 255, 0.7); }
      70% { box-shadow: 0 0 0 15px rgba(0, 243, 255, 0); }
      100% { box-shadow: 0 0 0 0 rgba(0, 243, 255, 0); }
    }
  </style>
  <?php endif; ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
