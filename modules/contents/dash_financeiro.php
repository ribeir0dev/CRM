<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__.'/../../includes/config.php';
require_once __DIR__.'/../../includes/db.php';

// Flash Message
$msg = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'entrada_sucesso') $msg = 'Entrada cadastrada com sucesso!';
    if ($_GET['msg'] === 'saida_sucesso') $msg = 'Saída cadastrada com sucesso!';
    if ($_GET['msg'] === 'entrada_excluida') $msg = 'Entrada excluída com sucesso!';
    if ($_GET['msg'] === 'saida_excluida') $msg = 'Saída excluída com sucesso!';
}

// Exclusão
if (isset($_GET['del_entrada'])) {
    $id = intval($_GET['del_entrada']);
    $pdo->prepare("DELETE FROM financeiro_entradas WHERE id = ? LIMIT 1")->execute([$id]);
    header("Location: dashboard.php?mod=financeiro&msg=entrada_excluida");
    exit;
}
if (isset($_GET['del_saida'])) {
    $id = intval($_GET['del_saida']);
    $pdo->prepare("DELETE FROM financeiro_saidas WHERE id = ? LIMIT 1")->execute([$id]);
    header("Location: dashboard.php?mod=financeiro&msg=saida_excluida");
    exit;
}

// --------- FILTROS PARA ENTRADAS ---------
$where_e = [];
$params_e = [];
if(!empty($_GET['cliente'])) {
    $where_e[] = 'cliente_id = ?';
    $params_e[] = $_GET['cliente'];
}
if(!empty($_GET['data_ini'])) {
    $where_e[] = 'data >= ?';
    $params_e[] = $_GET['data_ini'];
}
if(!empty($_GET['data_fim'])) {
    $where_e[] = 'data <= ?';
    $params_e[] = $_GET['data_fim'];
}
if(!empty($_GET['pagamento'])) {
    $where_e[] = 'tipo_pagamento = ?';
    $params_e[] = $_GET['pagamento'];
}
if(!empty($_GET['tipo_servico'])) {
    $where_e[] = 'tipo_servico_id = ?';
    $params_e[] = $_GET['tipo_servico'];
}
if(empty($_GET['data_ini']) && empty($_GET['data_fim'])) {
    $where_e[] = 'YEAR(data) = ?';
    $params_e[] = date('Y');
    $where_e[] = 'MONTH(data) = ?';
    $params_e[] = date('m');
}
$where_sql_e = count($where_e) ? 'WHERE '.implode(' AND ', $where_e) : '';

// --------- FILTROS PARA SAÍDAS ---------
$where_s = [];
$params_s = [];
if(!empty($_GET['data_ini'])) {
    $where_s[] = 'data >= ?';
    $params_s[] = $_GET['data_ini'];
}
if(!empty($_GET['data_fim'])) {
    $where_s[] = 'data <= ?';
    $params_s[] = $_GET['data_fim'];
}
if(empty($_GET['data_ini']) && empty($_GET['data_fim'])) {
    $where_s[] = 'YEAR(data) = ?';
    $params_s[] = date('Y');
    $where_s[] = 'MONTH(data) = ?';
    $params_s[] = date('m');
}
$where_sql_s = count($where_s) ? 'WHERE '.implode(' AND ', $where_s) : '';

// --------- CARDS TOTAIS ---------
$stmt = $pdo->prepare("SELECT SUM(valor_recebido) FROM financeiro_entradas $where_sql_e");
$stmt->execute($params_e);
$total_entradas = $stmt->fetchColumn() ?: 0.00;

$stmt2 = $pdo->prepare("SELECT SUM(valor) FROM financeiro_saidas $where_sql_s");
$stmt2->execute($params_s);
$total_saidas = $stmt2->fetchColumn() ?: 0.00;

$total_caixa = $total_entradas - $total_saidas;

