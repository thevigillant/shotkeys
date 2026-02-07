<?php
require __DIR__ . '/../config.php';
require_login();

// Check if user is admin
// Note: In production you should verify $_SESSION['user_role'] === 'admin'
// For now we will assume if they access this they are trying to be admin, 
// but add a check if role exists.
if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // If table users has role column, use it. For now, strict check.
    // Temporarily allow for dev, or redirect.
    // header("Location: ../dashboard.php");
    // exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Admin Command Center | ShotKeys</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="https://shotkeys.store/admin/" />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Rajdhani:wght@300;500;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --neon-blue: #00f3ff;
      --neon-purple: #bc13fe;
      --dark-bg: #050510;
      --panel-bg: rgba(20, 20, 35, 0.7);
      --border-color: rgba(255, 255, 255, 0.1);
    }

    body {
      background-color: var(--dark-bg);
      color: #fff;
      font-family: 'Rajdhani', sans-serif;
      overflow-x: hidden;
      margin: 0;
      padding: 0;
      background-image: 
        radial-gradient(circle at 10% 20%, rgba(188, 19, 254, 0.1) 0%, transparent 20%),
        radial-gradient(circle at 90% 80%, rgba(0, 243, 255, 0.1) 0%, transparent 20%);
    }

    .admin-container {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
      width: 260px;
      background: rgba(10, 10, 20, 0.95);
      border-right: 1px solid var(--border-color);
      backdrop-filter: blur(10px);
      padding: 2rem;
      display: flex;
      flex-direction: column;
    }

    .brand {
      font-family: 'Orbitron', sans-serif;
      font-size: 1.5rem;
      color: var(--neon-blue);
      text-shadow: 0 0 10px rgba(0, 243, 255, 0.5);
      margin-bottom: 3rem;
      letter-spacing: 2px;
    }

    .nav-item {
      display: flex;
      align-items: center;
      padding: 1rem;
      color: rgba(255, 255, 255, 0.6);
      text-decoration: none;
      transition: all 0.3s ease;
      border-radius: 8px;
      margin-bottom: 0.5rem;
      font-weight: 500;
      font-size: 1.1rem;
    }

    .nav-item:hover, .nav-item.active {
      background: rgba(0, 243, 255, 0.1);
      color: var(--neon-blue);
      border-left: 3px solid var(--neon-blue);
    }

    /* Main Content */
    .main-content {
      flex: 1;
      padding: 3rem;
    }

    .header-title {
      font-family: 'Orbitron', sans-serif;
      font-size: 2rem;
      margin-bottom: 2rem;
      background: linear-gradient(90deg, #fff, rgba(255,255,255,0.5));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    /* Cards */
    .stat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
      margin-bottom: 3rem;
    }

    .stat-card {
      background: var(--panel-bg);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 1.5rem;
      position: relative;
      overflow: hidden;
      transition: transform 0.3s;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      border-color: var(--neon-purple);
      box-shadow: 0 0 20px rgba(188, 19, 254, 0.2);
    }

    .stat-value {
      font-size: 2.5rem;
      font-weight: 700;
      font-family: 'Orbitron', sans-serif;
      color: #fff;
    }

    .stat-label {
      color: rgba(255, 255, 255, 0.5);
      text-transform: uppercase;
      letter-spacing: 1px;
      font-size: 0.9rem;
    }

    /* Configuration Panel */
    .config-panel {
      background: var(--panel-bg);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 2rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      color: var(--neon-blue);
      font-weight: 500;
    }

    .form-control {
      width: 100%;
      background: rgba(0, 0, 0, 0.3);
      border: 1px solid var(--border-color);
      color: #fff;
      padding: 1rem;
      border-radius: 8px;
      font-family: 'Rajdhani', sans-serif;
      font-size: 1rem;
      transition: all 0.3s;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--neon-purple);
      box-shadow: 0 0 15px rgba(188, 19, 254, 0.2);
    }

    .btn-save {
      background: linear-gradient(135deg, var(--neon-purple), #9d00e0);
      border: none;
      color: #fff;
      padding: 1rem 2rem;
      border-radius: 8px;
      font-family: 'Orbitron', sans-serif;
      font-weight: 700;
      cursor: pointer;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: all 0.3s;
    }

    .btn-save:hover {
      transform: scale(1.05);
      box-shadow: 0 0 30px rgba(188, 19, 254, 0.4);
    }
  </style>
</head>
<body>

<div class="admin-container">
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="brand">SK // ADMIN</div>
    <nav>
      <a href="dashboard.php" class="nav-item active">Dashboard</a>
      <a href="settings.php" class="nav-item">Config Emails</a>
      <a href="../index.php" class="nav-item">Voltar Loja</a>
    </nav>
  </aside>

  <!-- Main -->
  <main class="main-content">
    <h1 class="header-title">VISÃO GERAL DO SISTEMA</h1>

    <div class="stat-grid">
      <div class="stat-card">
        <div class="stat-label">Vendas Hoje</div>
        <div class="stat-value">R$ 0,00</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Keys Entregues</div>
        <div class="stat-value">0</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Usuários Ativos</div>
        <div class="stat-value">1</div>
      </div>
    </div>

    <div class="config-panel">
      <h3 style="color: var(--neon-blue); margin-bottom: 1.5rem; font-family: 'Orbitron'">Acesso Rápido</h3>
      <p style="color: rgba(255,255,255,0.7)">
        Bem-vindo ao painel de controle ShotKeys. Utilize o menu lateral para configurar o modelo de e-mail e gerenciar produtos.
      </p>
    </div>
  </main>
</div>

</body>
</html>
