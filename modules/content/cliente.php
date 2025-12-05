<?php
// modules/content/cliente.php
if (session_status() !== PHP_SESSION_ACTIVE)
  session_start();
require_once __DIR__ . '/../../inc/conf/db.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: /flowdesk_novo/index.php');
  exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
  echo '<p class="text-muted">Cliente não informado.</p>';
  return;
}

// Busca dados do cliente
$stmt = $pdo->prepare("
    SELECT id, nome, whatsapp, email, status, foto_perfil, criado_em, token_publico
    FROM clientes
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
  echo '<p class="text-muted">Cliente não encontrado.</p>';
  return;
}

// Busca todos os blocos do cliente
$stmt = $pdo->prepare("
  SELECT slug, titulo, conteudo, compartilhado
  FROM cliente_blocos
  WHERE cliente_id = ?
");
$stmt->execute([$cliente['id']]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Indexa por slug e decodifica o JSON
$blocos = [];
foreach ($rows as $r) {
  $data = json_decode($r['conteudo'] ?? '{}', true); // array associativo[web:427][web:429]
  if (!is_array($data))
    $data = [];
  $blocos[$r['slug']] = [
    'titulo' => $r['titulo'],
    'dados' => $data,
    'compartilhado' => (int) $r['compartilhado'],
  ];
}

// Divide nome em primeiro/último
$partes_nome = preg_split('/\s+/', trim($cliente['nome']));
$primeiro_nome = $partes_nome[0] ?? '';
$ultimo_nome = count($partes_nome) > 1 ? end($partes_nome) : '';
$foto = $cliente['foto_perfil'] ?: '/flowdesk_novo/assets/img/avatar.png';
?>

<!-- Topbar interna do cliente (dentro do main do painel) -->
<div class="d-flex align-items-center justify-content-between gap-3 mb-3">
    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarCliente">
      <i class="lni lni-pen-to-square me-1"></i>Editar Cliente
    </button>  
      <a href="painel.php?mod=clientes" class="btn btn-outline-secondary btn-sm">
      <i class="lni lni-arrow-left me-1"></i> Voltar para clientes
    </a>
</div>

<!-- 1ª linha: card com foto + botão editar -->
<div class="row g-3 mb-3">
  <div class="col-lg-4">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column align-items-center text-center">
        <div class="cliente-foto-wrapper mb-3">
          <img src="<?= htmlspecialchars($foto) ?>" alt="Foto do cliente" class="rounded-circle cliente-foto">
          <div class="mt-2">
            <button type="button" class="btn btn-outline-secondary btn-sm mt-2" data-bs-toggle="modal"
              data-bs-target="#modalFotoCliente">
              <i class="lni lni-camera me-1"></i>Enviar foto
            </button>
          </div>

        </div>
        <h5 class="mb-1"><?= htmlspecialchars($cliente['nome']) ?></h5>
        <span class="badge
                    <?= $cliente['status'] === 'ativo'
                      ? 'bg-success-subtle text-success'
                      : ($cliente['status'] === 'potencial'
                        ? 'bg-warning-subtle text-warning'
                        : 'bg-secondary-subtle text-secondary') ?>">
          <?= ucfirst($cliente['status']) ?>
        </span>
      </div>
    </div>
  </div>

  <!-- 2ª linha: dados básicos -->
  <div class="col-lg-8">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <h6 class="mb-3">Dados do cliente</h6>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small text-muted">Primeiro nome</label>
            <div class="form-control-plaintext fw-semibold"><?= htmlspecialchars($primeiro_nome) ?></div>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted">Último nome</label>
            <div class="form-control-plaintext fw-semibold"><?= htmlspecialchars($ultimo_nome) ?></div>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted">WhatsApp</label>
            <div class="form-control-plaintext">
              <i class="lni lni-whatsapp me-1 text-success"></i><?= htmlspecialchars($cliente['whatsapp']) ?>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted">E-mail</label>
            <div class="form-control-plaintext"><?= htmlspecialchars($cliente['email']) ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 3ª linha: conteúdos/acessos -->

<div class="row g-3 mb-3">
  <div class="col=md=4 col-lg-3">
    <h6>Dados de Acesso</h6>
  </div>
</div>
<?php
$website = $blocos['website']['dados'] ?? [];
$hosp = $blocos['hospedagem']['dados'] ?? [];
$siteAcc = $blocos['acesso_site']['dados'] ?? [];
$reg = $blocos['registro_br']['dados'] ?? [];
?>


<div class="row g-3 mb-3">
  <div class="col-md-6 col-lg-3">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column">
        <h6 class="mb-2"><i class="lni lni-world me-2"></i>Website</h6>
        <p class="small text-muted mb-1">Informações gerais do site do cliente.</p>

        <p class="small mb-3">
          URL:
          <?php if (!empty($website['url'])): ?>
            <a href="<?= htmlspecialchars($website['url']) ?>" target="_blank">
              <?= htmlspecialchars($website['url']) ?>
            </a>
          <?php else: ?>
            <span class="text-muted">Não informado</span>
          <?php endif; ?>
        </p>

        <button type="button" class="btn btn-outline-primary btn-sm w-100 mt-auto" data-bs-toggle="modal"
          data-bs-target="#modalBlocoCliente" data-slug="website" data-titulo="Website">
          Ver/gerenciar
        </button>
      </div>
    </div>
  </div>


  <div class="col-md-6 col-lg-3">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column">
        <h6 class="mb-2"><i class="lni lni-server me-2"></i>Acesso à hospedagem</h6>
        <p class="small text-muted mb-1">Dados de login do painel da hospedagem.</p>

        <p class="small mb-1">URL: <?= htmlspecialchars($hosp['url'] ?? '—') ?></p>
        <p class="small mb-1">Usuário: <?= htmlspecialchars($hosp['usuario'] ?? '—') ?></p>
        <p class="small mb-3">Senha: <?= htmlspecialchars($hosp['senha'] ?? '—') ?></p>

        <button type="button" class="btn btn-outline-primary btn-sm w-100 mt-auto" data-bs-toggle="modal"
          data-bs-target="#modalBlocoCliente" data-slug="hospedagem" data-titulo="Acesso à hospedagem">
          Ver/gerenciar
        </button>
      </div>
    </div>
  </div>


  <div class="col-md-6 col-lg-3">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column">
        <h6 class="mb-2"><i class="lni lni-lock-alt me-2"></i>Acesso ao website</h6>
        <p class="small text-muted mb-1">Credenciais do CMS / painel do site.</p>

        <p class="small mb-1">URL: <?= htmlspecialchars($siteAcc['url'] ?? '—') ?></p>
        <p class="small mb-1">Usuário: <?= htmlspecialchars($siteAcc['usuario'] ?? '—') ?></p>
        <p class="small mb-3">Senha: <?= htmlspecialchars($siteAcc['senha'] ?? '—') ?></p>

        <button type="button" class="btn btn-outline-primary btn-sm w-100 mt-auto" data-bs-toggle="modal"
          data-bs-target="#modalBlocoCliente" data-slug="acesso_site" data-titulo="Acesso ao website">
          Ver/gerenciar
        </button>
      </div>
    </div>
  </div>


  <div class="col-md-6 col-lg-3">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column">
        <h6 class="mb-2"><i class="lni lni-domain me-2"></i>Acesso Registro.br</h6>
        <p class="small text-muted mb-1">Domínios, DNS e conta no Registro.br.</p>

        <p class="small mb-1">URL: <?= htmlspecialchars($reg['url'] ?? '—') ?></p>
        <p class="small mb-1">Usuário: <?= htmlspecialchars($reg['usuario'] ?? '—') ?></p>
        <p class="small mb-3">Senha: <?= htmlspecialchars($reg['senha'] ?? '—') ?></p>

        <button type="button" class="btn btn-outline-primary btn-sm w-100 mt-auto" data-bs-toggle="modal"
          data-bs-target="#modalBlocoCliente" data-slug="registro_br" data-titulo="Acesso Registro.br">
          Ver/gerenciar
        </button>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col=md=4 col-lg-3">
    <h6>Arquivos do Cliente</h6>
  </div>
</div>

<!-- 4ª linha: anexos -->
<div class="row g-3 mb-3">
  <div class="col-md-6 col-lg-3">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column">
        <h6 class="mb-2"><i class="lni lni-licencse me-2"></i>Contratos</h6>
        <p class="small text-muted flex-grow-1">Documentos contratuais assinados.</p>
        <a href="#" class="btn btn-sm btn-outline-primary w-100 mt-2">Ver/gerenciar</a>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column">
        <h6 class="mb-2"><i class="lni lni-image me-2"></i>Logos</h6>
        <p class="small text-muted flex-grow-1">Arquivos de identidade visual do cliente.</p>
        <a href="#" class="btn btn-sm btn-outline-primary w-100 mt-2">Ver/gerenciar</a>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column">
        <h6 class="mb-2"><i class="lni lni-receipt me-2"></i>Recibos de pagamentos</h6>
        <p class="small text-muted flex-grow-1">Comprovantes e faturas pagas.</p>
        <a href="#" class="btn btn-sm btn-outline-primary w-100 mt-2">Ver/gerenciar</a>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12 mt-3 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
    <?php
    $dataCadastro = $cliente['criado_em'] ?? null;
    ?>
    <a>Cliente Cadastrado em: <?php echo date('d/m/Y', strtotime($dataCadastro)); ?>
    </a>
    <a href="/flowdesk_novo/modules/content/relatorio_cliente.php?token=<?= urlencode($cliente['token_publico']) ?>"
      target="_blank" class="btn btn-outline-secondary btn-sm">
      Gerar link de relatório<i class="lni lni-unlink-2-angular-eft ms-2"></i>
    </a>
  </div>
</div>

</div>

<?php
// Modal de edição do cliente (a criar em modules/modals/modal_editar_cliente.php)
include __DIR__ . '/../modals/modal_editar_cliente.php';
include __DIR__ . '/../modals/modal_foto_cliente.php';
include __DIR__ . '/../modals/modal_enviar_fotografia.php';
include __DIR__ . '/../modals/modal_bloco_cliente.php';
?>