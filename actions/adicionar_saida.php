<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../inc/conf/db.php';

function moneyToFloat($v)
{
    $v = trim((string)$v);
    if ($v === '') return 0.0;
    $v = str_replace(['.', ','], ['', '.'], $v);
    return (float)$v;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data      = $_POST['data_lancamento'] ?? date('Y-m-d');
    $descricao = trim($_POST['descricao'] ?? '');
    $tipo      = $_POST['tipo'] ?? 'outro';
    $valor     = moneyToFloat($_POST['valor'] ?? '0');
    $obs       = trim($_POST['observacoes'] ?? '');

    if ($descricao === '' || $valor <= 0) {
        header('Location: /modules/painel.php?mod=financeiro&erro_saida=1');
        exit;
    }

    $stmt = $pdo->prepare('
        INSERT INTO financeiro_saidas
        (data_lancamento, tipo, descricao, valor, observacoes, criado_em)
        VALUES (?, ?, ?, ?, ?, NOW())
    ');
    $stmt->execute([$data, $tipo, $descricao, $valor, $obs]);

    header('Location: /modules/painel.php?mod=financeiro&ok_saida=1');
    exit;
}

header('Location: /modules/painel.php?mod=financeiro');
exit;
