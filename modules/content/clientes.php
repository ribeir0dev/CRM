<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../app/Models/ClienteModel.php';

$status_cliente = $_GET['status_cliente'] ?? 'todos';
$busca          = trim($_GET['busca'] ?? '');

$model = new ClienteModel($pdo);
$clientes_filtrados = $model->listarFiltrados($status_cliente, $busca);
?>



<!-- Linha 1: título + botão CTA -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Clientes</h5>
    <button class="btn btn-primary btn-sm d-flex align-items-center" data-bs-toggle="modal"
        data-bs-target="#modalNovoCliente">
        <i class="ri-user-add-fill me-2"></i>Adicionar cliente
    </button>
</div>

<!-- Linha 2: filtros + busca -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">

  <!-- Dropdown de status -->
  <form method="get" class="d-flex align-items-center col-6 gap-2">
    <input type="hidden" name="mod" value="clientes">
    <input type="hidden" name="busca" value="<?= htmlspecialchars($busca) ?>">

    <div>
      <label class="form-label small mb-1">Status</label>
      <select name="status_cliente"
              class="form-select form-select-sm"
              onchange="this.form.submit()">
        <option value="todos"     <?= $status_cliente === 'todos' ? 'selected' : '' ?>>Todos</option>
        <option value="ativo"     <?= $status_cliente === 'ativo' ? 'selected' : '' ?>>Ativo</option>
        <option value="inativo"   <?= $status_cliente === 'inativo' ? 'selected' : '' ?>>Inativo</option>
        <option value="potencial" <?= $status_cliente === 'potencial' ? 'selected' : '' ?>>Em potencial</option>
      </select>
    </div>
  </form>
</div>

<!-- Linha 3: cards de clientes -->
<div class="row g-3">
  <?php foreach ($clientes_filtrados as $cli): ?>
    <?php
    $temFoto   = !empty($cli['foto_perfil']);
    $inicial   = strtoupper(mb_substr($cli['nome'], 0, 1));
    $genero    = $cli['genero'] ?? 'empresa';

    $classeGenero = match ($genero) {
        'masculino' => 'cliente-masculino',
        'feminino'  => 'cliente-feminino',
        default     => 'cliente-empresa',
    };

    $statusClasse = $cli['status'] === 'ativo'
      ? 'bg-success-subtle text-success'
      : ($cli['status'] === 'potencial'
          ? 'bg-warning-subtle text-warning'
          : 'bg-secondary-subtle text-secondary');

    $criadoEm = $cli['criado_em']
      ? date('d/m/Y H:i', strtotime($cli['criado_em']))
      : null;
    ?>
    <div class="col-md-4 col-lg-3">
      <a href="painel.php?mod=cliente&id=<?= (int)$cli['id'] ?>" class="text-decoration-none">
        <div class="card  h-100 cliente-card">
          <div class="card-body p-0">

            <!-- Topo colorido -->
            <div class="d-flex align-items-start justify-content-between px-3 py-2 topo-cliente <?= $classeGenero ?>">
              <div class="d-flex align-items-center mt-2">
                <div class="cliente-avatar me-2">
                  <?php if ($temFoto): ?>
                    <img src="<?= htmlspecialchars($cli['foto_perfil']) ?>"
                         alt="Foto de <?= htmlspecialchars($cli['nome']) ?>"
                         class="rounded-circle cliente-foto"
                         width="40" height="40" style="object-fit:cover;">
                  <?php else: ?>
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:40px;height:40px;background:#f5f5f5;">
                      <span class="fw-semibold"><?= $inicial ?></span>
                    </div>
                  <?php endif; ?>
                </div>
                <div>
                  <h6 class="mb-0"><?= htmlspecialchars($cli['nome']) ?></h6>
                  <small class="text-muted">
                    <?= $criadoEm ? 'Desde: ' . $criadoEm : 'Data não informada' ?>
                  </small>
                </div>
              </div>
            </div>

            <!-- Corpo neutro -->
            <div class="px-3 pb-3 pt-2">

              <hr class="my-2">

              <div class="small text-muted mb-1 d-flex align-items-center">
                <i class="bi bi-whatsapp me-2"></i>
                <span><?= htmlspecialchars($cli['whatsapp'] ?: 'Sem telefone') ?></span>
              </div>
              <div class="small text-muted mb-2 d-flex align-items-center">
                <i class="bi bi-envelope-at-fill me-2"></i>
                <span><?= htmlspecialchars($cli['email'] ?: 'Sem e-mail') ?></span>
              </div>

              <span class="badge <?= $statusClasse ?>">
                <?= ucfirst($cli['status']) ?>
              </span>

            </div>
          </div>
        </div>
      </a>
    </div>
  <?php endforeach; ?>
</div>



<?php if (empty($clientes_filtrados)): ?>
  <p class="text-muted small mt-3 mb-0">Nenhum cliente encontrado com os filtros atuais.</p>
<?php endif; ?>

<?php include __DIR__ . '/../modals/modal_novo_cliente.php'; ?>
