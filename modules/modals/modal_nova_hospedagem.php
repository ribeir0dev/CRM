<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../../config/db.php';
?>
<div class="modal fade modal-right" id="modalNovaHospedagem" tabindex="-1" aria-labelledby="modalNovaHospedagemLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="/app/Controllers/HospedagemController.php">
        <div class="modal-header">
          <h5 class="modal-title" id="modalNovaHospedagemLabel">Nova hospedagem</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small">Nome da hospedagem</label>
            <input type="text" name="nome" class="form-control" required
                   placeholder="Ex: Hospedagem WordPress Cliente X">
          </div>

          <div class="mb-3">
            <label class="form-label small">Tipo da hospedagem</label>
            <select name="tipo" class="form-select form-select-sm" required>
              <option value="wordpress">WordPress</option>
              <option value="vps">VPS</option>
              <option value="dominio">Domínio</option>
            </select>
          </div>

          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label small">Data de início</label>
              <input type="date" name="data_inicio" class="form-control form-control-sm"
                     value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small">Data de término</label>
              <input type="date" name="data_fim" class="form-control form-control-sm" required>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>
