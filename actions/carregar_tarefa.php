<?php
if (session_status() !== PHP_SESSION_ACTIVE)
    session_start();
require_once __DIR__ . '/../inc/conf/db.php';

header('Content-Type: application/json; charset=utf-8');

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(null);
    exit;
}

$stmt = $pdo->prepare("SELECT id, projeto_id, titulo, descricao, coluna, data_entrega
FROM projeto_tarefas
WHERE id = ?");
$stmt->execute([$id]);
$t = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($t ?: null);
