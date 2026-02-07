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
  <title>Criar conta - ShotKeys</title>
  
  <!-- Base URL -->
  <!-- Base URL -->
  <base href="https://shotkeys.store" />

  <!-- FavIcon -->
  <link rel="icon" href="assets/icons/favicon/logo-Shot-Keys.ico" type="image/x-icon" />
  
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
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

  <style>
    /* Estilos centralizados */
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #1a0033 0%, #0d001a 100%);
    }
    
    .login-wrapper {
      width: 100%;
      max-width: 450px;
      padding: 2rem;
    }
    
    .login-card {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 16px;
      padding: 2.5rem;
      box-shadow: 0 0 30px rgba(0,0,0,0.5);
    }

    .form-control {
      background: rgba(0, 0, 0, 0.3) !important;
      border: 1px solid rgba(255, 255, 255, 0.1);
      color: #fff !important;
    }
    
    .form-control:focus {
      background: rgba(0, 0, 0, 0.5) !important;
      border-color: var(--color-accent);
      box-shadow: 0 0 0 0.25rem rgba(230, 0, 230, 0.25);
    }
    
    .form-label {
      color: rgba(255, 255, 255, 0.9);
      margin-top: 10px;
    }

    .brand-title {
      color: var(--color-accent);
      text-shadow: 0 0 10px rgba(230, 0, 230, 0.3);
    }
  </style>
</head>
<body>

<div class="login-wrapper">
  <div class="login-card">
    <div class="text-center mb-4">
      <h2 class="archivofont brand-title mb-0">SHOTKEYS</h2>
      <p class="text-white opacity-75">Junte-se à elite</p>
    </div>

    <?php if ($success): ?>
      <div class="alert alert-success border-0 bg-success bg-opacity-25 text-white text-center">
        Cadastro realizado com sucesso!<br>
        Você já pode entrar.
      </div>
      <div class="d-grid gap-2">
        <a href="login.php" class="btn btn-custom btn-lg w-100">IR PARA LOGIN</a>
      </div>
    <?php else: ?>
      <?php if ($errors): ?>
        <div class="alert alert-danger border-0 bg-danger bg-opacity-25 text-white">
          <ul class="mb-0 ps-3">
            <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post">
        <div class="mb-3">
          <label for="name" class="form-label">Nome</label>
          <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required placeholder="Seu Nome">
        </div>
        
        <div class="mb-3">
          <label for="email" class="form-label">E-mail</label>
          <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required placeholder="seu@email.com">
        </div>
        
        <div class="mb-3">
          <label for="password" class="form-label">Senha</label>
          <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
        </div>
        
        <div class="mb-4">
          <label for="confirm" class="form-label">Confirmar senha</label>
          <input type="password" name="confirm" id="confirm" class="form-control" required placeholder="••••••••">
        </div>
        
        <div class="d-grid gap-2 mb-3">
          <button type="submit" class="btn btn-custom btn-lg w-100">CADASTRAR</button>
        </div>
        
        <div class="text-center">
          <span class="text-white opacity-75">Já tem conta?</span> 
          <a href="login.php" class="text-decoration-none fw-bold" style="color: var(--color-accent);">Entre aqui</a>
        </div>
      </form>
    <?php endif; ?>
  </div>
</div>

</body>
</html>