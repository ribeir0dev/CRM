<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../inc/conf/db.php';

header('Content-Type: application/json; charset=utf-8');

$cliente_id = (int)($_GET['cliente_id'] ?? 0);
$slug       = $_GET['slug'] ?? '';

if ($cliente_id <= 0 || $slug === '') {
    echo json_encode(null);
    exit;
}

$stmt = $pdo->prepare("
  SELECT titulo, conteudo, compartilhado
  FROM cliente_blocos
  WHERE cliente_id = ? AND slug = ?
  LIMIT 1
");
$stmt->execute([$cliente_id, $slug]);
$bloco = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($bloco ?: null);
