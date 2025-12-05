<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../inc/conf/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = isset($_POST['cliente_id']) ? (int)$_POST['cliente_id'] : 0;

    if ($cliente_id <= 0 || empty($_FILES['foto']['name'])) {
        header('Location: /modules/painel.php?mod=cliente&id='.$cliente_id);
        exit;
    }

    // Validação básica
    $ext_permitidas = ['jpg','jpeg','png'];
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $ext_permitidas) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        header('Location: /modules/painel.php?mod=cliente&id='.$cliente_id.'&foto=erro');
        exit;
    }

    $nome_arquivo = 'cliente_'.$cliente_id.'_'.time().'.'.$ext;
    $destino = __DIR__ . '/../uploads/clientes/' . $nome_arquivo;

    if (!is_dir(__DIR__ . '/../uploads/clientes')) {
        mkdir(__DIR__ . '/../uploads/clientes', 0775, true);
    }

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
        $caminho_db = '/uploads/clientes/' . $nome_arquivo;
        $stmt = $pdo->prepare("UPDATE clientes SET foto_perfil = ? WHERE id = ?");
        $stmt->execute([$caminho_db, $cliente_id]);
    }

    header('Location: /modules/painel.php?mod=cliente&id='.$cliente_id);
    exit;
}

header('Location: /modules/painel.php?mod=clientes');
exit;
