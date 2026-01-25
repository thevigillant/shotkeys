<?php
require __DIR__ . '/config.php';
require_login();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Painel - ShotKeys</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Archivo+Black&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg-dark-primary: #12002b;
      --bg-dark-secondary: #210041;
      --accent-purple: #c725d2;
      --accent-purple-hover: #a21ea9;
      --input-bg-dark: #3a1a5b;
      --text-color-light: #f8f8f8;
      --text-color-muted: #b0a0c0;
      --border-color-dark: #5a3a7b;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, var(--bg-dark-primary) 0%, #0d001a 100%);
      color: var(--text-color-light);
      min-height: 100vh;
    }

    .dashboard-shell {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .dashboard-header {
      padding: 24px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .brand-title {
      font-family: 'Archivo Black', sans-serif;
      letter-spacing: 1px;
      text-transform: uppercase;
      color: var(--accent-purple);
      font-size: 1.4rem;
    }

    .dashboard-card {
      background-color: var(--bg-dark-secondary);
      border-radius: 16px;
      border: 1px solid var(--border-color-dark);
      box-shadow: 0 18px 40px rgba(0, 0, 0, 0.4);
    }

    .dashboard-card .subtitle {
      color: var(--text-color-muted);
      margin-bottom: 0;
    }

    .status-chip {
      background: rgba(199, 37, 210, 0.15);
      color: var(--accent-purple);
      border: 1px solid rgba(199, 37, 210, 0.4);
      padding: 6px 14px;
      border-radius: 999px;
      font-size: 0.85rem;
      font-weight: 600;
    }

    .info-card {
      background: rgba(58, 26, 91, 0.6);
      border-radius: 14px;
      border: 1px solid rgba(90, 58, 123, 0.8);
      padding: 18px;
      height: 100%;
    }

    .info-card h3 {
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 8px;
    }

    .info-card p {
      color: var(--text-color-muted);
      font-size: 0.95rem;
      margin-bottom: 0;
    }

    .btn-primary {
      background-color: var(--accent-purple);
      border-color: var(--accent-purple);
      font-weight: 600;
      border-radius: 10px;
      padding: 10px 18px;
    }

    .btn-primary:hover {
      background-color: var(--accent-purple-hover);
      border-color: var(--accent-purple-hover);
    }

    .btn-outline-light {
      border-radius: 10px;
      font-weight: 600;
    }

    .section-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 16px;
    }
  </style>
</head>
<body>
<div class="dashboard-shell">
  <header class="dashboard-header">
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <div class="brand-title">ShotKeys</div>
        <div class="subtitle">Painel do cliente</div>
      </div>
      <div class="d-flex align-items-center gap-3">
        <span class="status-chip">Conta ativa</span>
        <a href="logout.php" class="btn btn-outline-light">Sair</a>
      </div>
    </div>
  </header>

  <main class="container py-5">
    <div class="dashboard-card p-4 p-lg-5">
      <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-4">
        <div>
          <h1 class="h3 mb-2">Olá, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuário') ?>!</h1>
          <p class="subtitle">Bem-vindo ao seu painel. Aqui ficam seus pedidos, keys e notificações importantes.</p>
        </div>
        <a href="produtos.php" class="btn btn-primary">Explorar ofertas</a>
      </div>

      <div class="row g-3 mt-4">
        <div class="col-md-4">
          <div class="info-card">
            <h3>Última compra</h3>
            <p>Nenhuma compra registrada ainda. Aproveite as promoções da semana.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="info-card">
            <h3>Seus benefícios</h3>
            <p>Receba alertas exclusivos e suporte prioritário para suas próximas keys.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="info-card">
            <h3>Atalhos rápidos</h3>
            <p>Gerencie seus pedidos e acompanhe o status das entregas em poucos cliques.</p>
          </div>
        </div>
      </div>

      <div class="mt-5">
        <div class="section-title">Próximos passos</div>
        <div class="row g-3">
          <div class="col-lg-6">
            <div class="info-card">
              <h3>Complete seu perfil</h3>
              <p>Atualize suas informações para receber recomendações personalizadas.</p>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="info-card">
              <h3>Fale com o suporte</h3>
              <p>Precisa de ajuda? Nossa equipe responde rapidamente dentro do painel.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
</body>
</html>
