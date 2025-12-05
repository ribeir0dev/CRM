<?php
$pageTitle = "Dashboard | FlowDesk";
session_start();
include __DIR__ . '/../inc/headers/painel.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit;
}
$user_name = $_SESSION['user_nome'] ?? 'Usuário';
$mod = $_GET['mod'] ?? 'dashboard';
?>


<div class="container-fluid painel-pai">
  <div class="row flex-nowrap">
    <nav class="col-auto col-lg-2 px-sm-2 px-0 sidebar min-vh-100 d-flex flex-column justify-content-between"
      id="sidebar">
      <div>
        <div class="d-flex align-items-center justify-content-center py-4">
          <img src="/assets/img/icon.png" alt="Logo" width="40" class="me-2" />
          <span class="text-bg-d fs-4">FlowDesk</span>
        </div>
        <div class="text-center text-light mb-3 fs-6 d-none d-lg-block">Sistema de Gerenciamento CRM</div>
        <ul class="nav nav-pills flex-column mb-auto">
          <li class="nav-item">
            <a href="painel.php?mod=dashboard"
              class="nav-link d-flex align-items-center text-light <?= ($mod === 'dashboard' ? 'active' : '') ?>">
              <i class="lni lni-dashboard-square-1 me-2"></i>Dashboard
            </a>
          </li>
          <li>
            <a href="painel.php?mod=clientes"
              class="nav-link d-flex align-items-center text-light <?= ($mod === 'clientes' ? 'active' : '') ?>">
              <i class="lni lni-user-multiple-4 me-2"></i>Clientes
            </a>
          </li>
          <li>
            <a href="painel.php?mod=projetos"
              class="nav-link d-flex align-items-center text-light <?= ($mod === 'projetos' ? 'active' : '') ?>">
              <i class="lni lni-layout-9 me-2"></i>Projetos
            </a>
          </li>
          <li>
            <a href="painel.php?mod=financeiro"
              class="nav-link d-flex align-items-center text-light <?= ($mod === 'financeiro' ? 'active' : '') ?>">
              <i class="lni lni-credit-card-multiple me-2"></i>Financeiro
            </a>
          </li>
          <li>
            <a href="painel.php?mod=hospedagens"
              class="nav-link d-flex align-items-center text-light <?= ($mod === 'hospedagens' ? 'active' : '') ?>">
              <i class="lni lni-cloud-2 me-2"></i>Hospedagens
            </a>
          </li>
        </ul>

      </div>
      <div class="sidebar-footer text-center text-light p-3 small">
        <div>&copy; 2025 FlowDesk</div>
        <div><a href="#" class="text-light text-decoration-underline fs-10">Termos de uso</a> • <a href="#"
            class="text-light text-decoration-underline fs-10">Política de privacidade</a></div>
      </div>
    </nav>

    <?php
    // Definições de módulo e avatar
    $user_name = $_SESSION['user_nome'] ?? 'Usuário';
    $user_avatar = $_SESSION['user_avatar'] ?? '/assets/img/avatar.png';

    $mod = $_GET['mod'] ?? 'dashboard';
    $module_titles = [
      'dashboard' => 'Dashboard',
      'clientes' => 'Clientes',
      'projetos' => 'Projetos',
      'financeiro' => 'Financeiro',
      'hospedagens' => 'Hospedagens',
    ];
    $mod_name = $module_titles[$mod] ?? ucfirst($mod);
    ?>

    <!-- Conteúdo principal -->
    <main class="col py-3 px-4 bg-light" id="painel_content">
      <!-- Menu Mobile Toggle -->
      <div class="d-lg-none" id="mobileTopbar">
        <div class="d-flex align-items-center justify-content-between px-3 py-2">
          <div class="d-flex align-items-center" id="mobileBrand">
            <img src="/assets/img/icon.png" alt="Logo" width="32" class="me-2" />
            <span class="fw-bold text-light">Flow Desk</span>
          </div>
          <button class="btn btn-light" id="menuToggle" aria-label="Abrir menu" style="box-shadow: none;">
            <i class="bi bi-list" style="font-size: 1rem; color: #fff;"></i>
          </button>
        </div>
      </div>
      <!-- Topbar do conteúdo -->
      <div class="d-flex align-items-center justify-content-between gap-3 pb-3 topbar-conteudo">
        <h4 class="m-0 fw-bold text-bg-d"><?= htmlspecialchars($mod_name) ?></h4>
        <div class="dropdown">
          <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown"
            data-bs-toggle="dropdown" aria-expanded="false">
            <img src="<?= htmlspecialchars($user_avatar) ?>" alt="Avatar" class="rounded-circle me-2" width="36"
              height="36" style="object-fit:cover;">
            <span class="usuario"><?= htmlspecialchars($user_name) ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li>
              <a class="dropdown-item" href="/modules/configuracoes.php">
                <i class="bi bi-gear me-2"></i>Configurações
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a href="/actions/logout.php" class="dropdown-item">
                <i class="bi bi-box-arrow-right me-2"></i>Sair
              </a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Área de conteúdo dinâmico -->
      <div class="p-3">
        <?php
        $mod_file = __DIR__ . '/content/' . $mod . '.php';
        if (file_exists($mod_file)) {
          include $mod_file;
        } else {
          echo '<p class="text-muted">Selecione um módulo para iniciar.</p>';
        }
        ?>
      </div>
    </main>
  </div> <!-- row -->
</div> <!-- container -->


<?php
include __DIR__ . '/../inc/footers/footer.php';
?>