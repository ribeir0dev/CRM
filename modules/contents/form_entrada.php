<form action="salvar.php" method="post" enctype="multipart/form-data" id="formEntradaFin">
  <div class="row g-2">
    <div class="col-md-6">
      <label class="form-label">Cliente</label>
      <select class="form-select" name="cliente_id" required>
        <option value="">Selecione...</option>
        <?php foreach($clientes as $cli): ?>
          <option value="<?php echo $cli['id']; ?>"><?php echo htmlspecialchars($cli['nome']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Tipo de Serviço</label>
      <!-- Exemplo -->
    <select name="tipo_servico_id" class="form-select" required>
      <?php foreach($tipos_servico as $ts): ?>
        <option value="<?=$ts['id']?>"><?=htmlspecialchars($ts['nome'])?></option>
      <?php endforeach; ?>
    </select>
    </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Data</label>
      <input type="date" class="form-control" name="data" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Tipo de Venda</label>
      <select class="form-select" name="tipo_venda" required>
        <option value="50/50">50/50</option>
        <option value="Inteiro">Inteiro</option>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Tipo de Pagamento</label>
      <select class="form-select" name="tipo_pagamento" required>
        <option>Pix</option>
        <option>Boleto</option>
        <option>Cartão</option>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Valor a receber</label>
      <input type="number" step="0.01" class="form-control" name="valor_receber" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Valor recebido</label>
      <input type="number" step="0.01" class="form-control" name="valor_recebido" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Comprovante 1</label>
      <input type="file" class="form-control" name="comprovante1" accept="image/*,application/pdf">
    </div>
    <div class="col-md-6">
      <label class="form-label">Comprovante 2</label>
      <input type="file" class="form-control" name="comprovante2" accept="image/*,application/pdf">
    </div>
  </div>
  <div class="mt-3 text-end">
    <button type="submit" class="btn btn-success">Salvar Entrada</button>
  </div>
</form>
