<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../inc/conf/db.php';

$stmtCli = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome ASC");
$listaClientes = $stmtCli->fetchAll(PDO::FETCH_ASSOC);


function moneyToFloat($v)
{
    $v = trim((string)$v);
    if ($v === '') return 0.0;
    $v = str_replace(['.', ','], ['', '.'], $v);
    return (float)$v;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data       = $_POST['data_lancamento'] ?? date('Y-m-d');
    $descricao  = trim($_POST['descricao'] ?? '');
    $servico    = $_POST['servico'] ?? 'outro';
    $tipo       = $_POST['tipo_pagamento'] ?? 'integral';
    $forma      = $_POST['forma_pagamento'] ?? 'pix';
    $valor_rec  = moneyToFloat($_POST['valor_a_receber'] ?? '0');
    $valor_recib= moneyToFloat($_POST['valor_recebido'] ?? '0');
    $obs        = trim($_POST['observacoes'] ?? '');
    $concluido  = ($tipo === 'integral' && $valor_recib >= $valor_rec) ? 1 : 0;

    if ($descricao === '' || $valor_rec <= 0) {
        header('Location: /modules/painel.php?mod=financeiro&erro=1');
        exit;
    }

        $stmt = $pdo->prepare('
        INSERT INTO financeiro_entradas
        (cliente_id, data_lancamento, descricao, servico, tipo_pagamento, forma_pagamento,
         valor_a_receber, valor_recebido, concluido, observacoes, criado_em)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ');
    $stmt->execute([
        $cliente_id,
        $data,
        $descricao,
        $servico,
        $tipo,
        $forma,
        $valor_rec,
        $valor_recib,
        $concluido,
        $obs
    ]);


    header('Location: /modules/painel.php?mod=financeiro&ok=1');
    exit;
}

header('Location: /modules/painel.php?mod=financeiro');
exit;
