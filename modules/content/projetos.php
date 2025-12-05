<?php
// modules/content/projetos.php
if (session_status() !== PHP_SESSION_ACTIVE)
    session_start();
require_once __DIR__ . '/../../inc/conf/db.php';

// Busca projetos + nome do cliente
$stmt = $pdo->query("
    SELECT p.id,
           p.nome_projeto,
           p.tipo_projeto,
           p.data_inicio,
           p.data_entrega,
           p.status,
           c.nome AS cliente_nome
    FROM projetos p
    LEFT JOIN clientes c ON c.id = p.cliente_id
    ORDER BY p.data_inicio DESC, p.id DESC
");
$projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Rótulos + ícones + cores por tipo
$mapTipos = [
    'landing_page' => ['label' => 'Landing Page', 'icon' => 'lni-layout-9', 'color' => '#81BEF0'],
    'configuracao' => ['label' => 'Configuração', 'icon' => 'lni-gear-1', 'color' => '#F0AC81'],
    'alteracao' => ['label' => 'Alteração', 'icon' => 'lni-pencil-1', 'color' => '#81F09F'],
    'otimizacao' => ['label' => 'Otimização', 'icon' => 'lni-bolt-3', 'color' => '#F0ED81'],
    'integracao' => ['label' => 'Integração', 'icon' => 'lni-www', 'color' => '#C481F0'],
    'design' => ['label' => 'Design', 'icon' => 'lni-colour-palette-3', 'color' => '#DA81F0'],
    'outro' => ['label' => 'Outro', 'icon' => 'lni-menu-meatballs-1', 'color' => '#5C5C5C'],
];
?>

<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 gap-3">
    <div>
        <h5 class="mb-1">Projetos</h5>
        <div class="small text-muted">Lista de projetos, clientes e prazos.</div>
    </div>

    <div>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNovoProjeto">
            <i class="lni lni-plus me-1"></i> Novo projeto
        </button>
    </div>
</div>

<div class="row g-3">
    <?php if (empty($projetos)): ?>
        <div class="col-12">
            <div class="alert alert-light border small mb-0">
                Nenhum projeto cadastrado até o momento.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($projetos as $p): ?>
            <?php
            $tipoInfo = $mapTipos[$p['tipo_projeto']] ?? [
                'label' => ucfirst($p['tipo_projeto']),
                'icon' => 'lni-more-alt',
                'color' => '#5C5C5C',
            ];
            $inicio = $p['data_inicio'] ? date('d/m/Y', strtotime($p['data_inicio'])) : '—';
            $entrega = $p['data_entrega'] ? date('d/m/Y', strtotime($p['data_entrega'])) : '—';
            ?>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">

                        <!-- Ícone + tipo -->
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-2" style="
                     width: 40px;
                     height: 40px;
                     border-radius: 50%;
                     display: flex;
                     align-items: center;
                     justify-content: center;
                     background-color: <?= htmlspecialchars($tipoInfo['color']) ?>20;
                   ">
                                <i class="lni <?= htmlspecialchars($tipoInfo['icon']) ?>"
                                    style="font-size: 1.4rem; color: <?= htmlspecialchars($tipoInfo['color']) ?>;"></i>
                            </div>
                            <div>
                                <span class="small text-muted d-block">Tipo do projeto</span>
                                <strong class="small"><?= htmlspecialchars($tipoInfo['label']) ?></strong>
                            </div>
                        </div>

                        <!-- Nome do projeto -->
                        <h6 class="mt-2 mb-1">
                            <?= htmlspecialchars($p['nome_projeto']) ?>
                        </h6>

                        <!-- Cliente -->
                        <p class="small text-muted mb-2">
                            Cliente:
                            <?php if (!empty($p['cliente_nome'])): ?>
                                <?= htmlspecialchars($p['cliente_nome']) ?>
                            <?php else: ?>
                                <span class="text-muted">Não vinculado</span>
                            <?php endif; ?>
                        </p>

                        <!-- Datas -->
                        <p class="small mb-1">
                            Início: <strong><?= $inicio ?></strong>
                        </p>
                        <p class="small mb-3">
                            Entrega: <strong><?= $entrega ?></strong>
                        </p>

                        <!-- Ações -->
                        <!-- Ações -->
                        <div class="mt-auto d-flex justify-content-between gap-2">
                            <a href="/modules/painel.php?mod=projeto_detalhe&id=<?= (int) $p['id'] ?>"
                                class="btn btn-outline-primary btn-sm w-100">
                                Detalhes
                            </a>

                            <button type="button" class="btn btn-outline-secondary btn-sm" title="Editar" data-bs-toggle="modal"
                                data-bs-target="#modalEditarProjeto" data-id="<?= (int) $p['id'] ?>">
                                <i class="lni lni-file-pencil"></i>
                            </button>

                            <form method="post" action="/actions/concluir_projeto.php"
                                onsubmit="return confirm('Concluir este projeto? Ele será removido da lista.');">
                                <input type="hidden" name="projeto_id" value="<?= (int) $p['id'] ?>">
                                <button type="submit" class="btn btn-outline-success btn-sm" title="Concluir projeto">
                                    <i class="lni lni-check"></i>
                                </button>
                            </form>
                        </div>


                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../modals/modal_novo_projeto.php'; ?>