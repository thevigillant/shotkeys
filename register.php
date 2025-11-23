<?php
require __DIR__ . '/config.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirm = $_POST['confirm'] ?? '';

  if ($name === '') {
    $errors[] = 'Informe seu nome.';
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'E-mail inválido.';
  }
  if (strlen($password) < 6) {
    $errors[] = 'A senha deve ter pelo menos 6 caracteres.';
  }
  if ($password !== $confirm) {
    $errors[] = 'As senhas não conferem.';
  }

  if (!$errors) {
    try {
      // Verificar se e-mail já existe
      $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
      $stmt->execute([$email]);
      if ($stmt->fetch()) {
        $errors[] = 'Este e-mail já está cadastrado.';
      } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $hash]);
        $success = true;
      }
    } catch (PDOException $e) {
      // Log the error for debugging (e.g., to a file)
      error_log("Database error during registration: " . $e->getMessage());
      $errors[] = 'Ocorreu um erro ao processar seu cadastro. Tente novamente mais tarde.';
      // Optionally, show a more generic error to the user
    }
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Criar conta - Minha Aplicação</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts (Opcional, mas melhora a tipografia) -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Custom CSS -->
  <style>
    /* Variáveis de Cores (Exemplo) */
    :root {
      --primary-color: #007bff; /* Azul padrão do Bootstrap */
      --primary-dark: #0056b3;
      --secondary-bg: #f0f2f5; /* Um cinza claro para o fundo */
      --text-color-dark: #333;
      --text-color-light: #6c757d;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--secondary-bg);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh; /* Ocupa a altura total da viewport */
      margin: 0;
      padding: 20px; /* Padding para telas pequenas */
    }

    .registration-container {
      max-width: 480px; /* Um pouco mais estreito, pode ser 420-520 */
      width: 100%;
      background-color: #fff;
      border-radius: 12px; /* Cantos mais arredondados */
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); /* Sombra mais suave e pronunciada */
      padding: 40px; /* Padding interno maior */
    }

    .brand-logo {
      text-align: center;
      margin-bottom: 30px;
    }
    .brand-logo h2 {
      font-weight: 700;
      color: var(--primary-color);
      font-size: 2rem;
    }
    /* Ou uma imagem de logo */
    /* .brand-logo img {
      max-width: 120px;
      height: auto;
    } */

    h1.form-title {
      font-size: 1.8rem;
      font-weight: 600;
      color: var(--text-color-dark);
      margin-bottom: 30px;
      text-align: center;
    }

    .form-label {
      font-weight: 500;
      color: var(--text-color-dark);
      margin-bottom: 8px; /* Espaçamento entre label e input */
    }

    .form-control {
      height: 48px; /* Altura um pouco maior para inputs */
      border-radius: 8px; /* Cantos arredondados para inputs */
      border-color: #e0e0e0;
      padding: 0.75rem 1rem;
      transition: all 0.2s ease-in-out;
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25); /* Sombra de foco padrão, ou customizada */
    }

    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      font-weight: 600;
      height: 50px; /* Altura do botão */
      border-radius: 8px;
      font-size: 1.1rem;
      transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out, transform 0.1s ease-in-out;
    }

    .btn-primary:hover {
      background-color: var(--primary-dark);
      border-color: var(--primary-dark);
      transform: translateY(-1px); /* Pequeno efeito de elevação no hover */
    }

    .alert {
      border-radius: 8px;
      font-size: 0.95rem;
      margin-bottom: 25px; /* Mais espaço abaixo do alerta */
    }

    .text-center a {
      color: var(--primary-color);
      font-weight: 500;
      text-decoration: none;
    }

    .text-center a:hover {
      text-decoration: underline;
    }

    /* Ajuste para alinhar verticalmente no centro caso o conteúdo seja menor que a viewport */
    html {
        height: 100%;
    }
  </style>
</head>
<body>
<div class="registration-container">
  <div class="brand-logo">
    <!-- Você pode colocar uma imagem aqui, ou um título como este -->
    <h2>Minha Aplicação</h2>
  </div>

  <h1 class="form-title">Criar sua conta</h1>

  <?php if ($success): ?>
    <div class="alert alert-success text-center">
      Cadastro realizado com sucesso! Você já pode entrar.
    </div>
    <div class="d-grid gap-2">
      <a href="login.php" class="btn btn-primary">Ir para Login</a>
    </div>
  <?php else: ?>
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul class="mb-0 ps-3"> <!-- ps-3 para padding-left -->
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label for="name" class="form-label">Nome</label>
        <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">E-mail</label>
        <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Senha</label>
        <input type="password" name="password" id="password" class="form-control" required>
      </div>
      <div class="mb-4"> <!-- mb-4 para mais espaço antes do botão -->
        <label for="confirm" class="form-label">Confirmar senha</label>
        <input type="password" name="confirm" id="confirm" class="form-control" required>
      </div>
      <div class="d-grid gap-2"> <!-- d-grid gap-2 para botão full width e espaçamento -->
        <button type="submit" class="btn btn-primary">Cadastrar</button>
      </div>
      <div class="text-center mt-4"> <!-- mt-4 para mais espaço após o botão -->
        Já tem conta? <a href="login.php">Entre aqui</a>
      </div>
    </form>
  <?php endif; ?>
</div>
</body>
</html>