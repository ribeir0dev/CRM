<?php
// Exemplo de valores estáticos; depois você puxa do banco conforme o mês selecionado
$mes_atual = $_GET['mes'] ?? date('Y-m');
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
        <?php
        $mes_atual = $_GET['mes'] ?? date('Y-m');
        $mes_label = date('m/Y', strtotime($mes_atual . '-01'));
        ?>
        <div class="d-flex align-items-center gap-2">

            <span class="small text-muted">
                <strong><?= htmlspecialchars($mes_label) ?></strong>
            </span>

            <form method="get" class="position-relative">
                <!-- input totalmente invisível, mas ainda no DOM -->
                <input type="month" id="filtroMes" name="mes" value="<?= htmlspecialchars($mes_atual) ?>"
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
                        <h5 class="mt-2 mb-0 fs-6">R$ 12.340,00</h5>
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
                        <h5 class="mt-2 mb-0 fs-6">R$ 5.890,00</h5>
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
                        <h5 class="mt-2 mb-0 fs-6">18 clientes</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- 3ª linha: 2 cards com gráficos (placeholders) -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted fw-bold fs-5"> <i
                                class="lni lni-trend-up-1 p-2 bg-light fs-5 text-dark me-2"></i>Gráfico de entrada
                            total</span>
                    </div>
                    <div class="grafico-placeholder text-muted d-flex align-items-center justify-content-center">
                        Área para gráfico de entrada (linha/coluna)
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted fw-bold fs-5"> <i
                                class="lni lni-trend-down-1 p-2 bg-light fs-5  text-dark me-2"></i>Gráfico de saída
                            total</span>
                    </div>
                    <div class="grafico-placeholder text-muted d-flex align-items-center justify-content-center">
                        Área para gráfico de saída (linha/coluna)
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4ª linha: tasks do dia + overview hospedagens -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <?php
                    $status_filtro = $_GET['status'] ?? 'todos';
                    ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="small text-muted fw-bold fs-5"> <i class="lni lni-agenda me-2 p-2 bg-light fs-5 text-dark"></i>Tasks de hoje</span>
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
                                <tr>
                                    <td>Reunião com cliente X</td>
                                    <td>Projeto Site X</td>
                                    <td><span class="badge bg-warning text-dark">Pendente</span></td>
                                    <td>Hoje 15:00</td>
                                </tr>
                                <tr>
                                    <td>Ajustar layout página inicial</td>
                                    <td>Landing Y</td>
                                    <td><span class="badge bg-info text-dark">Em andamento</span></td>
                                    <td>Hoje 18:00</td>
                                </tr>
                                <tr>
                                    <td>Enviar proposta Z</td>
                                    <td>Campanha Z</td>
                                    <td><span class="badge bg-success">Concluída</span></td>
                                    <td>Hoje 10:30</td>
                                </tr>
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
                        <span class="small text-muted fs-5 fw-bold"> <i class="lni lni-cloud-2 me-2 p-2 bg-light fs-5 text-dark"></i>Hospedagens Ativas</span>
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            site1.com.br
                            <span class="text-muted">faltam 15 dias</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            loja2.com
                            <span class="text-muted">faltam 45 dias</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            app3.io
                            <span class="text-muted">faltam 3 dias</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>