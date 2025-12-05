<?php
// actions/concluir_projeto.php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../inc/conf/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /modules/painel.php?mod=projetos');
    exit;
}

$projeto_id = (int)($_POST['projeto_id'] ?? 0);

if ($projeto_id <= 0) {
    header('Location: /modules/painel.php?mod=projetos&erro=1');
    exit;
}

// exclui o projeto da tabela
$stmt = $pdo->prepare('DELETE FROM projetos WHERE id = ?');
$stmt->execute([$projeto_id]);

header('Location: /modules/painel.php?mod=projetos&concluido=1');
exit;
