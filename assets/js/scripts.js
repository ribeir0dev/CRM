// Troca entre Login / Criar Conta
const btnLogin = document.getElementById('btn-login');
const btnCriar = document.getElementById('btn-criar');
const formLogin = document.getElementById('form-login');
const formCriar = document.getElementById('form-criar');

if (btnLogin && btnCriar && formLogin && formCriar) {
  btnLogin.onclick = function () {
    this.classList.add('active');
    btnCriar.classList.remove('active');
    formLogin.style.display = '';
    formCriar.style.display = 'none';
  };
  btnCriar.onclick = function () {
    this.classList.add('active');
    btnLogin.classList.remove('active');
    formLogin.style.display = 'none';
    formCriar.style.display = '';
  };
}

// AJAX criar conta
if (formCriar) {
  formCriar.onsubmit = async function (e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    let caixa = document.getElementById('msg-criar-conta');

    caixa.innerHTML = '<div class="alert alert-info">Processando, aguarde...</div>';

    try {
      let res = await fetch('actions/criar_conta.php', { method: 'POST', body: formData });
      let data = await res.json();

      if (data.success) {
        caixa.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
        form.reset();
      } else {
        let erros = (data.errors || ['Erro desconhecido.']).map(e => `<li>${e}</li>`).join('');
        caixa.innerHTML = `<div class="alert alert-danger"><ul>${erros}</ul></div>`;
      }
    } catch (erro) {
      caixa.innerHTML = '<div class="alert alert-danger">Erro ao conectar ao servidor!</div>';
    }
  };
}


// JS Menu
const menuBtn = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
const mobileBrand = document.getElementById('mobileBrand');

if (menuBtn && sidebar) {
  menuBtn.addEventListener('click', function () {
    sidebar.classList.toggle('show');
    
  });

  // Opcional: fecha menu ao clicar fora no mobile
  document.addEventListener('click', function (e) {
    if (
      window.innerWidth <= 991 &&
      sidebar.classList.contains('show') &&
      !sidebar.contains(e.target) &&
      e.target !== menuBtn &&
      !menuBtn.contains(e.target)
    ) {
      sidebar.classList.remove('show');

    }
  });
}


document.addEventListener('DOMContentLoaded', function () {
  const valorInputs = document.querySelectorAll('input.js-money');

  function formatMoney(input) {
    let v = input.value.replace(/\D/g, '');
    if (!v) {
      input.value = '';
      return;
    }
    v = (parseInt(v, 10) / 100).toFixed(2) + '';
    v = v.replace('.', ',');
    v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    input.value = v;
  }

  valorInputs.forEach(inp => {
    inp.addEventListener('input', function () {
      formatMoney(this);
    });
    inp.addEventListener('blur', function () {
      formatMoney(this);
    });
  });
});


document.addEventListener('DOMContentLoaded', function () {
  const telInputs = document.querySelectorAll('.js-telefone');

  function maskPhone(value) {
    value = value.replace(/\D/g, ''); // só números

    if (value.length > 11) value = value.slice(0, 11);

    // (99) 99999-9999 ou (99) 9999-9999
    if (value.length > 10) {            // 11 dígitos
      value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
    } else if (value.length > 6) {      // até 10 dígitos
      value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
    } else if (value.length > 2) {
      value = value.replace(/^(\d{2})(\d{0,5}).*/, '($1) $2');
    } else if (value.length > 0) {
      value = value.replace(/^(\d{0,2})/, '($1');
    }
    return value;
  }

  telInputs.forEach(function (input) {
    input.addEventListener('input', function () {
      this.value = maskPhone(this.value);
    });

    input.addEventListener('blur', function () {
      this.value = maskPhone(this.value);
    });
  });
});




document.addEventListener('DOMContentLoaded', function () {
  function formatBR(dateStr) {
    if (!dateStr) return '—/—/----';
    const [y, m, d] = dateStr.split('-');
    return `${d}/${m}/${y}`;
  }

  const inicioInput = document.getElementById('dataInicioProjeto');
  const entregaInput = document.getElementById('dataEntregaProjeto');

  const inicioLabel = document.getElementById('labelDataInicioProjeto');
  const entregaLabel = document.getElementById('labelDataEntregaProjeto');

  if (inicioInput) {
    inicioInput.addEventListener('change', function () {
      inicioLabel.textContent = formatBR(this.value);
    });
  }
  if (entregaInput) {
    entregaInput.addEventListener('change', function () {
      entregaLabel.textContent = formatBR(this.value);
    });
  }
});

document.addEventListener('DOMContentLoaded', function () {
  var colEdit  = document.getElementById('editTarefaColunaSelect');
  var colEditH = document.getElementById('editTarefaColuna');
  if (colEdit && colEditH) {
    colEdit.addEventListener('change', function () {
      colEditH.value = this.value;
    });
  }

  var modalEditar = document.getElementById('modalEditarTarefa');
  if (modalEditar) {
    modalEditar.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      if (!button) return;

      var tarefaId   = button.getAttribute('data-id');
      var titulo     = button.getAttribute('data-titulo') || '';
      var descricao  = button.getAttribute('data-descricao') || '';
      var coluna     = button.getAttribute('data-coluna') || 'backlog';

      document.getElementById('editTarefaId').value        = tarefaId;
      document.getElementById('editTarefaTitulo').value    = titulo;
      document.getElementById('editTarefaDescricao').value = descricao;
      colEdit.value  = coluna;
      colEditH.value = coluna;
    });
  }
});


let kanbanDragId = null;

function kanbanDrag(ev) {
  kanbanDragId = ev.target.getAttribute('data-id');
  ev.dataTransfer.effectAllowed = 'move';
}

function kanbanAllowDrop(ev) {
  ev.preventDefault();
  ev.dataTransfer.dropEffect = 'move';
}

function kanbanDrop(ev) {
  ev.preventDefault();
  const colunaEl = ev.currentTarget; // card-body da coluna
  const coluna   = colunaEl.getAttribute('data-coluna');
  if (!kanbanDragId || !coluna) return;

  // move visualmente
  const item = document.querySelector('.kanban-item[data-id="' + kanbanDragId + '"]');
  if (item && colunaEl) {
    colunaEl.appendChild(item);
  }

  // atualiza no servidor via AJAX
  fetch('/actions/mover_tarefa.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
    body: new URLSearchParams({
      tarefa_id: kanbanDragId,
      coluna: coluna
    })
  }).then(function (r) {
    // opcional: tratar erro, recarregar, etc.
  }).catch(function () {
    // em caso de erro, você pode recarregar a página
  });

  kanbanDragId = null;
}


