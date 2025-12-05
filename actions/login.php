<?php
ini_set('display_errors', 1); // REMOVA EM PRODUÇÃO
error_reporting(E_ALL);

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/../inc/conf/db.php';
require_once __DIR__ . '/../inc/conf/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verifica CSRF
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        exit('Erro de segurança: CSRF');
    }

    $user_or_email = trim($_POST['user_or_email'] ?? '');
    $password      = $_POST['password'] ?? '';
    $lembrar       = isset($_POST['remember']);

    if ($user_or_email === '' || $password === '') {
        exit('Preencha todos os campos.');
    }

    // Adapte para sua tabela "usuarios"
    // Campos: id, nome, email, senha (hash)
    $stmt = $pdo->prepare('
        SELECT id, nome, email, senha
        FROM usuarios
        WHERE email = ? OR nome = ?
        LIMIT 1
    ');
    $stmt->execute([$user_or_email, $user_or_email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['senha'])) {
        // Login OK
        session_regenerate_id(true);
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_nome']  = $user['nome'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_plano'] = $user['plano'];

        // “Lembrar este dispositivo”
        if ($lembrar) {
            setcookie(session_name(), session_id(), time() + 60 * 60 * 24 * 30, "/");
        }

        header('Location: /modules/painel.php');
        exit;
    } else {
        echo 'Login ou senha inválido.';
        exit;
    }
}

// Se não for POST, volta para o login
header('Location: /index.php');
exit;
