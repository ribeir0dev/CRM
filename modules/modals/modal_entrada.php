<?php
// modules/modals/modal_entrada.php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../../inc/conf/db.php';

// Busca clientes
$stmtCli = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome ASC");
$listaClientes = $stmtCli->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="modal fade" id="modalNovaEntrada" tabindex="-1" aria-labelledby="modalNovaEntradaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form method="post" action="/flowdesk_novo/actions/adicionar_entrada.php" id="form-nova-entrada">
        <div class="modal-header">
          <h5 class="modal-title" id="modalNovaEntradaLabel">Adicionar entrada</h5>
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
                placeholder="Ex: Site institucional para Cliente X" required>
            </div>
            <div class="col-12">
              <label class="form-label small">Cliente (opcional)</label>
              <select name="cliente_id" class="form-select form-select-sm">
                <option value="">Sem cliente vinculado</option>
                <?php foreach ($listaClientes as $c): ?>
                  <option value="<?= (int) $c['id'] ?>">
                    <?= htmlspecialchars($c['nome']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small">Serviço</label>
              <select name="servico" class="form-select" required>
                <option value="landing_page">Landing page</option>
                <option value="website">Website</option>
                <option value="configuracao">Configuração</option>
                <option value="alteracao">Alteração</option>
                <option value="design">Design</option>
                <option value="salario">Salário</option>
                <option value="outro">Outro</option>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label small">Tipo de pagamento</label>
              <select name="tipo_pagamento" class="form-select" required>
                <option value="50_50">50/50</option>
                <option value="integral" selected>Integral</option>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label small">Forma de pagamento</label>
              <select name="forma_pagamento" class="form-select" required>
                <option value="pix" selected>PIX</option>
                <option value="boleto">Boleto</option>
                <option value="cartao">Cartão</option>
                <option value="dinheiro">Dinheiro</option>
                <option value="outro">Outro</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label small">Valor a receber</label>
              <input type="text" step="0.01" min="0" name="valor_a_receber" class="form-control js-money"
                placeholder="0,00" required>
            </div>

            <div class="col-md-6">
              <label class="form-label small">Valor já recebido</label>
              <input type="text" step="0.01" min="0" name="valor_recebido" class="form-control js-money"
                placeholder="0,00" value="0">
            </div>

            <div class="col-12">
              <label class="form-label small">Observações (opcional)</label>
              <textarea name="observacoes" class="form-control" rows="3"
                placeholder="Detalhes sobre esta entrada, condições de pagamento, etc."></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar entrada</button>
        </div>
      </form>
    </div>
  </div>
</div>