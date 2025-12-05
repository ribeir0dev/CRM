<?php
// modules/content/financeiro.php
if (session_status() !== PHP_SESSION_ACTIVE)
    session_start();
require_once __DIR__ . '/../../inc/conf/db.php';

$mes_atual = $_GET['mes'] ?? date('Y-m');     // formato YYYY-MM
$mes_label = date('m/Y', strtotime($mes_atual . '-01'));

// Limites de data do mês
$inicio_mes = $mes_atual . '-01';
$fim_mes = date('Y-m-t', strtotime($inicio_mes)); // último dia do mês

/* =======================
   1. TOTAIS DO MÊS
   ====================== */

// Total de entradas (valor_recebido dentro do mês)
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(valor_recebido), 0) AS total
    FROM financeiro_entradas
    WHERE data_lancamento BETWEEN ? AND ?
");
$stmt->execute([$inicio_mes, $fim_mes]);
$total_entradas_mes = (float) $stmt->fetchColumn();

// Total de saídas
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(valor), 0) AS total
    FROM financeiro_saidas
    WHERE data_lancamento BETWEEN ? AND ?
");
$stmt->execute([$inicio_mes, $fim_mes]);
$total_saidas_mes = (float) $stmt->fetchColumn();

// Caixa total (todas as entradas - todas as saídas)
$stmt = $pdo->query("SELECT COALESCE(SUM(valor_recebido),0) FROM financeiro_entradas");
$caixa_total_entradas = (float) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COALESCE(SUM(valor),0) FROM financeiro_saidas");
$caixa_total_saidas = (float) $stmt->fetchColumn();

$caixa_total = $caixa_total_entradas - $caixa_total_saidas;
$caixa_mes = $total_entradas_mes - $total_saidas_mes;

/* =======================
   2. LISTAS DO MÊS
   ====================== */

