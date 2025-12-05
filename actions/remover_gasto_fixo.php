<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../inc/conf/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        // pode ser DELETE ou apenas marcar ativo=0
        $stmt = $pdo->prepare("UPDATE financeiro_fixos SET ativo = 0, atualizado_em = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }

    header('Location: /modules/painel.php?mod=financeiro&ok_fixo_removido=1');
    exit;
}

header('Location: /modules/painel.php?mod=financeiro');
exit;
