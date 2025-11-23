<form action="salvar.php" method="post" id="formSaidaFin">
  <div class="row g-2">
    <div class="col-12">
      <label class="form-label">Descrição</label>
      <input type="text" class="form-control" name="descricao" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Tipo de Saída</label>
      <select class="form-select" name="tipo_saida" required>
        <option>Contratação de Terceiros</option>
        <option>Boleto</option>
        <option>Conta</option>
        <option>Outros</option>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Tipo de Pagamento</label>
      <select class="form-select" name="tipo_pagamento" required>
        <option>Pix</option>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Data</label>
      <input type="date" class="form-control" name="data" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Valor</label>
      <input type="number" step="0.01" class="form-control" name="valor" required>
    </div>
  </div>
  <div class="mt-3 text-end">
    <button type="submit" class="btn btn-danger">Salvar Saída</button>
  </div>
</form>
