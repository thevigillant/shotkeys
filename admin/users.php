<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../config.php';
require_login();

// Allow access if session says admin OR if it's the super admin email
$userEmail = $_SESSION['user_email'] ?? '';
$userRole = $_SESSION['user_role'] ?? '';

$isSuperAdmin = ($userEmail === 'brunosantanareisfc@gmail.com');
$isAdminInfo = ($userRole === 'admin');

if (!$isSuperAdmin && !$isAdminInfo) {
     die("Acesso Negado. Role: " . htmlspecialchars($userRole) . " | Email: " . htmlspecialchars($userEmail));
}

// AUTO-REPAIR: Ensure 'active' column exists silently
try {
    // Check if column exists
    $check = $pdo->query("SHOW COLUMNS FROM users LIKE 'active'");
    if ($check->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN active TINYINT(1) DEFAULT 1");
    }
    $check->closeCursor(); // CRITICAL FIX: Close cursor to allow next query
} catch (Exception $e) {
    // Ignore errors here
}

$message = '';
$error_msg = '';

try {
    // Handle Actions (Block/Unblock/Delete)
    if (isset($_GET['action'], $_GET['id'])) {
        $uid = (int)$_GET['id'];
        $act = $_GET['action'];
        
        if ($act === 'delete') {
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$uid]);
            $message = "Usuário excluído com sucesso.";
        } elseif ($act === 'block') {
            $pdo->prepare("UPDATE users SET active = 0 WHERE id = ?")->execute([$uid]);
            $message = "Usuário BLOQUEADO! Ele não poderá mais fazer login.";
        } elseif ($act === 'unblock') {
            $pdo->prepare("UPDATE users SET active = 1 WHERE id = ?")->execute([$uid]);
            $message = "Usuário DESBLOQUEADO com sucesso.";
        }
    }

    // Fetch Users with Order Stats
    $stmt = $pdo->query("
        SELECT u.*, 
        (SELECT COUNT(*) FROM orders WHERE user_id = u.id AND status = 'PAID') as total_orders 
        FROM users u 
        ORDER BY u.id DESC
    ");
    $users = $stmt->fetchAll();

} catch (PDOException $e) {
    $error_msg = "Erro no Banco de Dados: " . $e->getMessage();
    $users = []; // Avoid foreach error
}
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
    /* Admin UI v2 */
    :root { --neon-blue: #00f3ff; --neon-red: #ff0055; --neon-green: #00ff99; --dark-bg: #050510; --panel-bg: rgba(20, 20, 35, 0.95); --border-color: rgba(255, 255, 255, 0.1); }
    body { background-color: var(--dark-bg); color: #fff; font-family: 'Rajdhani', sans-serif; margin: 0; background-image: radial-gradient(circle at 50% 50%, rgba(0, 50, 100, 0.1) 0%, transparent 50%); }
    .admin-container { display: flex; min-height: 100vh; }
    .sidebar { width: 260px; background: #0a0a0f; border-right: 1px solid var(--border-color); padding: 2rem; display: flex; flex-direction: column; }
    .nav-item { display: block; padding: 1rem; color: #888; text-decoration: none; margin-bottom: 0.5rem; transition: 0.3s; border-radius: 8px; font-weight: 500; }
    .nav-item:hover, .nav-item.active { background: rgba(0, 243, 255, 0.1); color: var(--neon-blue); border-left: 3px solid var(--neon-blue); }
    .main-content { flex: 1; padding: 3rem; overflow-x: auto; }
    
    .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .page-title { font-family: 'Orbitron'; font-size: 2rem; text-shadow: 0 0 10px rgba(255,255,255,0.2); margin: 0; }
    
    table { width: 100%; border-collapse: separate; border-spacing: 0 8px; margin-top: 1rem; }
    th { text-align: left; padding: 1rem; color: var(--neon-blue); font-family: 'Orbitron'; font-size: 0.9rem; letter-spacing: 1px; text-transform: uppercase; }
    tr.user-row { background: var(--panel-bg); transition: transform 0.2s; }
    tr.user-row:hover { transform: scale(1.01); background: rgba(30, 30, 50, 1); box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
    td { padding: 1rem; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); vertical-align: middle; }
    tr.user-row td:first-child { border-left: 1px solid var(--border-color); border-radius: 8px 0 0 8px; }
    tr.user-row td:last-child { border-right: 1px solid var(--border-color); border-radius: 0 8px 8px 0; }
    
    .badge { padding: 4px 10px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
    .badge-admin { background: rgba(188, 19, 254, 0.2); color: #bc13fe; border: 1px solid #bc13fe; }
    .badge-user { background: rgba(0, 243, 255, 0.2); color: #00f3ff; border: 1px solid #00f3ff; }
    .badge-active { color: var(--neon-green); text-shadow: 0 0 5px rgba(0,255,153,0.5); }
    .badge-banned { color: var(--neon-red); text-shadow: 0 0 5px rgba(255,0,85,0.5); }
    
    .btn-action { padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.8rem; font-weight: bold; margin-right: 5px; cursor: pointer; border: none; display: inline-block; transition: all 0.2s; }
    .btn-block { background: rgba(255, 165, 0, 0.2); color: orange; border: 1px solid orange; }
    .btn-block:hover { background: orange; color: #000; }
    .btn-unblock { background: rgba(0, 255, 153, 0.2); color: #00ff99; border: 1px solid #00ff99; }
    .btn-unblock:hover { background: #00ff99; color: #000; }
    .btn-delete { background: rgba(255, 0, 85, 0.2); color: #ff0055; border: 1px solid #ff0055; opacity: 0.5; }
    .btn-delete:hover { opacity: 1; background: #ff0055; color: white; }
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
    
    <?php if ($message): ?>
        <div style="background: rgba(0, 255, 153, 0.2); border: 1px solid #00ff99; color: #00ff99; padding: 1rem; margin-bottom: 2rem; border-radius: 8px;">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div style="background: rgba(255, 0, 85, 0.2); border: 1px solid #ff0055; color: #ff0055; padding: 1rem; margin-bottom: 2rem; border-radius: 8px;">
            <?= htmlspecialchars($error_msg) ?>
        </div>
    <?php endif; ?>

    <div class="panel-header">
        <h1 class="page-title">SISTEMA DE GESTÃO DE USUÁRIOS</h1>
        <div style="color: #666;">Total: <?= count($users) ?></div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuário</th>
                <th>Status</th>
                <th>Pedidos</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr class="user-row">
                <td style="color: #666;">#<?= str_pad($u['id'], 4, '0', STR_PAD_LEFT) ?></td>
                <td>
                    <div style="font-weight: bold; font-size: 1.1rem; color: #fff;"><?= htmlspecialchars($u['name']) ?></div>
                    <div style="font-size: 0.85rem; color: var(--neon-blue);"><?= htmlspecialchars($u['email']) ?></div>
                    <div style="font-size: 0.75rem; color: #555; margin-top: 2px;">Entrou em: <?= date('d/m/Y', strtotime($u['created_at'])) ?></div>
                </td>
                <td>
                    <?php if ($u['role'] === 'admin'): ?>
                        <span class="badge badge-admin">ADMINISTRADOR</span>
                    <?php else: ?>
                        <span class="badge badge-user">CLIENTE</span>
                    <?php endif; ?>
                    
                    <div style="margin-top: 5px;">
                        <?php if ($u['active'] == 1): ?>
                            <span class="badge-active">● ATIVO</span>
                        <?php else: ?>
                            <span class="badge-banned">● BLOQUEADO</span>
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                    <div style="font-size: 1.2rem; font-weight: bold;"><?= $u['total_orders'] ?></div>
                    <small style="color: #666;">compras</small>
                </td>
                <td>
                    <?php if (($u['email'] ?? '') !== $userEmail): ?> 
                        <?php if (($u['active'] ?? 1) == 1): ?>
                            <a href="users.php?action=block&id=<?= $u['id'] ?>" class="btn-action btn-block" title="Bloquear Acesso">BLOQUEAR</a>
                        <?php else: ?>
                            <a href="users.php?action=unblock&id=<?= $u['id'] ?>" class="btn-action btn-unblock" title="Liberar Acesso">LIBERAR</a>
                        <?php endif; ?>
                        
                        <a href="users.php?action=delete&id=<?= $u['id'] ?>" class="btn-action btn-delete" onclick="return confirm('ATENÇÃO: Isso apagará permanentemente o usuário. Continuar?');">EXCLUIR</a>
                    <?php else: ?>
                        <span style="color: #666; font-size: 0.8rem;">(Você)</span>
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
