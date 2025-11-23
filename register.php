<?php
$pageTitle = "Criar Conta | FlowDesk CRM";
include __DIR__.'/includes/header.php';
require_once __DIR__.'/includes/csrf.php';
require_once __DIR__.'/includes/db.php';

$success = false;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'])) {
        $message = "Erro de segurança, tente novamente.";
    } else {
        $nome = trim($_POST['nome'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if (!$nome || !$username || !$email || strlen($password) < 8) {
            $message = "Preencha todos os campos e use uma senha de pelo menos 8 caracteres.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            try {
                $stmt = $pdo->prepare('INSERT INTO users (nome, username, email, senha_hash) VALUES (?, ?, ?, ?)');
                $stmt->execute([$nome, $username, $email, $password_hash]);
                
                // Envia o e-mail
                $to = $email;
                $subject = "Conta criada com sucesso - FlowDesk CRM";
                $msg = "Olá, $nome! Sua conta no FlowDesk CRM foi criada com sucesso!\nAgora você pode acessar usando seu usuário ou e-mail e a senha cadastrada.\nAcesse: http://localhost/flowdesk/index.php";
                $headers = "From: suporte@seudominio.com\r\nReply-To: suporte@seudominio.com\r\nContent-Type: text/plain; charset=UTF-8";
                mail($to, $subject, $msg, $headers);

                header('Location: register_success.php');
                exit;
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = "E-mail ou usuário já cadastrado.";
                } else {
                    $message = "Erro ao criar conta, tente novamente.";
                }
            }
        }
    }
}
?>
<div class="login-card">
    <h1>Crie sua conta</h1>
    <p class="sub-text">Preencha os dados abaixo para acessar o seu painel.</p>
    <?php if ($message): ?>
        <div style="color:red;margin-bottom:8px;"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form action="register.php" method="post" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <div class="form-group">
            <label for="nome">Nome Completo</label>
            <input type="text" required name="nome" id="nome" placeholder="Digite seu nome completo">
        </div>
        <div class="form-group">
            <label for="username">Usuário</label>
            <input type="text" required name="username" id="username" placeholder="Escolha um nome de usuário">
        </div>
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" required name="email" id="email" placeholder="Digite seu e-mail">
        </div>
        <div class="form-group pw-group">
            <label for="password">Senha</label>
            <input type="password" required name="password" id="password" placeholder="Crie uma senha">
            <span class="pw-toggle" onclick="togglePassword()">👁️</span>
        </div>
        <button type="submit" class="login-btn">Criar conta</button>
    </form>
    <div class="footer-link">
        Já tem uma conta? <a href="index.php">Entrar</a>
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
