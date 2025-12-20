
<div class="modal fade modal-right" id="modalEditarProjeto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form method="post" action="/app/Controllers/ProjetoController.php?acao=atualizarProjeto" id="form-editar-projeto">
        <input type="hidden" name="projeto_id" id="editProjetoId" value="">

        <div class="modal-header">
          <h5 class="modal-title">Editar projeto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <div class="modal-body">
          <!-- mesmos campos do modalNovoProjeto, com ids edit... para preencher via JS -->
          <!-- ex.: -->
          <div class="mb-3">
            <label class="form-label small">Nome do projeto</label>
            <input type="text" name="nome_projeto" id="editNomeProjeto" class="form-control" required>
          </div>
          <!-- restante campos -->
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar alterações</button>
        </div>
      </form>
    </div>
  </div>
</div>
