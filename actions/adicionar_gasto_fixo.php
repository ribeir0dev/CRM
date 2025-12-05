<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../inc/conf/db.php';

function moneyToFloat($v) {
    $v = trim((string)$v);
    if ($v === '') return 0.0;
    $v = str_replace(['.', ','], ['', '.'], $v);
    return (float)$v;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_gasto   = trim($_POST['tipo_gasto'] ?? '');
    $valor        = moneyToFloat($_POST['valor'] ?? '0');
    $data_inicio  = $_POST['data_inicio'] ?? date('Y-m-d');
    $eh_parcelado = isset($_POST['eh_parcelado']) ? 1 : 0;
    $parcelas_tot = $eh_parcelado ? (int)($_POST['parcelas_totais'] ?? 0) : null;
    $parcelas_res = $eh_parcelado ? (int)($_POST['parcelas_restantes'] ?? 0) : null;
    $obs          = trim($_POST['observacoes'] ?? '');

    if ($tipo_gasto === '' || $valor <= 0) {
        header('Location: /modules/painel.php?mod=financeiro&erro_fixo=1');
        exit;
    }

    $stmt = $pdo->prepare('
        INSERT INTO financeiro_fixos
        (tipo_gasto, valor, eh_parcelado, parcelas_totais, parcelas_restantes,
         data_inicio, ativo, observacoes, criado_em)
        VALUES (?, ?, ?, ?, ?, ?, 1, ?, NOW())
    ');
    $stmt->execute([
        $tipo_gasto,
        $valor,
        $eh_parcelado,
        $parcelas_tot,
        $parcelas_res,
        $data_inicio,
        $obs
    ]);

    header('Location: /modules/painel.php?mod=financeiro&ok_fixo=1');
    exit;
}

header('Location: /modules/painel.php?mod=financeiro');
exit;
