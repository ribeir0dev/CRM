<?php
// modules/modals/modal_bloco_cliente.php
if (session_status() !== PHP_SESSION_ACTIVE)
  session_start();
require_once __DIR__ . '/../../inc/conf/db.php';

$cliente_id = (int) ($_GET['id'] ?? 0); // ou outra forma de obter o ID do cliente atual
?>
<div class="modal fade" id="modalBlocoCliente" tabindex="-1" aria-labelledby="modalBlocoClienteLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="/actions/salvar_bloco_cliente.php" id="form-bloco-cliente">
        <input type="hidden" name="cliente_id" id="blocoClienteId" value="<?= $cliente_id ?>">
        <input type="hidden" name="slug" id="blocoSlug" value="">

        <div class="modal-header">
          <h5 class="modal-title" id="modalBlocoClienteLabel">Editar bloco</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small">Título</label>
            <input type="text" class="form-control" name="titulo" id="blocoTitulo" required>
          </div>

          <!-- Campos genéricos escondidos por padrão -->
          <div id="campoUrl" class="mb-3 d-none">
            <label class="form-label small">URL</label>
            <input type="url" class="form-control" name="url" id="blocoUrl" placeholder="https://exemplo.com">
          </div>

          <div id="campoUsuario" class="mb-3 d-none">
            <label class="form-label small">Usuário</label>
            <input type="text" class="form-control" name="usuario" id="blocoUsuario" placeholder="Usuário de acesso">
          </div>

          <div id="campoSenha" class="mb-3 d-none">
            <label class="form-label small">Senha</label>
            <input type="text" class="form-control" name="senha" id="blocoSenha" placeholder="Senha de acesso">
          </div>

          <!-- Campo texto livre opcional para outros blocos -->
          <div id="campoConteudoLivre" class="mb-3 d-none">
            <label class="form-label small">Conteúdo</label>
            <textarea class="form-control" name="conteudo_livre" id="blocoConteudoLivre" rows="5"
              placeholder="Informações adicionais"></textarea>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="blocoCompartilhado" name="compartilhado">
            <label class="form-check-label small" for="blocoCompartilhado">
              Compartilhar este bloco com o cliente no relatório público
            </label>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar bloco</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
 document.addEventListener('DOMContentLoaded', function () {
  var modal = document.getElementById('modalBlocoCliente');
  if (!modal) return;

  modal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    if (!button) return;

    var slug   = button.getAttribute('data-slug');
    var titulo = button.getAttribute('data-titulo') || '';

    document.getElementById('blocoSlug').value   = slug;
    document.getElementById('blocoTitulo').value = titulo;

    // limpa campos
    document.getElementById('blocoUrl').value          = '';
    document.getElementById('blocoUsuario').value      = '';
    document.getElementById('blocoSenha').value        = '';
    document.getElementById('blocoConteudoLivre').value= '';
    document.getElementById('blocoCompartilhado').checked = false;

    // esconde tudo
    ['campoUrl','campoUsuario','campoSenha','campoConteudoLivre'].forEach(function(id){
      document.getElementById(id).classList.add('d-none');
    });

    // decide quais campos mostrar por slug
    if (slug === 'website') {
      document.getElementById('campoUrl').classList.remove('d-none');
    } else if (slug === 'hospedagem' || slug === 'acesso_site' || slug === 'registro_br') {
      document.getElementById('campoUrl').classList.remove('d-none');
      document.getElementById('campoUsuario').classList.remove('d-none');
      document.getElementById('campoSenha').classList.remove('d-none');
    } else {
      document.getElementById('campoConteudoLivre').classList.remove('d-none');
    }

    // Carrega dados atuais via AJAX
    fetch('/actions/carregar_bloco_cliente.php?cliente_id=<?= $cliente_id ?>&slug=' + encodeURIComponent(slug))
      .then(function (r) { return r.ok ? r.json() : null; })
      .then(function (data) {
        if (!data) return;

        if (data.titulo) document.getElementById('blocoTitulo').value = data.titulo;
        if (data.compartilhado === '1' || data.compartilhado === 1) {
          document.getElementById('blocoCompartilhado').checked = true;
        }

        if (data.conteudo) {
          try {
            var c = JSON.parse(data.conteudo);
            if (c.url)      document.getElementById('blocoUrl').value = c.url;
            if (c.usuario)  document.getElementById('blocoUsuario').value = c.usuario;
            if (c.senha)    document.getElementById('blocoSenha').value = c.senha;
            if (c.livre)    document.getElementById('blocoConteudoLivre').value = c.livre;
          } catch (e) {
            // se não for JSON, joga no campo livre
            document.getElementById('campoConteudoLivre').classList.remove('d-none');
            document.getElementById('blocoConteudoLivre').value = data.conteudo;
          }
        }
      })
      .catch(function () {});
  });
});

</script>