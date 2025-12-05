<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../inc/conf/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /modules/painel.php?mod=projetos');
    exit;
}

$nome_projeto = trim($_POST['nome_projeto'] ?? '');
$tipo_projeto = $_POST['tipo_projeto'] ?? 'outro';
$cliente_id   = isset($_POST['cliente_id']) && $_POST['cliente_id'] !== ''
    ? (int)$_POST['cliente_id']
    : null;
$data_inicio  = $_POST['data_inicio']   ?: null;
$data_entrega = $_POST['data_entrega']  ?: null;
$status       = $_POST['status']        ?? 'planejado';
$descricao    = trim($_POST['descricao'] ?? '');

if ($nome_projeto === '') {
    header('Location: /modules/painel.php?mod=projetos&erro=1');
    exit;
}

$stmt = $pdo->prepare('
    INSERT INTO projetos
    (cliente_id, nome_projeto, tipo_projeto, descricao,
     data_inicio, data_entrega, status, criado_em)
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
');
$stmt->execute([
    $cliente_id,
    $nome_projeto,
    $tipo_projeto,
    $descricao,
    $data_inicio,
    $data_entrega,
    $status
]);

header('Location: /modules/painel.php?mod=projetos&ok=1');
exit;
