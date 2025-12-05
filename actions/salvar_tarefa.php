<?php
if (session_status() !== PHP_SESSION_ACTIVE)
  session_start();
require_once __DIR__ . '/../inc/conf/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /modules/painel.php?mod=projetos');
  exit;
}

$projeto_id = (int) ($_POST['projeto_id'] ?? 0);
$tarefa_id = (int) ($_POST['tarefa_id'] ?? 0);
$titulo = trim($_POST['titulo'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$coluna = $_POST['coluna'] ?? 'backlog';
$data_entrega = $_POST['data_entrega'] ?: null;

if ($projeto_id <= 0 || $titulo === '') {
  header('Location: /modules/painel.php?mod=projeto_detalhe&id=' . $projeto_id . '&erro_tarefa=1');
  exit;
}

if ($tarefa_id > 0) {
  $stmt = $pdo->prepare("
UPDATE projeto_tarefas
SET titulo = ?, descricao = ?, coluna = ?, data_entrega = ?, atualizado_em = NOW()
WHERE id = ? AND projeto_id = ?
    ");
  $stmt->execute([$titulo, $descricao, $coluna, $data_entrega, $tarefa_id, $projeto_id]);
} else {
  $stmt = $pdo->prepare("
      INSERT INTO projeto_tarefas (projeto_id, titulo, descricao, coluna, ordem, data_entrega, criado_em)
VALUES (?, ?, ?, ?, 0, ?, NOW())
    ");
  $stmt->execute([$projeto_id, $titulo, $descricao, $coluna, $data_entrega]);
}

header('Location: /modules/painel.php?mod=projeto_detalhe&id=' . $projeto_id);
exit;
