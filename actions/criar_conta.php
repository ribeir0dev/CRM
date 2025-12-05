<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'../../inc/conf/csrf.php';
require_once __DIR__.'../../inc/conf/db.php';

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $conf_senha = $_POST['conf_senha'] ?? '';

    if (!$nome) $erros[] = "Informe o nome.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = "Email inválido.";
    if (strlen($senha) < 8) $erros[] = "Senha deve ter pelo menos 8 caracteres.";
    if ($senha !== $conf_senha) $erros[] = "As senhas não conferem.";

    $stmt = $pdo->prepare('SELECT 1 FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) $erros[] = "E-mail já cadastrado.";

    if (!$erros) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');
        if ($stmt->execute([$nome, $email, $hash])) {
            echo json_encode([
                "success" => true,
                "message" => "Conta criada com sucesso! <a href='login.php'>Entrar</a>"
            ]);
            exit;
        } else {
            $erros[] = "Erro ao salvar usuário.";
        }
    }
    echo json_encode([
        "success" => false,
        "errors" => $erros
    ]);
    exit;
}
echo json_encode([
    "success" => false,
    "errors" => ["Requisição inválida."]
]);
exit;
?>
