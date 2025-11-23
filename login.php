<?php
require __DIR__ . '/config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'E-mail inválido.';
  } else {
    try {
      $stmt = $pdo->prepare('SELECT id, name, password_hash FROM users WHERE email = ? LIMIT 1');
      $stmt->execute([$email]);
      $user = $stmt->fetch();

      if (!$user || !password_verify($password, $user['password_hash'])) {
        $errors[] = 'E-mail ou senha incorretos.';
      } else {
        // Iniciar a sessão, se ainda não estiver iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        // Redireciona
        $to = 'dashboard.php';
        if (!empty($_GET['redirect'])) {
          $to = filter_var($_GET['redirect'], FILTER_SANITIZE_URL);
        }
        header("Location: $to");
        exit;
      }
    } catch (PDOException $e) {
      error_log("Database error during login: " . $e->getMessage());
      $errors[] = 'Ocorreu um erro ao processar seu login. Tente novamente mais tarde.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Login - ShotKeys</title> <!-- Título atualizado -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts (Opcional, mas melhora a tipografia) -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Archivo+Black&display=swap" rel="stylesheet">
  <!-- Custom CSS -->
  <style>
    /* Variáveis de Cores (Consistente com a página de registro) */
    :root {
      --bg-dark-primary: #12002b; /* Fundo principal muito escuro */
      --bg-dark-secondary: #210041; /* Fundo para o container do formulário */
      --accent-purple: #c725d2; /* Cor de destaque (botão, links) - um magenta/roxo vibrante */
      --accent-purple-hover: #a21ea9; /* Cor de destaque hover */
      --input-bg-dark: #3a1a5b; /* Fundo dos inputs */
      --text-color-light: #f8f8f8; /* Texto claro */
      --text-color-muted: #b0a0c0; /* Texto mais suave, para descrições */
      --border-color-dark: #5a3a7b; /* Cor da borda dos inputs e containers */
      --alert-danger-bg: #4a0505; /* Fundo vermelho escuro para erros */
      --alert-success-bg: #0a4a15; /* Fundo verde escuro para sucesso */
    }

    body {
      font-family: 'Poppins', sans-serif;
      /* Gradiente sutil para o fundo, parecido com o da ShotKeys */
      background: linear-gradient(135deg, var(--bg-dark-primary) 0%, #0d001a 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      padding: 20px;
      color: var(--text-color-light); /* Cor padrão do texto no body */
    }

    .registration-container { /* Renomeei de .login-container para reutilizar o estilo */
      max-width: 480px;
      width: 100%;
      background-color: var(--bg-dark-secondary); /* Fundo escuro para o container */
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5); /* Sombra mais escura e pronunciada */
      padding: 40px;
      border: 1px solid var(--border-color-dark); /* Borda sutil para definir */
    }

    .brand-logo {
      text-align: center;
      margin-bottom: 30px;
    }
    .brand-logo h2 {
      font-family: 'Archivo Black', sans-serif; /* Fonte para o logo */
      font-weight: 700;
      color: var(--accent-purple); /* Cor de destaque para o logo */
      font-size: 2.2rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    h1.form-title {
      font-size: 2rem;
      font-weight: 600;
      color: var(--text-color-light);
      margin-bottom: 30px;
      text-align: center;
    }

    .form-label {
      font-weight: 500;
      color: var(--text-color-light);
      margin-bottom: 8px;
    }

    .form-control {
      height: 48px;
      border-radius: 8px;
      background-color: var(--input-bg-dark); /* Fundo escuro para input */
      color: var(--text-color-light); /* Cor do texto digitado */
      border: 1px solid var(--border-color-dark); /* Borda mais discreta */
      padding: 0.75rem 1rem;
      transition: all 0.2s ease-in-out;
    }

    .form-control:focus {
      border-color: var(--accent-purple); /* Borda de foco com a cor de destaque */
      box-shadow: 0 0 0 0.25rem rgba(199, 37, 210, 0.4); /* Sombra de foco com a cor de destaque e opacidade */
      background-color: var(--input-bg-dark); /* Manter o fundo escuro */
      color: var(--text-color-light);
    }

    /* Placeholder color para inputs escuros */
    .form-control::placeholder {
      color: var(--text-color-muted); /* Texto mais suave para o placeholder */
    }

    .btn-primary {
      background-color: var(--accent-purple);
      border-color: var(--accent-purple);
      font-weight: 600;
      height: 50px;
      border-radius: 8px;
      font-size: 1.1rem;
      color: white; /* Certificar que o texto do botão é branco */
      transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out, transform 0.1s ease-in-out;
    }

    .btn-primary:hover {
      background-color: var(--accent-purple-hover);
      border-color: var(--accent-purple-hover);
      transform: translateY(-2px); /* Efeito de elevação mais pronunciado */
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4); /* Sombra no hover */
    }

    /* Estilo para alerts em tema escuro */
    .alert {
      border-radius: 8px;
      font-size: 0.95rem;
      margin-bottom: 25px;
      color: var(--text-color-light); /* Texto claro para alerts */
      border: none; /* Remove borda padrão do bootstrap */
      padding: 1rem 1.25rem; /* Ajusta o padding */
    }

    .alert-danger {
      background-color: var(--alert-danger-bg);
      border: 1px solid rgba(255, 0, 0, 0.3); /* Borda sutil de erro */
    }

    .alert-success {
      background-color: var(--alert-success-bg);
      border: 1px solid rgba(0, 255, 0, 0.3); /* Borda sutil de sucesso */
    }

    .text-center a {
      color: var(--accent-purple); /* Links com a cor de destaque */
      font-weight: 500;
      text-decoration: none;
    }

    .text-center a:hover {
      text-decoration: underline;
      color: var(--accent-purple-hover);
    }

    /* Ajuste para alinhar verticalmente no centro caso o conteúdo seja menor que a viewport */
    html {
        height: 100%;
    }
  </style>
</head>
<body>
<div class="registration-container"> <!-- Usando a mesma classe para consistência de estilo -->
  <div class="brand-logo">
    <h2>SHOTKEYS</h2>
  </div>

  <h1 class="form-title">Entrar na sua conta</h1> <!-- Título atualizado -->

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <ul class="mb-0 ps-3">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label for="email" class="form-label">E-mail</label>
      <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
    </div>
    <div class="mb-4"> <!-- mb-4 para mais espaço antes do botão -->
      <label for="password" class="form-label">Senha</label>
      <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <div class="d-grid gap-2">
      <button type="submit" class="btn btn-primary">Entrar</button>
    </div>
    <div class="text-center mt-4">
      Não tem conta? <a href="register.php">Criar conta</a> <!-- Texto do link atualizado -->
    </div>
  </form>
</div>
</body>
</html>