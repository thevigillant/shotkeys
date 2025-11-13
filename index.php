<?php
declare(strict_types=1);
session_start();

// Opcional: inclui configurações gerais (DB, constantes, etc.)
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

// Nome do app para reutilizar em title/OG
$APP_NAME = defined('APP_NAME') ? constant('APP_NAME') : 'ShotKeys';

// Estado de login (ajuste a chave de sessão conforme seu login.php)
$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_id'] !== '';
$userName   = $_SESSION['user_name'] ?? $_SESSION['email'] ?? 'Usuário';
$firstName  = trim(explode(' ', $userName)[0] ?? '');

// Gera/obtém token CSRF simples para ações sensíveis (ex.: logout)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

// URL canônica básica (ajuste se necessário)
$host      = $_SERVER['HTTP_HOST'] ?? 'shotkeys.store';
$scheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'https';
$canonical = $scheme . '://' . $host . '/';

// Flash message opcional
$flash = $_SESSION['flash'] ?? null;
if ($flash) {
    unset($_SESSION['flash']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title><?php echo htmlspecialchars($APP_NAME); ?> — Produtividade em um clique</title>
  <meta name="description" content="Acelere seu fluxo com o ShotKeys. Crie atalhos inteligentes, automatize tarefas e otimize sua produtividade." />
  <link rel="canonical" href="<?php echo htmlspecialchars($canonical); ?>" />

  <!-- Open Graph -->
  <meta property="og:title" content="<?php echo htmlspecialchars($APP_NAME); ?> — Produtividade em um clique" />
  <meta property="og:description" content="Crie atalhos inteligentes e automatize tarefas. Chegue mais rápido ao que importa." />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="<?php echo htmlspecialchars($canonical); ?>" />
  <meta property="og:image" content="<?php echo htmlspecialchars($canonical); ?>assets/og-image.jpg" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?php echo htmlspecialchars($APP_NAME); ?> — Produtividade em um clique" />
  <meta name="twitter:description" content="Crie atalhos inteligentes e automatize tarefas. Chegue mais rápido ao que importa." />
  <meta name="twitter:image" content="<?php echo htmlspecialchars($canonical); ?>assets/og-image.jpg" />

  <!-- Base URL -->
  <base href="https://shotkeys.store/" />

  <!-- Favicon (ajuste os caminhos conforme seus assets) -->
  <link rel="icon" href="assets/favicon.ico" sizes="any" />
  <link rel="icon" href="assets/icon.svg" type="image/svg+xml" />
  <link rel="apple-touch-icon" href="assets/apple-touch-icon.png" />
  <link rel="manifest" href="site.webmanifest" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet" />

  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous"
  />

  <!-- Estilos mínimos da landing -->
  <style>
    :root {
      --brand: #7c3aed; /* roxo */
      --brand-dark: #5b21b6;
      --text: #0f172a; /* slate-900 */
      --muted: #475569; /* slate-600 */
      --bg: #ffffff;
    }
    body {
      font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", sans-serif;
      color: var(--text);
      background: var(--bg);
    }
    .navbar-brand {
      font-family: "Space Grotesk", Inter, system-ui, sans-serif;
      font-weight: 700;
      letter-spacing: -0.02em;
    }
    .btn-brand {
      background: var(--brand);
      color: #fff;
      border: none;
    }
    .btn-brand:hover {
      background: var(--brand-dark);
      color: #fff;
    }
    .hero {
      padding: 80px 0 40px;
    }
    .hero h1 {
      font-family: "Space Grotesk", Inter, system-ui, sans-serif;
      font-weight: 700;
      letter-spacing: -0.02em;
    }
    .hero p.lead {
      color: var(--muted);
    }
    .feature-icon {
      width: 40px; height: 40px;
      display: inline-flex; align-items: center; justify-content: center;
      border-radius: 10px; background: #f3e8ff; color: var(--brand);
    }
    .footer {
      border-top: 1px solid #e5e7eb;
      padding: 32px 0;
      color: #64748b;
      margin-top: 48px;
    }
    .badge-soft {
      background: #f1f5f9;
      color: #0f172a;
      border: 1px solid #e2e8f0;
    }
  </style>

  <!-- JSON-LD simples (ajuste se quiser detalhar) -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "<?php echo htmlspecialchars($APP_NAME); ?>",
    "url": "<?php echo htmlspecialchars($canonical); ?>",
    "potentialAction": {
      "@type": "SearchAction",
      "target": "<?php echo htmlspecialchars($canonical); ?>?q={search_term_string}",
      "query-input": "required name=search_term_string"
    }
  }
  </script>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="/">
        <span class="fs-4">🧩</span>
        <span><?php echo htmlspecialchars($APP_NAME); ?></span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Alternar navegação">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navMain">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="#features">Recursos</a></li>
          <li class="nav-item"><a class="nav-link" href="#plans">Planos</a></li>
          <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
          <li class="nav-item"><a class="nav-link" href="#contato">Contato</a></li>
        </ul>

        <?php if (!$isLoggedIn): ?>
          <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="login.php">Entrar</a>
            <a class="btn btn-brand" href="register.php">Criar conta</a>
          </div>
        <?php else: ?>
          <div class="dropdown">
            <button class="btn btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <span class="me-2">👤</span>
              <span><?php echo htmlspecialchars($firstName ?: 'Conta'); ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
              <li><a class="dropdown-item" href="account.php">Minha conta</a></li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form action="logout.php" method="post" class="px-3 py-1">
                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>" />
                  <button type="submit" class="btn btn-link p-0 text-danger">Sair</button>
                </form>
              </li>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <!-- FLASH MESSAGE -->
  <?php if ($flash && is_array($flash)): ?>
    <div class="container mt-3">
      <div class="alert alert-<?php echo htmlspecialchars($flash['type'] ?? 'info'); ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($flash['message'] ?? ''); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
      </div>
    </div>
  <?php endif; ?>

  <!-- HERO -->
  <header class="hero">
    <div class="container">
      <?php if ($isLoggedIn): ?>
        <div class="mb-3">
          <span class="badge badge-soft rounded-pill text-uppercase">Logado</span>
        </div>
        <h1 class="display-5 mb-3">
          Olá, <?php echo htmlspecialchars($firstName ?: ''); ?>! Pronto para acelerar com o <?php echo htmlspecialchars($APP_NAME); ?>?
        </h1>
        <p class="lead mb-4">
          Acesse sua dashboard para criar atalhos, organizar coleções e acompanhar suas métricas.
        </p>
        <div class="d-flex gap-2">
          <a class="btn btn-brand btn-lg" href="dashboard.php">Ir para a Dashboard</a>
          <a class="btn btn-outline-secondary btn-lg" href="#features">Ver recursos</a>
        </div>
      <?php else: ?>
        <div class="mb-3">
          <span class="badge badge-soft rounded-pill text-uppercase">Novo</span>
        </div>
        <h1 class="display-5 mb-3">
          Atalhos inteligentes. Fluxo mais rápido. Resultados maiores.
        </h1>
        <p class="lead mb-4">
          O <?php echo htmlspecialchars($APP_NAME); ?> ajuda você a criar e gerenciar “ShotKeys” para tarefas repetitivas, economizando tempo todos os dias.
        </p>
        <div class="d-flex gap-2">
          <a class="btn btn-brand btn-lg" href="register.php">Começar agora</a>
          <a class="btn btn-outline-secondary btn-lg" href="login.php">Já tenho conta</a>
        </div>
      <?php endif; ?>
    </div>
  </header>

  <!-- FEATURES -->
  <section id="features" class="py-5">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-4">
          <div class="p-4 border rounded-3 h-100">
            <div class="feature-icon mb-3">⚡</div>
            <h3>Crie atalhos</h3>
            <p class="text-secondary">Defina ShotKeys para ações repetidas e ganhe velocidade com um clique.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-4 border rounded-3 h-100">
            <div class="feature-icon mb-3">📦</div>
            <h3>Organize coleções</h3>
            <p class="text-secondary">Agrupe atalhos por projeto, equipe ou cliente, do seu jeito.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-4 border rounded-3 h-100">
            <div class="feature-icon mb-3">📈</div>
            <h3>Métricas de uso</h3>
            <p class="text-secondary">Acompanhe os cliques e o tempo economizado, com relatórios claros.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- PLANS -->
  <section id="plans" class="py-5 bg-light">
    <div class="container">
      <div class="text-center mb-4">
        <h2>Planos para cada fase</h2>
        <p class="text-secondary">Comece grátis. Evolua quando fizer sentido.</p>
      </div>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="p-4 border rounded-3 h-100">
            <h3 class="mb-1">Gratuito</h3>
            <p class="text-secondary">Ideal para começar</p>
            <ul class="mb-4">
              <li>Até 10 ShotKeys</li>
              <li>1 coleção</li>
              <li>Métricas básicas</li>
            </ul>
            <a class="btn btn-outline-secondary w-100" href="register.php">Criar conta</a>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-4 border rounded-3 h-100">
            <h3 class="mb-1">Pro</h3>
            <p class="text-secondary">Para quem quer escalar</p>
            <ul class="mb-4">
              <li>ShotKeys ilimitados</li>
              <li>Coleções ilimitadas</li>
              <li>Relatórios avançados</li>
            </ul>
            <a class="btn btn-brand w-100" href="register.php">Experimentar</a>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-4 border rounded-3 h-100">
            <h3 class="mb-1">Equipe</h3>
            <p class="text-secondary">Colaboração com controle</p>
            <ul class="mb-4">
              <li>Times e permissões</li>
              <li>SSO (SAML/Google)</li>
              <li>Suporte prioritário</li>
            </ul>
            <a class="btn btn-outline-secondary w-100" href="#contato">Falar com vendas</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section id="faq" class="py-5">
    <div class="container">
      <h2 class="mb-4">Perguntas frequentes</h2>
      <div class="accordion" id="faqAcc">
        <div class="accordion-item">
          <h2 class="accordion-header" id="q1">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#c1" aria-expanded="true" aria-controls="c1">
              Como o ShotKeys economiza meu tempo?
            </button>
          </h2>
          <div id="c1" class="accordion-collapse collapse show" aria-labelledby="q1" data-bs-parent="#faqAcc">
            <div class="accordion-body">
              Você transforma passos repetitivos em atalhos executáveis. Menos cliques, mais foco no que importa.
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="q2">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c2" aria-expanded="false" aria-controls="c2">
              O plano gratuito é realmente gratuito?
            </button>
          </h2>
          <div id="c2" class="accordion-collapse collapse" aria-labelledby="q2" data-bs-parent="#faqAcc">
            <div class="accordion-body">
              Sim. Sem cartão para começar. Quando precisar de mais, você migra no seu ritmo.
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="q3">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c3" aria-expanded="false" aria-controls="c3">
              Posso usar no time?
            </button>
          </h2>
          <div id="c3" class="accordion-collapse collapse" aria-labelledby="q3" data-bs-parent="#faqAcc">
            <div class="accordion-body">
              Sim, com o plano Equipe você terá convites, permissões e auditoria de ações.
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CONTATO -->
  <section id="contato" class="py-5 bg-light">
    <div class="container">
      <h2 class="mb-3">Fale com a gente</h2>
      <p class="text-secondary mb-4">Dúvidas, ideias ou parcerias? Envie uma mensagem.</p>
      <form action="contact.php" method="post" class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="name">Nome</label>
          <input class="form-control" type="text" id="name" name="name" required />
        </div>
        <div class="col-md-6">
          <label class="form-label" for="email">Email</label>
          <input class="form-control" type="email" id="email" name="email" required />
        </div>
        <div class="col-12">
          <label class="form-label" for="message">Mensagem</label>
          <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
        </div>
        <div class="col-12">
          <button class="btn btn-brand" type="submit">Enviar</button>
        </div>
      </form>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
      <div>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($APP_NAME); ?> — Todos os direitos reservados.</div>
      <ul class="list-inline m-0">
        <li class="list-inline-item"><a class="text-secondary text-decoration-none" href="terms.php">Termos</a></li>
        <li class="list-inline-item"><a class="text-secondary text-decoration-none" href="privacy.php">Privacidade</a></li>
        <li class="list-inline-item"><a class="text-secondary text-decoration-none" href="cookies.php">Cookies</a></li>
      </ul>
    </div>
  </footer>

  <!-- Bootstrap Bundle -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"
  ></script>
</body>
</html>