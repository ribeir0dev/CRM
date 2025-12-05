<?php
// modules/modals/modal_novo_cliente.php
?>
<div class="modal fade" id="modalNovoCliente" tabindex="-1" aria-labelledby="modalNovoClienteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form method="post" action="/flowdesk_novo/actions/cadastrar_cliente.php" id="form-novo-cliente">
        <div class="modal-header">
          <h5 class="modal-title" id="modalNovoClienteLabel">Adicionar cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small">Nome completo</label>
              <input type="text" name="nome" class="form-control" placeholder="Nome do cliente" required>
            </div>

            <div class="col-md-6">
              <label class="form-label small">WhatsApp</label>
              <input type="text" name="whatsapp" class="form-control js-telefone" placeholder="(00) 00000-0000" required>
            </div>

            <div class="col-md-6">
              <label class="form-label small">E-mail</label>
              <input type="email" name="email" class="form-control" placeholder="email@cliente.com" required>
            </div>

            <div class="col-md-6">
              <label class="form-label small">Status</label>
              <select name="status" class="form-select">
                <option value="ativo" selected>Ativo</option>
                <option value="potencial">Em potencial</option>
                <option value="inativo">Inativo</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small">Gênero / Tipo</label>
              <select name="genero" class="form-select">
                <option value="masculino">Masculino</option>
                <option value="feminino">Feminino</option>
                <option value="empresa" selected>Empresa</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label small">Observações internas (opcional)</label>
              <textarea name="observacoes" class="form-control" rows="3"
                placeholder="Notas sobre o cliente, contexto, preferências..."></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar cliente</button>
        </div>
      </form>
    </div>
  </div>
</div>