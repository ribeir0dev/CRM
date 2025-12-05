<?php
if (session_status() !== PHP_SESSION_ACTIVE)
    session_start();
require_once __DIR__ . '/../../inc/conf/db.php';

// Mês selecionado no filtro (padrão = mês atual)
$mes_atual_param = $_GET['mes'] ?? date('Y-m'); // formato YYYY-MM
// garante formato válido
if (!preg_match('/^\d{4}-\d{2}$/', $mes_atual_param)) {
    $mes_atual_param = date('Y-m');
}

$ano = (int) substr($mes_atual_param, 0, 4);
$mes = (int) substr($mes_atual_param, 5, 2);

// label exibido no topo
$mes_label = date('m/Y', strtotime($mes_atual_param . '-01'));

// ENTRADAS: financeiro_entradas.valor_recebido
$stmt = $pdo->prepare("
  SELECT COALESCE(SUM(valor_recebido),0) AS total
  FROM financeiro_entradas
  WHERE YEAR(data_lancamento) = ?
    AND MONTH(data_lancamento) = ?
");
$stmt->execute([$ano, $mes]);
$totalEntradasMes = (float) $stmt->fetchColumn();

// SAÍDAS: financeiro_saidas.valor
$stmt = $pdo->prepare("
  SELECT COALESCE(SUM(valor),0) AS total
  FROM financeiro_saidas
  WHERE YEAR(data_lancamento) = ?
    AND MONTH(data_lancamento) = ?
");
$stmt->execute([$ano, $mes]);
$totalSaidasMes = (float) $stmt->fetchColumn();

// novos clientes no mês selecionado
$stmt = $pdo->prepare("
  SELECT COUNT(*)
  FROM clientes
  WHERE YEAR(criado_em) = ?
    AND MONTH(criado_em) = ?
");
$stmt->execute([$ano, $mes]);
$novosClientesMes = (int) $stmt->fetchColumn();

// Tasks ativas (por projeto) para o dashboard
$status_filtro = $_GET['status'] ?? 'todos';

$params = [];
$where = "t.coluna IN ('backlog','andamento','revisao')";

if ($status_filtro === 'pendente') {
    $where = "t.coluna = 'backlog'";
} elseif ($status_filtro === 'andamento') {
    $where = "t.coluna = 'andamento'";
} elseif ($status_filtro === 'concluida') {
    $where = "t.coluna = 'concluido'";
}

$sql = "
  SELECT t.id,
         t.titulo,
         t.coluna,
         t.criado_em,
         t.data_entrega,
         p.nome_projeto
  FROM projeto_tarefas t
  INNER JOIN projetos p ON p.id = t.projeto_id
  WHERE $where
  ORDER BY p.nome_projeto, t.id ASC
";

$stmt = $pdo->query($sql);
$tasksHoje = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Hospedagens ativas para o dashboard
$hoje = date('Y-m-d');

$stmt = $pdo->prepare("
  SELECT id, nome, tipo, data_inicio, data_fim
  FROM hospedagens
  WHERE data_fim >= ?
  ORDER BY data_fim ASC, nome ASC
");
$stmt->execute([$hoje]);
$hospAtivas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// mesmo mapa de ícones/cores do módulo de hospedagens
$mapIconesHosp = [
    'wordpress' => ['icon' => 'lni lni-wordpress', 'color' => '#81BEF0', 'label' => 'WordPress'],
    'vps' => ['icon' => 'lni lni-cloud-2', 'color' => '#F0AC81', 'label' => 'VPS'],
    'dominio' => ['icon' => 'lni lni-www', 'color' => '#C481F0', 'label' => 'Domínio'],
];
?>



<div class="dashboard-module">

    <!-- 1ª linha: frase + KPIs + filtro -->
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 gap-3">
        <div>
            <h5 class="mb-1">Vamos começar o dia</h5>
            <div class="d-flex flex-wrap gap-3 mt-2">
                <div class="small text-muted">
                    Tasks pendentes: <span class="fw-bold">12</span>
                </div>
                <div class="small text-muted">
                    Projetos ativos: <span class="fw-bold">5</span>
                </div>
                <div class="small text-muted">
                    Projetos finalizados no mês: <span class="fw-bold">3</span>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="small text-muted">
                <strong><?= htmlspecialchars($mes_label) ?></strong>
            </span>

            <form method="get" class="position-relative">
                <input type="hidden" name="mod" value="dashboard">
                <input type="month" id="filtroMes" name="mes" value="<?= htmlspecialchars($mes_atual_param) ?>"
                    style="position:absolute; opacity:0; pointer-events:none; width:0; height:0;">

                <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center"
                    onclick="document.getElementById('filtroMes').showPicker();">
                    <i class="lni lni-calendar-days fs-4"></i>
                </button>
            </form>
        </div>

        <script>
            document.getElementById('filtroMes').addEventListener('change', function () {
                this.form.submit();
            });
        </script>


    </div>

    <!-- 2ª linha: 3 cards de resumo -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box me-3">
                        <i class="lni lni-trend-up-1 text-white"></i>
                    </div>
                    <div>
                        <span class="fs-5 fw-bold">Entradas</span>
                        <h5 class="mt-2 mb-0 fs-6">R$<?= number_format($totalEntradasMes, 2, ',', '.') ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-danger text-white shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box me-3">
                        <i class="lni lni-trend-down-1 text-white"></i>
                    </div>
                    <div>
                        <span class="fs-5 fw-bold">Saídas</span>
                        <h5 class="mt-2 mb-0 fs-6">R$<?= number_format($totalSaidasMes, 2, ',', '.') ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box me-3">
                        <i class="lni lni-user-multiple-4 text-muted"></i>
                    </div>
                    <div>
                        <span class="small text-muted fw-bold fs-5">Novos Clientes</span>
                        <h5 class="mt-2 mb-0 fs-6"><?= $novosClientesMes ?> clientes</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- 3ª linha: tasks do dia + overview hospedagens -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <?php
                    $status_filtro = $_GET['status'] ?? 'todos';
                    ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="small text-muted fw-bold fs-5"> <i
                                    class="lni lni-agenda me-2 p-2 bg-light fs-5 text-dark"></i>Tasks de hoje</span>
                            <span class="small text-muted ms-2"><?= date('d/m/Y') ?></span>
                        </div>
                        <?php $status_filtro = $_GET['status'] ?? 'todos'; ?>
                    </div>
                    <?php $status_filtro = $_GET['status'] ?? 'todos'; ?>

                    <div class="filtros-tarefas mb-3">
                        <a href="?mod=dashboard&status=todos"
                            class="btn btn-status <?= $status_filtro === 'todos' ? 'btn-status-active' : '' ?>">Todos</a>

                        <a href="?mod=dashboard&status=pendente"
                            class="btn btn-status <?= $status_filtro === 'pendente' ? 'btn-status-active' : '' ?>">Pendente</a>

                        <a href="?mod=dashboard&status=andamento"
                            class="btn btn-status <?= $status_filtro === 'andamento' ? 'btn-status-active' : '' ?>">Em
                            andamento</a>

                        <a href="?mod=dashboard&status=concluida"
                            class="btn btn-status <?= $status_filtro === 'concluida' ? 'btn-status-active' : '' ?>">Concluída</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Tarefa</th>
                                    <th>Projeto</th>
                                    <th>Status</th>
                                    <th>Prazo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($tasksHoje)): ?>
                                    <tr>
                                        <td colspan="4" class="text-muted small text-center">
                                            Nenhuma task ativa para os projetos.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php
                                    $mapCol = [
                                        'backlog' => ['label' => 'Pendente', 'class' => 'bg-warning text-dark'],
                                        'andamento' => ['label' => 'Em andamento', 'class' => 'bg-info text-dark'],
                                        'revisao' => ['label' => 'Em revisão', 'class' => 'bg-secondary'],
                                        'concluido' => ['label' => 'Concluída', 'class' => 'bg-success'],
                                    ];
                                    ?>
                                    <?php foreach ($tasksHoje as $t): ?>
                                        <?php $info = $mapCol[$t['coluna']] ?? $mapCol['backlog']; ?>
                                        <tr>
                                            <td><?= htmlspecialchars($t['titulo']) ?></td>
                                            <td><?= htmlspecialchars($t['nome_projeto']) ?></td>
                                            <td>
                                                <span class="badge <?= $info['class'] ?>"><?= $info['label'] ?></span>
                                            </td>
                                            <td>
                                                <?php if (!empty($t['data_entrega'])): ?>
                                                    <?= date('d/m/Y', strtotime($t['data_entrega'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Sem prazo</span>
                                                <?php endif; ?>
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

        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted fs-5 fw-bold">
                            <i class="lni lni-cloud-2 me-2 p-2 bg-light fs-5 text-dark"></i>Hospedagens Ativas
                        </span>
                    </div>

                    <?php if (empty($hospAtivas)): ?>
                        <p class="small text-muted mb-0">Nenhuma hospedagem ativa no momento.</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush small">
                            <?php foreach ($hospAtivas as $h): ?>
                                <?php
                                $info = $mapIconesHosp[$h['tipo']] ?? $mapIconesHosp['dominio'];

                                $dtHoje = new DateTime($hoje);
                                $dtFim = new DateTime($h['data_fim']);
                                $diff = $dtHoje->diff($dtFim, true);
                                $dias = $diff->days; // inteiro de dias de hoje até data_fim
                        
                                $textoDias = $dias === 0
                                    ? 'expira hoje'
                                    : ($dias === 1 ? '1 dia restante' : $dias . ' dias restantes');
                                ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle me-2"
                                            style="width:28px;height:28px;
                            background: <?= htmlspecialchars($info['color']) ?>20;">
                                            <i class="<?= htmlspecialchars($info['icon']) ?>"
                                                style="color: <?= htmlspecialchars($info['color']) ?>; font-size:14px;"></i>
                                        </div>
                                        <span><?= htmlspecialchars($h['nome']) ?></span>
                                    </div>
                                    <span class="text-muted"><?= $textoDias ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                </div>
            </div>
        </div>

    </div>

</div>