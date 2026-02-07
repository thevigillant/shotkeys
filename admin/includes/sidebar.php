<?php
// Detect current page to set active class
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div style="font-family: 'Orbitron'; font-size: 1.5rem; color: var(--neon-blue); margin-bottom: 2rem; cursor: pointer;" onclick="window.location.href='dashboard.php'">SK // ADMIN</div>
    <nav>
      <a href="dashboard.php" class="nav-item <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
      <a href="users.php" class="nav-item <?= $currentPage == 'users.php' ? 'active' : '' ?>">Gerenciar Usuários</a>
      <a href="products.php" class="nav-item <?= $currentPage == 'products.php' ? 'active' : '' ?>">Gerenciar Produtos</a>
      <a href="keys.php" class="nav-item <?= $currentPage == 'keys.php' ? 'active' : '' ?>">Importar Keys</a>
      <a href="settings.php" class="nav-item <?= $currentPage == 'settings.php' ? 'active' : '' ?>">Config Emails</a>
      
      <div style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
          <a href="../index.php" class="nav-item" style="color: #fff;">&larr; Voltar Loja</a>
          <a href="../logout.php" class="nav-item" style="color: #ff0055;">Sair</a>
      </div>
    </nav>
</aside>
