<?php
if (session_status() !== PHP_SESSION_ACTIVE)
  session_start();
require_once __DIR__ . '/../../inc/conf/db.php';

$token = $_GET['token'] ?? '';
if ($token === '') {
  http_response_code(404);
  exit('Relatório não encontrado.');
}

// carrega cliente com dados básicos
$stmt = $pdo->prepare("
    SELECT id, nome, whatsapp, email, foto_perfil
    FROM clientes
    WHERE token_publico = ?
");
$stmt->execute([$token]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
  http_response_code(404);
  exit('Relatório não encontrado.');
}

// separa primeiro e último nome
$partesNome = preg_split('/\s+/', trim($cliente['nome']));
$primeiroNome = $partesNome[0] ?? '';
$ultimoNome = $partesNome[count($partesNome) - 1] ?? '';

// busca blocos compartilhados
$stmt = $pdo->prepare("
    SELECT slug, titulo, conteudo
    FROM cliente_blocos
    WHERE cliente_id = ? AND compartilhado = 1
    ORDER BY titulo
");
$stmt->execute([$cliente['id']]);
$blocos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Relatório de <?= htmlspecialchars($primeiroNome) ?></title>
  <link rel="stylesheet" href="/assets/css/login.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container py-5">

    <!-- Card central: mini perfil -->
    <div class="row justify-content-center mb-4">
      <div class="col-md-6">
        <div class="card shadow-sm text-center">
          <div class="card-body">

            <!-- 1ª linha: Foto -->
            <?php if (!empty($cliente['foto_perfil'])): ?>
              <img src="<?= htmlspecialchars($cliente['foto_perfil']) ?>" alt="Foto do cliente"
                class="rounded-circle mb-3" style="width: 96px; height: 96px; object-fit: cover;">
            <?php else: ?>
              <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mb-3"
                style="width: 96px; height: 96px; margin: 0 auto; font-size: 2rem;">
                <?= strtoupper(substr($primeiroNome, 0, 1)) ?>
              </div>
            <?php endif; ?>

            <!-- 2ª linha: Primeiro / Último nome -->
            <h5 class="mb-2">
              <?= htmlspecialchars($primeiroNome . ' ' . $ultimoNome) ?>
            </h5>

            <!-- 3ª linha: WhatsApp -->
            <p class="mb-1 small text-muted">
              WhatsApp:
              <?php if (!empty($cliente['whatsapp'])): ?>
                <?= htmlspecialchars($cliente['whatsapp']) ?>
              <?php else: ?>
                <span class="text-muted">Não informado</span>
              <?php endif; ?>
            </p>

            <!-- 4ª linha: E-mail -->
            <p class="mb-0 small text-muted">
              E-mail:
              <?php if (!empty($cliente['email'])): ?>
                <a href="mailto:<?= htmlspecialchars($cliente['email']) ?>">
                  <?= htmlspecialchars($cliente['email']) ?>
                </a>
              <?php else: ?>
                <span class="text-muted">Não informado</span>
              <?php endif; ?>
            </p>

          </div>
        </div>
      </div>
    </div>

    <!-- Cards dos blocos compartilhados (usando JSON de conteudo) -->
    <div class="row g-3">
      <?php if (empty($blocos)): ?>
        <div class="col-12 text-center">
          <p class="text-muted small mb-0">Nenhuma informação compartilhada neste relatório.</p>
        </div>
      <?php else: ?>
        <?php foreach ($blocos as $b): ?>
          <?php $c = json_decode($b['conteudo'] ?? '{}', true) ?: []; ?>
          <div class="col-12 col-md-6">
            <div class="card shadow-sm h-100">
              <div class="card-body">
                <h6 class="mb-1">
                  <?= htmlspecialchars($b['titulo']) ?>
                </h6>
                <hr class="my-2">

                <div class="small">
                  <?php if ($b['slug'] === 'website'): ?>
                    <p class="mb-1">
                      <?php if (!empty($c['url'])): ?>
                        <a href="<?= htmlspecialchars($c['url']) ?>" target="_blank">
                          <?= htmlspecialchars($c['url']) ?>
                        </a>
                      <?php else: ?>
                        <span class="text-muted">Não informado</span>
                      <?php endif; ?>
                    </p>

                  <?php elseif (in_array($b['slug'], ['hospedagem', 'acesso_site', 'registro_br'], true)): ?>
                    <p class="mb-1">URL: <a href="<?= htmlspecialchars($c['url']) ?>" target="_blank">
                        <?= htmlspecialchars($c['url']) ?>
                      </a></p>
                    <p class="mb-1">Usuário: <?= htmlspecialchars($c['usuario'] ?? '—') ?></p>
                    <p class="mb-0">Senha: <?= htmlspecialchars($c['senha'] ?? '—') ?></p>

                  <?php else: ?>
                    <p class="mb-0">
                      <?= nl2br(htmlspecialchars($c['livre'] ?? '')) ?>
                    </p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>
</body>

</html>