// Entradas do mês
$stmt = $pdo->prepare("
    SELECT e.*, c.nome AS cliente_nome
    FROM financeiro_entradas e
    LEFT JOIN clientes c ON c.id = e.cliente_id
    WHERE e.data_lancamento BETWEEN ? AND ?
    ORDER BY e.data_lancamento DESC, e.id DESC
");
$stmt->execute([$inicio_mes, $fim_mes]);
$entradas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Saídas do mês
$stmt = $pdo->prepare("
    SELECT *
    FROM financeiro_saidas
    WHERE data_lancamento BETWEEN ? AND ?
    ORDER BY data_lancamento DESC, id DESC
");
$stmt->execute([$inicio_mes, $fim_mes]);
$saidas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// IDs de gastos fixos pagos no mês selecionado (via saidas com fixo_id)
$stmt = $pdo->prepare("
    SELECT DISTINCT fixo_id
    FROM financeiro_saidas
    WHERE fixo_id IS NOT NULL
      AND data_lancamento BETWEEN ? AND ?
");
$stmt->execute([$inicio_mes, $fim_mes]);
$fixosPagosMes = $stmt->fetchAll(PDO::FETCH_COLUMN); // array de IDs

// Gastos fixos ativos (para tabela)
$stmt = $pdo->prepare("
    SELECT *
    FROM financeiro_fixos
    WHERE ativo = 1
      AND data_inicio <= ?
    ORDER BY tipo_gasto ASC
");
$stmt->execute([$fim_mes]);
$fixos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total de gastos fixos do mês (só os que ainda NÃO foram pagos no mês)
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(f.valor),0) AS total
    FROM financeiro_fixos f
    WHERE f.ativo = 1
      AND f.data_inicio <= ?
      AND (f.eh_parcelado = 0 OR (f.eh_parcelado = 1 AND f.parcelas_totais >= 1))
      AND f.id NOT IN (
          SELECT DISTINCT fixo_id
          FROM financeiro_saidas
          WHERE fixo_id IS NOT NULL
            AND data_lancamento BETWEEN ? AND ?
      )
");
$stmt->execute([$fim_mes, $inicio_mes, $fim_mes]);
$total_fixos_mes = (float) $stmt->fetchColumn();
?>


<!-- 1ª LINHA: Resumo financeiro + filtro de mês -->
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 gap-3">
    <div>
        <h5 class="mb-1">Financeiro</h5>
        <div class="small text-muted">Visão geral de entradas, saídas e gastos fixos do mês.</div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <form method="get" class="position-relative">
            <input type="hidden" name="mod" value="financeiro">
            <input type="month" id="filtroMesFinanceiro" name="mes" value="<?= htmlspecialchars($mes_atual) ?>"
                style="position:absolute; opacity:0; pointer-events:none; width:0; height:0;">
            <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center"
                onclick="document.getElementById('filtroMesFinanceiro').showPicker();">
                <i class="lni lni-calendar-2 me-1"></i> Escolher mês
            </button>
        </form>
        <span class="small text-muted">
            Mês selecionado: <strong><?= htmlspecialchars($mes_label) ?></strong>
        </span>
    </div>
</div>

<script>
    document.getElementById('filtroMesFinanceiro').addEventListener('change', function () {
        this.form.submit();
    });
</script>

<div class="row g-3 mb-4">
    <!-- Entradas -->
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3">
                    <i class="lni  lni-exit-up text-dark"></i>
                </div>
                <div>
                    <span class="small text-muted">Entradas (<?= htmlspecialchars($mes_label) ?>)</span>
                    <h5 class="mt-2 mb-0 text-success">
                        R$ <?= number_format($total_entradas_mes, 2, ',', '.') ?>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Saídas -->
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                        transform="rotate(0 0 0)">
                        <path
                            d="M11.75 12.75C11.9729 12.75 12.1731 12.6528 12.3104 12.4984L16.2802 8.53122C16.5731 8.23842 16.5733 7.76354 16.2805 7.47056C15.9877 7.17757 15.5128 7.17742 15.2198 7.47022L12.5 10.1883L12.5 4.25C12.5 3.83579 12.1642 3.5 11.75 3.5C11.3358 3.5 11 3.83579 11 4.25L11 10.1882L8.28014 7.4702C7.98715 7.17741 7.51227 7.17758 7.21948 7.47057C6.92669 7.76357 6.92686 8.23844 7.21986 8.53123L11.1876 12.4962C11.325 12.6518 11.526 12.75 11.75 12.75Z"
                            fill="#343C54" />
                        <path opacity="0.4"
                            d="M21.75 11.5C21.75 10.2574 20.7426 9.25 19.5 9.25H17.6218C17.541 9.37082 17.4472 9.48555 17.3405 9.59221L16.182 10.75L19.5 10.75C19.9142 10.75 20.25 11.0858 20.25 11.5V17.5C20.25 17.9142 19.9142 18.25 19.5 18.25L4 18.25C3.58579 18.25 3.25 17.9142 3.25 17.5L3.25 11.5C3.25 11.0858 3.58579 10.75 4 10.75L7.31811 10.75L6.15957 9.59227C6.05281 9.48558 5.95901 9.37084 5.87817 9.25L4 9.25C2.75736 9.25 1.75 10.2574 1.75 11.5V17.5C1.75 18.7426 2.75736 19.75 4 19.75H19.5C20.7426 19.75 21.75 18.7426 21.75 17.5V11.5Z"
                            fill="#343C54" />
                    </svg>

                </div>
                <div>
                    <span class="small text-muted">Saídas (<?= htmlspecialchars($mes_label) ?>)</span>
                    <h5 class="mt-2 mb-0 text-danger">
                        R$ <?= number_format($total_saidas_mes, 2, ',', '.') ?>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Caixa + Gastos Fixos -->
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3">
                    <i class="lni  lni-google-pay text-dark"></i>
                </div>
                <div>
                    <span class="small text-muted">Caixa total (todas as entradas - saídas)</span>
                    <h5 class="mt-2 mb-1 <?= $caixa_total >= 0 ? 'text-success' : 'text-danger' ?>">
                        R$ <?= number_format($caixa_total, 2, ',', '.') ?>
                    </h5>
                </div>
            </div>
        </div>
    </div>
    <!-- Caixa + Gastos Fixos -->
    <?php
    $icone_verde = $caixa_mes >= $total_fixos_mes;
    $corIcone = $icone_verde ? '#16a34a' : '#dc2626'; // verde / vermelho
    ?>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3">
                    <svg width="24" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M11.3156 2.35225C11.9098 2.0805 12.5928 2.0805 13.1871 2.35225L18.9111 4.96991C19.7118 5.33608 20.2253 6.13562 20.2253 7.01607L20.2254 12.4811C20.2254 14.0375 19.7481 15.5838 18.7447 16.8253C17.9444 17.8155 16.8583 19.0727 15.7472 20.089C15.1923 20.5966 14.6131 21.0609 14.0452 21.4022C13.4919 21.7349 12.8712 21.999 12.2514 21.999C11.6317 21.999 11.011 21.7349 10.4576 21.4022C9.88978 21.0608 9.31059 20.5966 8.75563 20.0889C7.64456 19.0726 6.55842 17.8154 5.75814 16.8252C4.75474 15.5837 4.27744 14.0376 4.27742 12.4813L4.27734 7.01612C4.27733 6.13565 4.79089 5.33607 5.5916 4.9699L11.3156 2.35225ZM13.0019 6.24903C13.0019 5.83481 12.6662 5.49903 12.2519 5.49903C11.8377 5.49903 11.5019 5.83481 11.5019 6.24903V6.71902C10.4999 6.94639 9.75194 7.84248 9.75194 8.91327V9.17834C9.75194 10.1162 10.3337 10.9557 11.2119 11.2851L12.7653 11.8677C13.058 11.9774 13.2519 12.2573 13.2519 12.5699V12.835C13.2519 13.2492 12.9162 13.585 12.5019 13.585H11.8145C11.5038 13.585 11.2519 13.3331 11.2519 13.0224C11.2519 12.6082 10.9162 12.2724 10.5019 12.2724C10.0877 12.2724 9.75194 12.6082 9.75194 13.0224C9.75194 14.0552 10.5111 14.9108 11.5019 15.0614V15.499C11.5019 15.9132 11.8377 16.249 12.2519 16.249C12.6662 16.249 13.0019 15.9132 13.0019 15.499V15.0292C14.0039 14.8018 14.7519 13.9058 14.7519 12.835V12.5699C14.7519 11.632 14.1702 10.7925 13.292 10.4632L11.7386 9.88059C11.4459 9.77081 11.2519 9.49097 11.2519 9.17834V8.91327C11.2519 8.49906 11.5877 8.16327 12.0019 8.16327H12.6893C13.0001 8.16327 13.2519 8.41515 13.2519 8.72587C13.2519 9.14008 13.5877 9.47587 14.0019 9.47587C14.4162 9.47587 14.7519 9.14008 14.7519 8.72587C14.7519 7.693 13.9927 6.83744 13.0019 6.6868V6.24903Z"
                            fill="<?= $corIcone ?>" />
                    </svg>
                </div>
                <div>
                    <span class="small text-muted">Custo de Vida</span>
                    <h5 class="fw-semibold mb-0">
                        R$ <?= number_format($total_fixos_mes, 2, ',', '.') ?>
                    </h5>
                </div>
            </div>
        </div>
    </div>


</div>

<!-- 2ª LINHA: Entradas e Saídas -->

<div class="row g-3 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0">
            <i class="lni lni-stats-up me-2"></i>Movimentações financeiras
        </h6>
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                aria-expanded="false">
                Adicionar dado
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <button type="button" class="dropdown-item" data-bs-toggle="modal"
                        data-bs-target="#modalNovaEntrada">
                        <i class="lni lni-enter-down me-1"></i>Receita (Entrada)
                    </button>
                </li>
                <li>
                    <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalNovaSaida">
                        <i class="lni lni-exit-up me-1"></i>Despesa (Saída)
                    </button>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalGastoFixo">
                        <i class="lni lni-dollar-circle me-1"></i>Gasto fixo
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Card Entradas -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box me-2">
                            <i class="lni lni-enter text-muted"></i>
                        </div>
                        <h6 class="mb-0">Entradas</h6>
                    </div>
                </div>


                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0 table-financeiro">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Cliente</th>
                                <th>Descrição</th>
                                <th>Serviço</th>
                                <th>Tipo pgto.</th>
                                <th>Forma</th>
                                <th class="text-end">A receber</th>
                                <th class="text-end">Recebido</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($entradas)): ?>
                                <tr>
                                    <td colspan="9" class="text-muted small">Nenhuma entrada neste mês.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($entradas as $e): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($e['data_lancamento'])) ?></td>
                                        <td>
                                            <?= $e['cliente_nome']
                                                ? htmlspecialchars($e['cliente_nome'])
                                                : '<span class="text-muted small">Sem cliente</span>' ?>
                                        </td>
                                        <td><?= htmlspecialchars($e['descricao']) ?></td>
                                        <td><?= ucfirst(str_replace('_', ' ', $e['servico'])) ?></td>
                                        <td><?= $e['tipo_pagamento'] === '50_50' ? '50/50' : 'Integral' ?></td>
                                        <td><?= strtoupper($e['forma_pagamento']) ?></td>
                                        <td class="text-end">R$ <?= number_format($e['valor_a_receber'], 2, ',', '.') ?></td>
                                        <td class="text-end">R$ <?= number_format($e['valor_recebido'], 2, ',', '.') ?></td>
                                        <td class="text-end">
                                            <?php if ($e['tipo_pagamento'] === '50_50' && !$e['concluido']): ?>
                                                <button class="btn btn-success btn-sm me-1">
                                                    <i class="lni lni-checkmark"></i>
                                                </button>
                                            <?php endif; ?>
                                            <form method="post" action="/flowdesk_novo/actions/excluir_entrada.php"
                                                class="d-inline"
                                                onsubmit="return confirm('Deseja realmente excluir esta entrada?');">
                                                <input type="hidden" name="id" value="<?= (int) $e['id'] ?>">
                                                <button class="btn btn-outline-danger btn-sm">
                                                    <i class="lni lni-trash-3"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>


                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Saídas -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box me-2">
                            <i class="lni lni-exit text-muted"></i>
                        </div>
                        <h6 class="mb-0">Saídas</h6>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0 table-financeiro">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Tipo</th>
                                <th>Descrição</th>
                                <th class="text-end">Valor</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($saidas)): ?>
                                <tr>
                                    <td colspan="5" class="text-muted small">Nenhuma saída neste mês.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($saidas as $s): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($s['data_lancamento'])) ?></td>
                                        <td><?= ucfirst($s['tipo']) ?></td>
                                        <td><?= htmlspecialchars($s['descricao']) ?></td>
                                        <td class="text-end">R$ <?= number_format($s['valor'], 2, ',', '.') ?></td>
                                        <td class="text-end">
                                            <form method="post" action="/flowdesk_novo/actions/excluir_saida.php"
                                                class="d-inline"
                                                onsubmit="return confirm('Deseja realmente excluir esta saída?');">
                                                <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                                                <button class="btn btn-outline-danger btn-sm">
                                                    <i class="lni lni-trash-3"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3ª LINHA: Gastos Fixos -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="lni lni-coin me-2"></i>Gastos fixos
            </h6>
        </div>

        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tipo de gasto</th>
                        <th class="text-end">Valor</th>
                        <th>Parcelas</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fixos as $f): ?>
                        <?php
                        $totais = (int) $f['parcelas_totais'];
                        $ehParcelado = (int) $f['eh_parcelado'] === 1;

                        if ($ehParcelado && $totais > 0) {
                            $inicio = new DateTime($f['data_inicio']);
                            $ref = new DateTime($inicio_mes); // mês filtrado
                            $diffMeses = ($ref->format('Y') - $inicio->format('Y')) * 12
                                + ($ref->format('m') - $inicio->format('m'))
                                + 1; // primeira parcela = 1
                            $parcelaAtual = max(1, min($totais, $diffMeses));
                            $textoParcelas = "{$parcelaAtual}/{$totais}";
                        } else {
                            $parcelaAtual = null;
                            $textoParcelas = '—';
                        }

                        $foiPagoNesteMes = in_array($f['id'], $fixosPagosMes, true);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($f['tipo_gasto']) ?></td>
                            <td class="text-end">R$ <?= number_format($f['valor'], 2, ',', '.') ?></td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark px-2"><?= $textoParcelas ?></span>
                            </td>
                            <td>
                                <?php if ($foiPagoNesteMes): ?>
                                    <span class="badge bg-success-subtle text-success">Pago</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning">A ser pago</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if (!$foiPagoNesteMes): ?>
                                    <form method="post" action="/flowdesk_novo/actions/pagar_gasto_fixo.php" class="d-inline"
                                        onsubmit="return confirm('Confirmar pagamento deste gasto fixo neste mês?');">
                                        <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
                                        <button class="btn btn-success btn-sm">
                                            <i class="lni lni-checkmark-circle"></i> Pago
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <form method="post" action="/flowdesk_novo/actions/remover_gasto_fixo.php" class="d-inline"
                                    onsubmit="return confirm('Remover este gasto fixo permanentemente?');">
                                    <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
                                    <button class="btn btn-outline-danger btn-sm">
                                        <i class="lni lni-trash-3"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>

            </table>
        </div>


    </div>
</div>


<?php
include __DIR__ . '/../modals/modal_entrada.php';
include __DIR__ . '/../modals/modal_saida.php';
include __DIR__ . '/../modals/modal_gasto_fixo.php';
