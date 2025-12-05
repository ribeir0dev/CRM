<?php
// modules/content/clientes.php
if (session_status() !== PHP_SESSION_ACTIVE)
    session_start();
require_once __DIR__ . '/../../inc/conf/db.php';

$status_cliente = $_GET['status_cliente'] ?? 'todos';
$busca = $_GET['busca'] ?? '';

// Monta SQL com filtros
$sql = "SELECT id, nome, whatsapp, email, foto_perfil, genero, status FROM clientes WHERE 1=1";
$params = [];

// Filtro de status
if ($status_cliente !== 'todos') {
    $sql .= " AND status = ?";
    $params[] = $status_cliente;
}

// Filtro de busca (nome, email, whatsapp)
if ($busca !== '') {
    $sql .= " AND (LOWER(nome) LIKE ? OR LOWER(email) LIKE ? OR LOWER(whatsapp) LIKE ?)";
    $like = '%' . mb_strtolower($busca) . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$sql .= " ORDER BY nome ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$clientes_filtrados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Linha 1: título + botão CTA -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Clientes</h5>
    <button class="btn btn-primary btn-sm d-flex align-items-center" data-bs-toggle="modal"
        data-bs-target="#modalNovoCliente">
        <i class="lni lni-plus me-2"></i>Adicionar cliente
    </button>
</div>

<!-- Linha 2: filtros + busca -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
    <div class="filtros-clientes">
        <a href="?mod=clientes&status_cliente=todos"
            class="btn btn-status <?= $status_cliente === 'todos' ? 'btn-status-active' : '' ?>">Todos</a>

        <a href="?mod=clientes&status_cliente=ativo"
            class="btn btn-status <?= $status_cliente === 'ativo' ? 'btn-status-active' : '' ?>">Ativo</a>

        <a href="?mod=clientes&status_cliente=inativo"
            class="btn btn-status <?= $status_cliente === 'inativo' ? 'btn-status-active' : '' ?>">Inativo</a>

        <a href="?mod=clientes&status_cliente=potencial"
            class="btn btn-status <?= $status_cliente === 'potencial' ? 'btn-status-active' : '' ?>">Em potencial</a>
    </div>

    <form method="get" class="d-flex align-items-center gap-2">
        <input type="hidden" name="mod" value="clientes">
        <input type="hidden" name="status_cliente" value="<?= htmlspecialchars($status_cliente) ?>">

        <div class="search-wrapper">
            <i class="lni lni-search-2 search-icon"></i>
            <input type="text" class="search-input" name="busca" placeholder="Buscar por nome, email ou WhatsApp"
                value="<?= htmlspecialchars($busca) ?>">
        </div>
    </form>
    
    </form>
</div>

<!-- Linha 3: cards de clientes -->
<div class="row g-3">
    <?php foreach ($clientes_filtrados as $cli): ?>
        <?php
        $temFoto = !empty($cli['foto_perfil']);
        $inicial = strtoupper(mb_substr($cli['nome'], 0, 1));
        $genero = $cli['genero'] ?? 'empresa';

        $classeGenero = match ($genero) {
            'masculino' => 'cliente-masculino',
            'feminino' => 'cliente-feminino',
            default => 'cliente-empresa',
        };
        ?>
        <div class="col-md-4 col-lg-3">
            <a href="painel.php?mod=cliente&id=<?= (int) $cli['id'] ?>" class="text-decoration-none">
                <div class="card shadow-sm h-100 cliente-card <?= $classeGenero ?>">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="cliente-avatar me-2">
                                <?php if ($temFoto): ?>
                                    <img src="<?= htmlspecialchars($cli['foto_perfil']) ?>"
                                        alt="Foto de <?= htmlspecialchars($cli['nome']) ?>" class="rounded-circle cliente-foto">
                                <?php else: ?>
                                    <span><?= $inicial ?></span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h6 class="mb-0"><?= htmlspecialchars($cli['nome']) ?></h6>
                                <small class="text-muted"><?= htmlspecialchars($cli['email']) ?></small>
                            </div>
                        </div>
                        <div class="small text-muted mb-1">
                            <i class="lni lni-whatsapp me-1"></i><?= htmlspecialchars($cli['whatsapp']) ?>
                        </div>
                        <span class="badge
                            <?= $cli['status'] === 'ativo'
                                ? 'bg-success-subtle text-success'
                                : ($cli['status'] === 'potencial'
                                    ? 'bg-warning-subtle text-warning'
                                    : 'bg-secondary-subtle text-secondary') ?>">
                            <?= ucfirst($cli['status']) ?>
                        </span>
                    </div>
                </div>
            </a>
        </div>

    <?php endforeach; ?>

    <?php if (empty($clientes_filtrados)): ?>
        <div class="col-12">
            <p class="text-muted small mb-0">Nenhum cliente encontrado com os filtros atuais.</p>
        </div>
    <?php endif; ?>
</div>


<?php
// Inclui o modal de novo cliente (pasta modules/modals)
include __DIR__ . '/../modals/modal_novo_cliente.php';
?>