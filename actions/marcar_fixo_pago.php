<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../inc/conf/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        header('Location: /flowdesk_novo/modules/painel.php?mod=financeiro&erro_fixo=1');
        exit;
    }

    // Marca como pago apenas no status do mês
    $stmt = $pdo->prepare("
        UPDATE financeiro_fixos
        SET status_mes = 'pago', atualizado_em = NOW()
        WHERE id = ? AND ativo = 1
    ");
    $stmt->execute([$id]);

    // Se quiser, também pode lançar saída no caixa aqui (opcional)
    // Mesma lógica de antes, adicionando registro em financeiro_saidas

    header('Location: /flowdesk_novo/modules/painel.php?mod=financeiro&ok_fixo_pago=1');
    exit;
}

header('Location: /flowdesk_novo/modules/painel.php?mod=financeiro');
exit;
