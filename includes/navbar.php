<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <img src="assets/images/nav Shot Keys.png" alt="ShotKeys" height="50" class="d-inline-block align-text-top">
    </a>

    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarNav"
      aria-controls="navbarNav"
      aria-expanded="false"
      aria-label="Alternar navegação"
    >
      <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fs-5 archivofont">
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">Início</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>" href="products.php">Produtos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">FAQ</a>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-3">
        <!-- Carrinho -->
        <a href="#" class="text-white position-relative" aria-label="Carrinho">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
        </a>

        <?php if (is_logged_in()): ?>
          <!-- Logado -->
          <div class="dropdown">
            <a href="#" class="text-white d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <span class="d-none d-lg-inline fw-bold"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuário') ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="userDropdown">
              <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                  <li>
                      <a class="dropdown-item fw-bold text-uppercase" href="admin/dashboard.php" style="color: #00f3ff !important; background: rgba(0, 243, 255, 0.1); border-left: 3px solid #00f3ff; margin-bottom: 5px;">
                          🚀 PAINEL ADMIN
                      </a>
                  </li>
                  <li><hr class="dropdown-divider"></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="dashboard.php">Minha Conta</a></li>
              <li><a class="dropdown-item" href="my_orders.php">Meus Pedidos</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="logout.php">Sair</a></li>
            </ul>
          </div>
        <?php else: ?>
          <!-- Deslogado -->
          <a href="login.php" class="btn btn-sm btn-custom text-white px-3 ms-2">
            Entrar
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
