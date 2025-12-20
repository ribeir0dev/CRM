<?php
// modules/modals/modal_editar_cliente.php
// Usa a variável $cliente já carregada na página cliente.php
?>
<div class="modal fade modal-right" id="modalEditarCliente" tabindex="-1" aria-labelledby="modalEditarClienteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form method="post" action="/app/Controllers/ClienteController.php?acao=atualizar" id="form-editar-cliente">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditarClienteLabel">Editar cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id" value="<?= isset($cliente['id']) ? (int)$cliente['id'] : 0; ?>">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small">Nome completo</label>
              <input type="text" name="nome" class="form-control"
                     value="<?= htmlspecialchars($cliente['nome'] ?? '') ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label small">WhatsApp</label>
              <input type="text" name="whatsapp" class="form-control"
                     value="<?= htmlspecialchars($cliente['whatsapp'] ?? '') ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label small">E-mail</label>
              <input type="email" name="email" class="form-control"
                     value="<?= htmlspecialchars($cliente['email'] ?? '') ?>" required>
            </div>

            <div class="col-md-3">
              <label class="form-label small">Gênero / Tipo</label>
              <select name="genero" class="form-select">
                <?php $g = $cliente['genero'] ?? 'empresa'; ?>
                <option value="masculino" <?= $g === 'masculino' ? 'selected' : '' ?>>Masculino</option>
                <option value="feminino"  <?= $g === 'feminino'  ? 'selected' : '' ?>>Feminino</option>
                <option value="empresa"   <?= $g === 'empresa'   ? 'selected' : '' ?>>Empresa</option>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label small">Status</label>
              <select name="status" class="form-select">
                <?php $s = $cliente['status'] ?? 'ativo'; ?>
                <option value="ativo"     <?= $s === 'ativo'     ? 'selected' : '' ?>>Ativo</option>
                <option value="potencial" <?= $s === 'potencial' ? 'selected' : '' ?>>Em potencial</option>
                <option value="inativo"   <?= $s === 'inativo'   ? 'selected' : '' ?>>Inativo</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label small">Observações internas</label>
              <textarea name="observacoes" class="form-control" rows="3"
                        placeholder="Notas, contexto, preferências do cliente..."><?= htmlspecialchars($cliente['observacoes'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar alterações</button>
        </div>
      </form>
    </div>
  </div>
</div>
