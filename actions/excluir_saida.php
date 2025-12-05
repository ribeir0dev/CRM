<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../inc/conf/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM financeiro_saidas WHERE id = ?");
        $stmt->execute([$id]);
    }
    header('Location: /flowdesk_novo/modules/painel.php?mod=financeiro');
    exit;
}

header('Location: /flowdesk_novo/modules/painel.php?mod=financeiro');
exit;
