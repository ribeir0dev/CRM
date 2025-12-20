<?php
// modules/modals/modal_gasto_fixo.php
?>
<div class="modal fade modal-right" id="modalGastoFixo" tabindex="-1" aria-labelledby="modalGastoFixoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="/app/Controllers/FinanceiroController.php" id="form-gasto-fixo">
        <input type="hidden" name="acao" value="adicionar_fixo">
        <div class="modal-header">
          <h5 class="modal-title" id="modalGastoFixoLabel">Adicionar gasto fixo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label small">Tipo de gasto</label>
              <input type="text" name="tipo_gasto" class="form-control" placeholder="Ex: Hospedagem, Internet, Aluguel"
                required>
            </div>

            <div class="col-md-6">
              <label class="form-label small">Valor</label>
              <input type="text" step="0.01" min="0" name="valor" class="form-control js-money" placeholder="0,00"
                required>
              <div class="form-text small">
                Se for parcelado, este é o valor de <strong>cada parcela</strong>.
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label small">Data de início</label>
              <input type="date" name="data_inicio" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="ehParcelado" name="eh_parcelado">
                <label class="form-check-label small" for="ehParcelado">
                  Este gasto é parcelado
                </label>
              </div>
            </div>

            <div class="col-md-6 parcelas-field d-none">
              <label class="form-label small">Quantidade total de parcelas</label>
              <select name="parcelas_totais" class="form-select form-select-sm">
                <?php for ($i = 1; $i <= 24; $i++): ?>
                  <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
              </select>
            </div>

            <div class="col-md-6 parcelas-field d-none">
              <label class="form-label small">Parcelas restantes</label>
              <select name="parcelas_restantes" class="form-select form-select-sm">
                <?php for ($i = 1; $i <= 24; $i++): ?>
                  <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label small">Observações (opcional)</label>
              <textarea name="observacoes" class="form-control" rows="3"
                placeholder="Detalhes adicionais sobre este gasto fixo."></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar gasto fixo</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Mostra/esconde campos de parcelas quando marcar 'parcelado'
  document.addEventListener('DOMContentLoaded', function () {
    const chk = document.getElementById('ehParcelado');
    const fields = document.querySelectorAll('.parcelas-field');
    if (!chk) return;

    function toggleParcelas() {
      fields.forEach(f => {
        f.classList.toggle('d-none', !chk.checked);
      });
    }

    chk.addEventListener('change', toggleParcelas);
    toggleParcelas();
  });
</script>