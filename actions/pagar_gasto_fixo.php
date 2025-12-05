<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../inc/conf/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        header('Location: /modules/painel.php?mod=financeiro&erro_fixo=1');
        exit;
    }

    // Busca o gasto fixo ativo
    $stmt = $pdo->prepare("SELECT * FROM financeiro_fixos WHERE id = ? AND ativo = 1");
    $stmt->execute([$id]);
    $f = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$f) {
        header('Location: /modules/painel.php?mod=financeiro&erro_fixo=1');
        exit;
    }

    $valor = (float)$f['valor'];
    $hoje  = date('Y-m-d');

    // 1) Registra saída vinculada a este gasto fixo (fixo_id)
    $stmt = $pdo->prepare('
        INSERT INTO financeiro_saidas
        (fixo_id, data_lancamento, tipo, descricao, valor, observacoes, criado_em)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ');
    $descricao_saida = 'Gasto fixo: ' . $f['tipo_gasto'];
    $obs_saida       = 'Pagamento de gasto fixo ID ' . $f['id'];
    $stmt->execute([
        $f['id'],
        $hoje,
        'pagamentos',
        $descricao_saida,
        $valor,
        $obs_saida
    ]);

    // 2) Opcional: controlar parcelas (se quiser que um parcelado termine um dia)
    if ((int)$f['eh_parcelado'] === 1) {
        $restantes = (int)$f['parcelas_restantes'];
        if ($restantes > 0) {
            $novo_restantes = $restantes - 1;
            $ativo = $novo_restantes > 0 ? 1 : 0;
            $stmt = $pdo->prepare("
                UPDATE financeiro_fixos
                SET parcelas_restantes = ?, ativo = ?, atualizado_em = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$novo_restantes, $ativo, $id]);
        }
    }

    header('Location: /modules/painel.php?mod=financeiro&ok_fixo_pago=1');
    exit;
}

header('Location: /modules/painel.php?mod=financeiro');
exit;
