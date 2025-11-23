<?php
require_once __DIR__.'/../../includes/config.php';
require_once __DIR__.'/../../includes/db.php';
$clientes = $pdo->query("SELECT id, nome, telefone, email FROM clientes ORDER BY nome")->fetchAll();
?>

<div class="clientes-main pt-2 px-0 px-md-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0 fw-semibold" style="font-size:1.18rem;">Clientes cadastrados</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovoCliente" style="font-size:1rem;">
      <i class="bi bi-plus-lg"></i> Adicionar novo cliente
    </button>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle w-100 mb-0" style="background:#fff;">
      <thead class="table-light">
        <tr>
          <th style="min-width:120px;">Nome</th>
          <th style="min-width:100px;">Telefone</th>
          <th style="min-width:180px;">E-mail</th>
          <th class="text-center" style="width:100px;">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($clientes): ?>
          <?php foreach ($clientes as $c): ?>
            <tr>
              <td><?=htmlspecialchars($c['nome'])?></td>
              <td><?=htmlspecialchars($c['telefone'])?></td>
              <td><?=htmlspecialchars($c['email'])?></td>
              <td class="text-center">
                <a href="detalhes_cliente.php?id=<?=$c['id']?>" class="btn btn-outline-secondary btn-sm">
                  Detalhes
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" class="text-center text-muted">Nenhum cliente cadastrado.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- MODAL ADIÇÃO DE CLIENTE -->
<div class="modal fade" id="modalNovoCliente" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="salvar.php" method="post" id="formNovoCliente">
        <div class="modal-header">
          <h5 class="modal-title">Adicionar novo cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="tipo" value="cliente">
          <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" required maxlength="100">
          </div>
          <div class="mb-3">
            <label class="form-label">Telefone</label>
            <input type="text" name="telefone" class="form-control" maxlength="20">
          </div>
          <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" class="form-control" maxlength="120">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- CSS LOCAL -->
<style>
.clientes-main {
  margin-left: 0;
  margin-right: 0;
  width: 100% !important;
  max-width: none !important;
  box-sizing: border-box;
}
.table-responsive {
  width: 100% !important;
}
.table {
  width: 100% !important;
  margin-bottom: 0;
  background: #fff;
  border-radius: 8px;
}
/* Tira qualquer centralização herdada */
body, html, .col, .main-content, .container, .content-module {
  margin: 0 !important;
  padding: 0 !important;
  max-width: none !important;
}
</style>
