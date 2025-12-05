<?php
    $pageTitle = "Login | FlowDesk";
    include __DIR__.'/inc/headers/login.php';
    require_once __DIR__.'/inc/conf/csrf.php';
    require_once __DIR__.'/inc/conf/db.php';


session_start();

// Se já estiver logado, manda direto para o painel
if (isset($_SESSION['user_id'])) {
    header('Location: /flowdesk_novo/modules/painel.php');
    exit;
}

$pageTitle = "Login | FlowDesk";
include __DIR__ . '/inc/headers/login.php';
?>
<div class="login-container">
    <div class="form-side">
        <div class="mb-4 text-center">
            <img src="assets/img/icon.png" width="64" alt="FlowDesk Logo" class="mb-3" />
            <h5 class="mb-1">Bem-vindo(a) de volta, criativo!</h5>
            <p class="text-secondary small mb-4">Estamos felizes em ver você novamente.</p>
        </div>
        <div class="super-tabs mb-3">
            <button class="super-tab active" type="button" id="btn-login">Logar</button>
            <button class="super-tab" type="button" id="btn-criar">Criar Conta</button>
        </div>
<form id="form-login" method="POST" action="actions/login.php" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

    <div class="mb-3 input-group">
        <input type="text" class="form-control" placeholder="Seu usuário ou email" name="user_or_email" required>
        <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
    </div>
    <div class="mb-2 input-group">
        <input type="password" class="form-control" placeholder="Senha" name="password" required>
        <span class="input-group-text bg-white"><i class="bi bi-shield-lock"></i></span>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="rememberMe" name="remember" checked>
            <label class="form-check-label small" for="rememberMe">Lembrar este dispositivo.</label>
        </div>
        <a href="#" class="small text-primary text-decoration-none">Esqueci minha senha</a>
    </div>
    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill mb-3">Entrar</button>
</form>


<div id="msg-login"></div>

       <form id="form-criar" autocomplete="off" style="display:none">
    <div class="mb-3 input-group">
        <input type="text" class="form-control" placeholder="Usuário" name="nome" required>
        <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
    </div>
    <div class="mb-3 input-group">
        <input type="email" class="form-control" placeholder="Email" name="email" required>
        <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
    </div>
    <div class="mb-2 input-group">
        <input type="password" class="form-control" placeholder="Senha" name="senha" required>
        <span class="input-group-text bg-white"><i class="bi bi-shield-lock"></i></span>
    </div>
    <div class="mb-2 input-group">
        <input type="password" class="form-control" placeholder="Confirmar senha" name="conf_senha" required>
        <span class="input-group-text bg-white"><i class="bi bi-shield-lock"></i></span>
    </div>
    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill mb-3">Criar Conta</button>
</form>
<div id="msg-criar-conta"></div>

        <div class="text-center my-3 text-muted">OU</div>
        <button class="btn btn-google btn-social w-100"><i class="bi bi-google"></i> Entrar com Google</button>
    </div>
    <div class="art-side">
        <div class="art-caption">
            © 2025 FlowDesk. Todos os direitos reservados. É proibida a utilização ou reprodução não autorizada de qualquer conteúdo ou informação desta plataforma. Para mais informações, consulte os nossos Termos de Serviço e Política de Privacidade.
        </div>
    </div>
</div>

<?php
        include __DIR__.'/inc/footers/footer.php';
?>