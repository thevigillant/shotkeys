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
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Cadastro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 520px;">
  <h1 class="h3 mb-4">Criar conta</h1>

  <?php if ($success): ?>
    <div class="alert alert-success">Cadastro realizado! Você já pode entrar.</div>
    <a href="login.php" class="btn btn-primary">Ir para Login</a>
  <?php else: ?>
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" class="card p-4 shadow-sm bg-white">
      <div class="mb-3">
        <label class="form-label">Nome</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">E-mail</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Senha</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirmar senha</label>
        <input type="password" name="confirm" class="form-control" required>
      </div>
      <button class="btn btn-primary w-100">Cadastrar</button>
      <div class="text-center mt-3">
        Já tem conta? <a href="login.php">Entre aqui</a>
      </div>
    </form>
  <?php endif; ?>
</div>
</body>
</html>