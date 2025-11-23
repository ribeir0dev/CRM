<?php
$pageTitle = "Login | FlowDesk CRM";
include __DIR__.'/includes/header.php';
require_once __DIR__.'/includes/csrf.php';
?>

<div class="login-card">
    <h1>Olá, bem vindo de volta!</h1>
    <p class="sub-text">
        Logue com seu usuário ou e-mail e senha.
    </p>
    <form action="login.php" method="post" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <div class="form-group">
            <label for="user_or_email">Usuário ou Email</label>
            <input type="text" required name="user_or_email" id="user_or_email" placeholder="Digite seu usuário ou email">
        </div>
        <div class="form-group pw-group">
            <label for="password">Senha</label>
            <input type="password" required name="password" id="password" placeholder="Digite sua senha">
            <span class="pw-toggle" onclick="togglePassword()">👁️</span>
        </div>
        <div class="form-row">
            <label><input type="checkbox" name="remember"> Lembrar desse dispositivo</label>
            <a href="reset_password.php" class="forgot-link">Esqueci minha senha</a>
        </div>
        <button type="submit" class="login-btn">Entrar</button>
    </form>
    <div class="footer-link">
        Não possui uma conta? <a href="register.php">Criar nova conta</a>
    </div>
</div>
<script>
function togglePassword() {
    var pw = document.getElementById('password');
    pw.type = pw.type === 'password' ? 'text' : 'password';
}
</script>

<?php
include __DIR__.'/includes/footer.php';
?>
