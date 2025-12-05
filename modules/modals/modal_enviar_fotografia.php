<div class="modal fade" id="modalFotoCliente" tabindex="-1" aria-labelledby="modalFotoClienteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="/flowdesk_novo/actions/upload_foto_cliente.php" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="modalFotoClienteLabel">Alterar foto do cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="cliente_id" value="<?= (int)$cliente['id'] ?>">
          <!-- campo de arquivo -->
          <div class="mb-3">
            <label class="form-label small">Escolher arquivo</label>
            <input type="file" name="foto" class="form-control" accept="image/*" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar foto</button>
        </div>
      </form>
    </div>
  </div>
</div>
