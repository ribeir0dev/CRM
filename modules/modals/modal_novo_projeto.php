<?php
// modules/modals/modal_novo_projeto.php
if (session_status() !== PHP_SESSION_ACTIVE)
  session_start();
require_once __DIR__ . '/../../inc/conf/db.php';

// busca clientes para o select
$stmtCli = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome ASC");
$clientes = $stmtCli->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="modal fade" id="modalNovoProjeto" tabindex="-1" aria-labelledby="modalNovoProjetoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form method="post" action="/actions/cadastrar_projeto.php" id="form-novo-projeto">
        <div class="modal-header">
          <h5 class="modal-title" id="modalNovoProjetoLabel">Novo projeto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">

            <!-- Nome do projeto -->
            <div class="col-12">
              <label class="form-label small">Nome do projeto</label>
              <input type="text" name="nome_projeto" class="form-control"
                placeholder="Ex: Landing page Black Friday Cliente X" required>
            </div>

            <!-- Tipo do projeto -->
            <div class="col-md-6">
              <label class="form-label small">Tipo do projeto</label>
              <select name="tipo_projeto" class="form-select form-select-sm" required>
                <option value="landing_page">Landing Page</option>
                <option value="configuracao">Configuração</option>
                <option value="alteracao">Alteração</option>
                <option value="otimizacao">Otimização</option>
                <option value="integracao">Integração</option>
                <option value="design">Design</option>
                <option value="outro">Outro</option>
              </select>
            </div>

            <!-- Cliente -->
            <div class="col-md-6">
              <label class="form-label small">Cliente</label>
              <select name="cliente_id" class="form-select form-select-sm">
                <option value="">Sem cliente vinculado</option>
                <?php foreach ($clientes as $c): ?>
                  <option value="<?= (int) $c['id'] ?>">
                    <?= htmlspecialchars($c['nome']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text small">Opcional, mas recomendado.</div>
            </div>

            <!-- Datas -->
            <div class="col-md-6">
              <label class="form-label small">Início do projeto</label>
              <div class="d-flex align-items-center gap-2">
                <!-- input escondido -->
                <input type="date" id="dataInicioProjeto" name="data_inicio" class="form-control form-control-sm"
                  value="<?= date('Y-m-d') ?>"
                  style="position:absolute; opacity:0; pointer-events:none; width:0; height:0;" required>

                <!-- botão menor -->
                <button type="button" class="btn btn-outline-secondary btn-sm"
                  onclick="document.getElementById('dataInicioProjeto').showPicker();">
                  Escolher data
                </button>

                <!-- data escolhida -->
                <span class="small text-muted">
                  <span id="labelDataInicioProjeto"><?= date('d/m/Y') ?></span>
                </span>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label small">Data de entrega</label>
              <div class="d-flex align-items-center gap-2">
                <input type="date" id="dataEntregaProjeto" name="data_entrega" class="form-control form-control-sm"
                  style="position:absolute; opacity:0; pointer-events:none; width:0; height:0;" required>

                <button type="button" class="btn btn-outline-secondary btn-sm"
                  onclick="document.getElementById('dataEntregaProjeto').showPicker();">
                  Escolher data
                </button>

                <span class="small text-muted">
                  <span id="labelDataEntregaProjeto">—/—/----</span>
                </span>
              </div>
            </div>

            <!-- Status -->
            <div class="col-md-6">
              <label class="form-label small">Status</label>
              <select name="status" class="form-select form-select-sm">
                <option value="planejado" selected>Planejado</option>
                <option value="em_andamento">Em andamento</option>
                <option value="concluido">Concluído</option>
                <option value="pausado">Pausado</option>
                <option value="cancelado">Cancelado</option>
              </select>
            </div>

            <!-- Descrição -->
            <div class="col-12">
              <label class="form-label small">Descrição (opcional)</label>
              <textarea name="descricao" class="form-control" rows="3"
                placeholder="Resumo do escopo do projeto, objetivos, observações importantes."></textarea>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar projeto</button>
        </div>
      </form>
    </div>
  </div>
</div>