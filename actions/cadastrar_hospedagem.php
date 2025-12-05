<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../inc/conf/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /modules/painel.php?mod=hospedagens');
    exit;
}

// define ação: criar (padrão) ou excluir
$acao = $_POST['acao'] ?? 'criar';

if ($acao === 'excluir') {
    $id = (int)($_POST['hospedagem_id'] ?? 0);

    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM hospedagens WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: /modules/painel.php?mod=hospedagens&excluida=1');
        exit;
    }

    header('Location: /modules/painel.php?mod=hospedagens&erro=1');
    exit;
}

// AÇÃO PADRÃO: CRIAR
$nome        = trim($_POST['nome'] ?? '');
$tipo        = $_POST['tipo'] ?? 'dominio';
$data_inicio = $_POST['data_inicio'] ?? null;
$data_fim    = $_POST['data_fim'] ?? null;

if ($nome === '' || !$data_inicio || !$data_fim) {
    header('Location: /modules/painel.php?mod=hospedagens&erro=1');
    exit;
}

$stmt = $pdo->prepare("
  INSERT INTO hospedagens (nome, tipo, data_inicio, data_fim, criado_em)
  VALUES (?, ?, ?, ?, NOW())
");
$stmt->execute([$nome, $tipo, $data_inicio, $data_fim]);

header('Location: /modules/painel.php?mod=hospedagens&ok=1');
exit;
