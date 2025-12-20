<?php
// modules/modals/modal_saida.php
?>
<div class="modal fade modal-right" id="modalNovaSaida" tabindex="-1" aria-labelledby="modalNovaSaidaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="/app/Controllers/FinanceiroController.php" id="form-nova-saida">
        <input type="hidden" name="acao" value="adicionar_saida">
        <div class="modal-header">
          <h5 class="modal-title" id="modalNovaSaidaLabel">Adicionar saída</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label small">Data</label>
              <input type="date" name="data_lancamento" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="col-md-8">
              <label class="form-label small">Descrição</label>
              <input type="text" name="descricao" class="form-control"
                placeholder="Ex: Almoço com cliente, mensalidade ferramenta, etc." required>
            </div>

            <div class="col-md-6">
              <label class="form-label small">Tipo de gasto</label>
              <select name="tipo" class="form-select" required>
                <option value="mercado">Mercado</option>
                <option value="lanche">Lanche</option>
                <option value="almoco">Almoço</option>
                <option value="pagamentos">Pagamentos</option>
                <option value="retiradas">Retiradas</option>
                <option value="outro">Outro</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label small">Valor</label>
              <input type="text" step="0.01" min="0" name="valor" class="form-control js-money" placeholder="0,00"
                required>
            </div>

            <div class="col-12">
              <label class="form-label small">Observações (opcional)</label>
              <textarea name="observacoes" class="form-control" rows="3"
                placeholder="Ex: categoria detalhada, forma de pagamento, etc."></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar saída</button>
        </div>
      </form>
    </div>
  </div>
</div>