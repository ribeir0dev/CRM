<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../inc/conf/db.php';
require_once __DIR__ . '/../inc/functions/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = trim($_POST['nome'] ?? '');
    $whatsapp  = trim($_POST['whatsapp'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $status    = $_POST['status'] ?? 'ativo';
    $genero    = $_POST['genero'] ?? 'empresa';
    $obs       = trim($_POST['observacoes'] ?? '');

    if ($nome === '' || $whatsapp === '' || $email === '') {
        header('Location: /flowdesk_novo/modules/painel.php?mod=clientes&erro=1');
        exit;
    }

    // garante função (caso auth.php não carregue por algum motivo)
    if (!function_exists('gerarTokenPublico')) {
        function gerarTokenPublico($length = 64) {
            return bin2hex(random_bytes($length / 2));
        }
    }

    $token_publico = gerarTokenPublico(64);

    $stmt = $pdo->prepare('
        INSERT INTO clientes (nome, whatsapp, email, status, observacoes, genero, token_publico, criado_em)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ');
    $stmt->execute([$nome, $whatsapp, $email, $status, $obs, $genero, $token_publico]);

    header('Location: /flowdesk_novo/modules/painel.php?mod=clientes&ok=1');
    exit;
}

header('Location: /flowdesk_novo/modules/painel.php?mod=clientes');
exit;
