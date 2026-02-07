<?php
require __DIR__ . '/../config.php';
require_login();

// Simple Admin Check
if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
     header("Location: ../dashboard.php");
     exit;
}

// Handle Actions (Block/Unblock)
if (isset($_GET['action'], $_GET['id'])) {
    $uid = (int)$_GET['id'];
    $act = $_GET['action'];
    
    // Check if column 'active' exists, if not assume we handle via logic or add column
    // For MVP let's assume all users are active by default. We might need to add 'active' column.
    // Let's add 'active' column dynamically if missing in setup_admin.php really, but here we can try:
    
    // We will use 'role' as a toggle for now if active column doesn't exist? 
    // Or let's CREATE properly. I'll stick to a simple role switch or check if I can disable them.
    // Let's assume we want to just DELETE/BAN.
    
    if ($act === 'delete') {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$uid]);
    }
}

// Fetch Users
$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Gerenciar Usuários | ShotKeys Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="https://shotkeys.store/admin/" />
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Rajdhani:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    /* Reusing Admin Styles */
    :root { --neon-blue: #00f3ff; --neon-red: #ff0055; --dark-bg: #050510; --panel-bg: rgba(20, 20, 35, 0.9); --border-color: rgba(255, 255, 255, 0.1); }
    body { background-color: var(--dark-bg); color: #fff; font-family: 'Rajdhani', sans-serif; margin: 0; background-image: radial-gradient(circle at 50% 50%, rgba(0, 50, 100, 0.1) 0%, transparent 50%); }
    .admin-container { display: flex; min-height: 100vh; }
    .sidebar { width: 260px; background: #0a0a0f; border-right: 1px solid var(--border-color); padding: 2rem; display: flex; flex-direction: column; }
    .nav-item { display: block; padding: 1rem; color: #888; text-decoration: none; margin-bottom: 0.5rem; transition: 0.3s; border-radius: 8px; }
    .nav-item:hover, .nav-item.active { background: rgba(0, 243, 255, 0.1); color: var(--neon-blue); border-left: 3px solid var(--neon-blue); }
    .main-content { flex: 1; padding: 3rem; overflow-x: auto; }
    
    table { width: 100%; border-collapse: collapse; margin-top: 2rem; background: var(--panel-bg); border-radius: 12px; overflow: hidden; }
    th { text-align: left; padding: 1.5rem; background: rgba(0,0,0,0.5); color: var(--neon-blue); font-family: 'Orbitron'; letter-spacing: 1px; }
    td { padding: 1.5rem; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover { background: rgba(255,255,255,0.02); }
    
    .badge { padding: 4px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; }
    .badge-admin { background: rgba(188, 19, 254, 0.2); color: #bc13fe; border: 1px solid #bc13fe; }
    .badge-user { background: rgba(0, 243, 255, 0.2); color: #00f3ff; border: 1px solid #00f3ff; }
    
    .btn-action { padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.9rem; font-weight: bold; margin-right: 5px; cursor: pointer; border: none; }
    .btn-delete { background: rgba(255, 0, 85, 0.2); color: #ff0055; border: 1px solid #ff0055; }
    .btn-delete:hover { background: #ff0055; color: white; }
  </style>
</head>
<body>

<div class="admin-container">
  <aside class="sidebar">
    <div style="font-family: 'Orbitron'; font-size: 1.5rem; color: var(--neon-blue); margin-bottom: 2rem;">SK // ADMIN</div>
    <nav>
      <a href="dashboard.php" class="nav-item">Dashboard</a>
      <a href="users.php" class="nav-item active">Gerenciar Usuários</a>
      <a href="settings.php" class="nav-item">Config Emails</a>
      <a href="../index.php" class="nav-item">Voltar Loja</a>
    </nav>
  </aside>

  <main class="main-content">
    <h1 style="font-family:'Orbitron'; font-size:2rem;">LISTA DE CLIENTES</h1>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Cargo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td>#<?= $u['id'] ?></td>
                <td>
                    <div style="font-weight: bold; font-size: 1.1rem;"><?= htmlspecialchars($u['name']) ?></div>
                    <div style="font-size: 0.8rem; color: #666;">Registrado em: <?= $u['created_at'] ?></div>
                </td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                    <span class="badge <?= $u['role'] === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                        <?= $u['role'] ?>
                    </span>
                </td>
                <td>
                    <?php if ($u['email'] !== $_SESSION['user_email']): // Don't delete self ?> 
                    <a href="users.php?action=delete&id=<?= $u['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Tem certeza que deseja remover este usuário?');">Excluir</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
  </main>
</div>

</body>
</html>
