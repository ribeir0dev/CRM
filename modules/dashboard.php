<?php
$pageTitle = "Dashboard | FlowDesk CRM";
include __DIR__.'/../includes/header_dash.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: /flowdesk/index.php');
    exit;
}
$user_name = isset($_SESSION['user_nome']) ? $_SESSION['user_nome'] : 'Usuário';
$mod = isset($_GET['mod']) ? $_GET['mod'] : 'home';
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/flowdesk/assets/css/custom.css">

<div class="container-fluid">
  <div class="row flex-nowrap min-vh-100">
    <!-- Sidebar -->
    <nav class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dash border-end min-vh-100">
      <div class="d-flex flex-column align-items-sm-start pt-3 min-vh-100">
        <a href="#" class="d-flex align-items-center mb-3 ms-2 mb-md-0 me-md-auto">
          <img src="/flowdesk/assets/images/icon.png" width="32" class="me-2" alt="Logo">
          <span class="fs-4 d-none d-md-inline text-primary fw-bold logo-text">FlowDesk</span>
        </a>
        <a> <span class="logo-alt-text">Sistema de gerenciamento CRM Interno</span></a>
        <ul class="nav nav-pills flex-column mb-auto w-100 mt-4" id="sidebarMenu">
          <li class="nav-item"><a href="#" class="nav-link<?= $mod == 'home' ? ' active' : '' ?>" data-content="home"><i class="bi bi-house"></i> Dashboard</a></li>
          <li><a href="#" class="nav-link<?= $mod == 'financeiro' ? ' active' : '' ?>" data-content="financeiro"><i class="bi bi-cash-coin"></i> Financeiro</a></li>
          <li><a href="#" class="nav-link<?= $mod == 'clientes' ? ' active' : '' ?>" data-content="clientes"><i class="bi bi-people"></i> Clientes</a></li>
          <li><a href="#" class="nav-link<?= $mod == 'jobs' ? ' active' : '' ?>" data-content="jobs"><i class="bi bi-kanban"></i> Jobs</a></li>
          <li><a href="#" class="nav-link<?= $mod == 'hospedagem' ? ' active' : '' ?>" data-content="hospedagem"><i class="bi bi-globe"></i> Hospedagem</a></li>
        </ul>
        <div class="mt-auto ms-2 mb-2 small text-body-tertiary">© <?=date('Y')?> FlowDesk</div>
      </div>
    </nav>

    <!-- Conteúdo principal -->
    <div class="col ps-md-4 pt-2 bg-dash">
      <!-- Topbar -->
      <nav class="navbar navbar-expand navbar-light bg-dash border-bottom">
        <div class="container-fluid px-0">
          <span class="navbar-brand d-none d-md-block" id="label-page">
            <?=["home" => "Dashboard", "financeiro"=>"Financeiro","clientes"=>"Clientes","jobs"=>"Jobs","hospedagem"=>"Hospedagem"][$mod] ?? "Dashboard"?>
          </span>
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                <img src="/flowdesk/assets/images/profile.png" class="rounded-circle me-2" width="33" height="33">
                <span class="d-none d-md-inline"><?php echo htmlspecialchars($user_name); ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#" onclick="loadContent('configuracoes')">Configurações</a></li>
                <li><a class="dropdown-item" href="/flowdesk/logout.php">Sair</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>
      <!-- Conteúdo dinâmico aqui -->
      <main id="dashboardMain" class="py-4">
        <?php
          if ($mod === 'financeiro') {
              include __DIR__.'/contents/dash_financeiro.php';
          } else if ($mod === 'clientes') {
              include __DIR__.'/contents/dash_clientes.php';
          } else if ($mod === 'jobs') {
              include __DIR__.'/contents/dash_jobs.php';
          } else if ($mod === 'hospedagem') {
              include __DIR__.'/contents/dash_hospedagem.php';
          } else {
              include __DIR__.'/contents/dash_home.php';
          }
        ?>
      </main>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script>
function loadContent(mod) {
  fetch('/flowdesk/modules/contents/dash_' + mod + '.php')
    .then(r => r.text())
    .then(html => {
      document.getElementById('dashboardMain').innerHTML = html;
      document.getElementById('label-page').textContent =
        {home: 'Dashboard', financeiro: 'Financeiro', clientes: 'Clientes', jobs: 'Jobs', hospedagem: 'Hospedagem', configuracoes: 'Configurações'}[mod] || 'Dashboard';
      history.replaceState({}, '', '?mod=' + encodeURIComponent(mod));
    });
}
document.querySelectorAll('#sidebarMenu .nav-link').forEach(btn => {
  btn.addEventListener('click', function(e){
    e.preventDefault();
    document.querySelector('#sidebarMenu .active').classList.remove('active');
    this.classList.add('active');
    loadContent(this.dataset.content);
  });
});
</script>
<?php include __DIR__.'/../includes/footer.php'; ?>
