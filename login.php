<?php
ini_set('display_errors',1); // Para debug; remova em produção
error_reporting(E_ALL);

require_once __DIR__.'/includes/config.php';
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/csrf.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        exit('Erro de segurança: CSRF');
    }
    $user_or_email = $_POST['user_or_email'] ?? '';
    $password = $_POST['password'] ?? '';

$stmt = $pdo->prepare('SELECT id, nome, senha_hash FROM users WHERE email = ? OR username = ? LIMIT 1');
$stmt->execute([$user_or_email, $user_or_email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['senha_hash'])) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_nome'] = $user['nome']; // Adicione isto
    header('Location: modules/dashboard.php');
    exit;
}else {
        echo "Login ou senha inválido.";
    }
}
