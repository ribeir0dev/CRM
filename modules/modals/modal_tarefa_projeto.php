<?php
// modules/modals/modal_tarefa_projeto.php
if (session_status() !== PHP_SESSION_ACTIVE)
  session_start();
require_once __DIR__ . '/../../config/db.php';

$projeto_id = (int) ($_GET['id'] ?? 0); // mesmo id usado em projeto_detalhe.php
?>
<div class="modal fade modal-right" id="modalNovaTarefa" tabindex="-1" aria-labelledby="modalNovaTarefaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="/app/Controllers/ProjetoController.php?acao=salvarTarefa" id="form-tarefa-projeto">
        <input type="hidden" name="projeto_id" value="<?= $projeto_id ?>">
        <input type="hidden" name="tarefa_id" id="tarefaId" value="">
        <input type="hidden" name="coluna" id="tarefaColuna" value="backlog">

        <div class="modal-header">
          <h5 class="modal-title" id="modalNovaTarefaLabel">Nova tarefa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small">Título da tarefa</label>
            <input type="text" name="titulo" id="tarefaTitulo" class="form-control"
              placeholder="Ex: Configurar DNS, Criar layout da seção hero" required>
          </div>

          <div class="mb-3">
            <label class="form-label small">Descrição (opcional)</label>
            <textarea name="descricao" id="tarefaDescricao" class="form-control" rows="3"
              placeholder="Detalhes, links, checklist simples, etc."></textarea>
          </div>

          <div class="mb-0">
            <label class="form-label small">Coluna</label>
            <select name="coluna_select" id="tarefaColunaSelect" class="form-select form-select-sm">
              <option value="backlog">Backlog</option>
              <option value="andamento">Em andamento</option>
              <option value="revisao">Revisão</option>
              <option value="concluido">Concluído</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label small">Data de entrega</label>
            <input type="date" name="data_entrega" id="tarefaDataEntrega" class="form-control form-control-sm">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar tarefa</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal reaproveitado para edição -->
<div class="modal fade modal-right" id="modalEditarTarefa" tabindex="-1" aria-labelledby="modalEditarTarefaLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- usa o mesmo form, apenas preenchido via JS -->
      <form method="post" action="/app/Controllers/ProjetoController.php?acao=atualizarTarefa" id="form-editar-tarefa">
        <input type="hidden" name="projeto_id" value="<?= $projeto_id ?>">
        <input type="hidden" name="tarefa_id" id="editTarefaId" value="">
        <input type="hidden" name="coluna" id="editTarefaColuna" value="backlog">

        <div class="modal-header">
          <h5 class="modal-title" id="modalEditarTarefaLabel">Editar tarefa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small">Título da tarefa</label>
            <input type="text" name="titulo" id="editTarefaTitulo" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label small">Descrição (opcional)</label>
            <textarea name="descricao" id="editTarefaDescricao" class="form-control" rows="3"></textarea>
          </div>

          <div class="mb-0">
            <label class="form-label small">Coluna</label>
            <select name="coluna_select" id="editTarefaColunaSelect" class="form-select form-select-sm">
              <option value="backlog">Backlog</option>
              <option value="andamento">Em andamento</option>
              <option value="revisao">Revisão</option>
              <option value="concluido">Concluído</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label small">Data de entrega</label>
            <input type="date" name="data_entrega" id="editTarefaDataEntrega" class="form-control form-control-sm">
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

