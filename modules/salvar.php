<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/db.php';

// Função reutilizável para upload (limite 2 arquivos, aceitando pdf ou imagem)
function upload_comprovantes($files, $prefix = 'comp') {
    $paths = [];
    $upload_dir = __DIR__ . '/../../uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    for ($i=1; $i<=2; $i++) {
        if (!empty($files["comprovante$i"]['name'])) {
            $ext = strtolower(pathinfo($files["comprovante$i"]['name'], PATHINFO_EXTENSION));
            $valid = in_array($ext, ['pdf','png','jpg','jpeg','gif','webp']);
            if ($valid) {
                $unique = uniqid($prefix.$i.'_').'.'.$ext;
                move_uploaded_file($files["comprovante$i"]['tmp_name'], $upload_dir . $unique);
                $paths[] = $unique;
            } else {
                $paths[] = null;
            }
        } else {
            $paths[] = null;
        }
    }
    return $paths;
}

// ENTRADA
if (isset($_POST['cliente_id']) && isset($_POST['valor_receber'])) {
    $cliente_id = $_POST['cliente_id'];
    $tipo_servico_id = $_POST['tipo_servico_id'];
    $data = $_POST['data'];
    $tipo_venda = $_POST['tipo_venda'];
    $tipo_pagamento = $_POST['tipo_pagamento'];
    $valor_receber = $_POST['valor_receber'];
    $valor_recebido = $_POST['valor_recebido'];
    list($comp1, $comp2) = upload_comprovantes($_FILES);

    $sql = "INSERT INTO financeiro_entradas (cliente_id, tipo_servico_id, data, tipo_venda, tipo_pagamento, valor_receber, valor_recebido, comprovante1, comprovante2)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([
        $cliente_id, $tipo_servico_id, $data, $tipo_venda, $tipo_pagamento, $valor_receber, $valor_recebido, $comp1, $comp2
    ]);
    if ($ok) {
        header("Location: dashboard.php?mod=financeiro&msg=entrada_sucesso");
        exit;
    } else {
        die("Erro ao salvar entrada.");
    }
}

// SAÍDA
if (isset($_POST['descricao']) && isset($_POST['valor'])) {
    $descricao = $_POST['descricao'];
    $tipo_saida = $_POST['tipo_saida'];
    $tipo_pagamento = $_POST['tipo_pagamento'];
    $data = $_POST['data'];
    $valor = $_POST['valor'];
    $sql = "INSERT INTO financeiro_saidas (descricao, tipo_saida, tipo_pagamento, data, valor)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([
        $descricao, $tipo_saida, $tipo_pagamento, $data, $valor
    ]);
    if ($ok) {
        header("Location: dashboard.php?mod=financeiro&msg=saida_sucesso");
        exit;
    } else {
        die("Erro ao salvar saída.");
    }
}

// CADASTRO DE NOVO CLIENTE
if (isset($_POST['tipo']) && $_POST['tipo'] == 'cliente' && isset($_POST['nome'])) {
    $nome = trim($_POST['nome']);
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $sql = "INSERT INTO clientes (nome, telefone, email) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([$nome, $telefone, $email]);

    if ($ok) {
        header("Location: dashboard.php?mod=clientes&msg=sucesso");
        exit;
    } else {
        die("Erro ao salvar cliente.");
    }
}


// Se não for nenhum caso acima:
header("Location: dashboard.php?mod=financeiro&msg=erro_salidadao");
exit;
?>
