<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../inc/conf/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nome      = trim($_POST['nome'] ?? '');
    $whatsapp  = trim($_POST['whatsapp'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $status    = $_POST['status'] ?? 'ativo';
    $genero    = $_POST['genero'] ?? 'empresa';
    $obs       = trim($_POST['observacoes'] ?? '');

    if ($id <= 0 || $nome === '' || $whatsapp === '' || $email === '') {
        header('Location: /flowdesk_novo/modules/painel.php?mod=cliente&id='.$id.'&erro=1');
        exit;
    }

    $stmt = $pdo->prepare('
        UPDATE clientes
        SET nome = ?, whatsapp = ?, email = ?, status = ?, genero = ?, observacoes = ?, atualizado_em = NOW()
        WHERE id = ?
    ');
    $stmt->execute([$nome, $whatsapp, $email, $status, $genero, $obs, $id]);

    header('Location: /flowdesk_novo/modules/painel.php?mod=cliente&id='.$id.'&ok=1');
    exit;
}

header('Location: /flowdesk_novo/modules/painel.php?mod=clientes');
exit;