// --------- LISTAGEM ---------
$stmt3 = $pdo->prepare("SELECT fe.*, c.nome AS cliente_nome, ts.nome AS tipo_servico 
    FROM financeiro_entradas fe
    JOIN clientes c ON fe.cliente_id = c.id
    JOIN tipos_servico ts ON fe.tipo_servico_id = ts.id
    $where_sql_e
    ORDER BY fe.data DESC");
$stmt3->execute($params_e);
$entradas = $stmt3->fetchAll();

$stmt4 = $pdo->prepare("SELECT * FROM financeiro_saidas $where_sql_s ORDER BY data DESC");
$stmt4->execute($params_s);
$saidas = $stmt4->fetchAll();

// --------- Para selects de filtro ---------
$clientes = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome")->fetchAll();
$tipos_servico = $pdo->query("SELECT id, nome FROM tipos_servico ORDER BY nome")->fetchAll();
?>

<div class="financeiro-main pt-3 px-2 px-md-4">
  <!-- Flash Message -->
  <?php if($msg): ?>
    <div id="flashMsg" class="alert alert-success animate__animated animate__fadeInDown" style="max-width:350px;position:relative;z-index:10;">
      <?=$msg?>
    </div>
  <?php endif; ?>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm text-success">
        <div class="card-body">
          <div class="fw-bold small">Entradas</div>
          <div class="fs-4">R$ <?php echo number_format($total_entradas, 2, ',', '.'); ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm text-danger">
        <div class="card-body">
          <div class="fw-bold small">Saídas</div>
          <div class="fs-4">R$ <?php echo number_format($total_saidas, 2, ',', '.'); ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm text-primary">
        <div class="card-body">
          <div class="fw-bold small">Total Caixa</div>
          <div class="fs-4">R$ <?php echo number_format($total_caixa, 2, ',', '.'); ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filtros -->
  <form class="card border-0 shadow-sm mb-4 p-3" id="filtroFinanceiro" method="get" action="">
    <div class="row g-2 align-items-end">
      <div class="col-md-2">
        <label class="form-label">Cliente</label>
        <select class="form-select" name="cliente" id="filterCliente">
          <option value="">Todos</option>
          <?php foreach($clientes as $cli): ?>
            <option value="<?=$cli['id']?>" <?=(isset($_GET['cliente']) && $_GET['cliente'] == $cli['id'])?'selected':'';?>><?=$cli['nome']?></option>
          <?php endforeach;?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Data Inicial</label>
        <input type="date" class="form-control" name="data_ini" value="<?=htmlspecialchars($_GET['data_ini']??'')?>">
      </div>
      <div class="col-md-2">
        <label class="form-label">Data Final</label>
        <input type="date" class="form-control" name="data_fim" value="<?=htmlspecialchars($_GET['data_fim']??'')?>">
      </div>
      <div class="col-md-2">
        <label class="form-label">Tipo Pagamento</label>
        <select class="form-select" name="pagamento" id="filterPag">
          <option value="">Todos</option>
          <option <?=(isset($_GET['pagamento']) && $_GET['pagamento']=='Pix')?'selected':'';?>>Pix</option>
          <option <?=(isset($_GET['pagamento']) && $_GET['pagamento']=='Boleto')?'selected':'';?>>Boleto</option>
          <option <?=(isset($_GET['pagamento']) && $_GET['pagamento']=='Cartão')?'selected':'';?>>Cartão</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Tipo Serviço</label>
        <select class="form-select" name="tipo_servico" id="filterServ">
          <option value="">Todos</option>
          <?php foreach($tipos_servico as $ts): ?>
            <option value="<?=$ts['id']?>" <?=(isset($_GET['tipo_servico']) && $_GET['tipo_servico'] == $ts['id'])?'selected':'';?>><?=$ts['nome']?></option>
          <?php endforeach;?>
        </select>
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-grow-1">Filtrar</button>
        <a href="relatorio_financeiro.php<?php echo $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>" class="btn btn-outline-dark flex-grow-1">Relatório</a>
      </div>
    </div>
  </form>

  <!-- Ações rápidas -->
  <div class="mb-2 d-flex gap-2">
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#entradaModal">Adicionar Entrada</button>
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#saidaModal">Adicionar Saída</button>
  </div>

  <!-- Tabela de Entradas -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-light fw-bold">Entradas</div>
    <div class="table-responsive">
      <table class="table align-middle m-0">
        <thead>
          <tr>
            <th>Data</th>
            <th>Cliente</th>
            <th>Serviço</th>
            <th>Tipo Venda</th>
            <th>Pagamento</th>
            <th>A receber</th>
            <th>Recebido</th>
            <th>Comprovantes</th>
            <th class="text-center">Excluir</th>
          </tr>
        </thead>
        <tbody>
        <?php if($entradas): foreach($entradas as $e): ?>
          <tr>
            <td><?=date('d/m/Y',strtotime($e['data']))?></td>
            <td><?=$e['cliente_nome']?></td>
            <td><?=$e['tipo_servico']?></td>
            <td><?=$e['tipo_venda']?></td>
            <td><?=$e['tipo_pagamento']?></td>
            <td>R$ <?=number_format($e['valor_receber'],2,',','.')?></td>
            <td>R$ <?=number_format($e['valor_recebido'],2,',','.')?></td>
            <td>
              <?php if(!empty($e['comprovante1'])): ?>
                <a href="/flowdesk/uploads/<?=$e['comprovante1']?>" target="_blank">1</a>
              <?php endif; ?>
              <?php if(!empty($e['comprovante2'])): ?>
                <a href="/flowdesk/uploads/<?=$e['comprovante2']?>" target="_blank">2</a>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <a href="?mod=financeiro&del_entrada=<?=$e['id']?>" 
                 class="btn btn-outline-danger btn-sm" 
                 onclick="return confirm('Deseja realmente excluir esta entrada?');">
                 Excluir
              </a>
            </td>
          </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="9" class="text-center text-muted">Nenhuma entrada encontrada.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Tabela de Saídas -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-light fw-bold">Saídas</div>
    <div class="table-responsive">
      <table class="table align-middle m-0">
        <thead>
          <tr>
            <th>Data</th>
            <th>Descrição</th>
            <th>Tipo</th>
            <th>Pagamento</th>
            <th>Valor</th>
            <th class="text-center">Excluir</th>
          </tr>
        </thead>
        <tbody>
        <?php if($saidas): foreach($saidas as $s): ?>
          <tr>
            <td><?=date('d/m/Y',strtotime($s['data']))?></td>
            <td><?=$s['descricao']?></td>
            <td><?=$s['tipo_saida']?></td>
            <td><?=$s['tipo_pagamento']?></td>
            <td>R$ <?=number_format($s['valor'],2,',','.')?></td>
            <td class="text-center">
              <a href="?mod=financeiro&del_saida=<?=$s['id']?>" 
                 class="btn btn-outline-danger btn-sm"
                 onclick="return confirm('Deseja realmente excluir esta saída?');">
                 Excluir
              </a>
            </td>
          </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="6" class="text-center text-muted">Nenhuma saída encontrada.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modais -->
<div class="modal fade" id="entradaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content"><div class="modal-header"><h5 class="modal-title">Cadastrar Entrada</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body"><?php include __DIR__.'/form_entrada.php'; ?></div>
    </div>
  </div>
</div>
<div class="modal fade" id="saidaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content"><div class="modal-header"><h5 class="modal-title">Cadastrar Saída</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body"><?php include __DIR__.'/form_saida.php'; ?></div>
    </div>
  </div>
</div>

<script>
if(document.getElementById('flashMsg')){
  setTimeout(function(){
    document.getElementById('flashMsg').style.display = 'none';
  }, 2600);
}
</script>
<style>
.financeiro-main {
  margin-left: 0;
  margin-right: 0;
  width: 100% !important;
  max-width: none !important;
  box-sizing: border-box;
  padding-left: 18px;
  padding-right: 18px;
}
@media (min-width: 991px) {
  .financeiro-main {
    padding-left: 32px;
    padding-right: 32px;
  }
}
.table-responsive, .table {
  width: 100% !important;
}
</style>
