<?php
$pageTitle = "Recuperar Senha | FlowDesk CRM";
include __DIR__.'/includes/header.php';
require_once __DIR__.'/includes/csrf.php';
?>
<div class="login-card">
    <h1>Recuperar senha</h1>
    <p class="sub-text">Informe seu e-mail cadastrado para redefinir sua senha.</p>
    <form action="reset_password.php" method="post" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" required name="email" id="email" placeholder="Digite seu e-mail">
        </div>
        <button type="submit" class="login-btn">Enviar link de recuperação</button>
    </form>
    <div class="footer-link">
        Lembrou da senha? <a href="index.php">Entrar</a>
    </div>
</div>
<?php
include __DIR__.'/includes/footer.php';
?>
