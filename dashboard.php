<?php
require __DIR__ . '/config.php';
require_login();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Área Logada</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="card p-4 shadow-sm">
    <h1 class="h4 mb-3">Olá, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuário') ?>!</h1>
    <p>Você está logado. Coloque aqui o conteúdo exclusivo da área logada.</p>
    <a href="logout.php" class="btn btn-outline-danger">Sair</a>
  </div>
</div>
</body>
</html>