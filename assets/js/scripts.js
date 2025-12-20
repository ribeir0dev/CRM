// ========================================
// LOGIN / CRIAR CONTA (TABS)
// ========================================
document.addEventListener('DOMContentLoaded', () => {
  const btnLogin = document.getElementById('btn-login');
  const btnCriar = document.getElementById('btn-criar');
  const formLogin = document.getElementById('form-login');
  const formCriar = document.getElementById('form-criar');

  // Alterna visualmente entre Login e Criar Conta
  function ativarLogin() {
    btnLogin?.classList.add('active');
    btnCriar?.classList.remove('active');
    if (formLogin) formLogin.style.display = '';
    if (formCriar) formCriar.style.display = 'none';
  }

  function ativarCriar() {
    btnCriar?.classList.add('active');
    btnLogin?.classList.remove('active');
    if (formLogin) formLogin.style.display = 'none';
    if (formCriar) formCriar.style.display = '';
  }

  if (btnLogin && btnCriar && formLogin && formCriar) {
    btnLogin.addEventListener('click', ativarLogin);
    btnCriar.addEventListener('click', ativarCriar);
  }

  // ======================================
  // AJAX: CRIAR CONTA
  // ======================================
  if (formCriar) {
    formCriar.addEventListener('submit', async (e) => {
      e.preventDefault();

      const form = e.target;
      const formData = new FormData(form);
      const caixa = document.getElementById('msg-criar-conta');
      if (!caixa) return;

      caixa.innerHTML = '<div class="alert alert-info">Processando, aguarde...</div>';

      try {
        const res = await fetch('app/Controllers/AuthController.php?acao=register', {
          method: 'POST',
          body: formData,
        });
        const data = await res.json();

        if (data.success) {
          caixa.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
          form.reset();
          ativarLogin(); // opcional: volta para aba login após cadastro
        } else {
          const erros = (data.errors || ['Erro desconhecido.'])
            .map((err) => `<li>${err}</li>`)
            .join('');
          caixa.innerHTML = `<div class="alert alert-danger"><ul>${erros}</ul></div>`;
        }
      } catch {
        caixa.innerHTML = '<div class="alert alert-danger">Erro ao conectar ao servidor!</div>';
      }
    });
  }

  // ======================================
  // MENU MOBILE (SIDEBAR)
  // ======================================
  const menuBtn = document.getElementById('menuToggle');
  const sidebar = document.querySelector('.sv-sidebar');

  if (menuBtn && sidebar) {
    // Abre/fecha sidebar
    menuBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });

    // Fecha ao clicar fora (apenas em telas <= 991px)
    document.addEventListener('click', (e) => {
      const clicouForaSidebar = !sidebar.contains(e.target);
      const clicouForaBotao = e.target !== menuBtn && !menuBtn.contains(e.target);

      if (
        window.innerWidth <= 991 &&
        sidebar.classList.contains('show') &&
        clicouForaSidebar &&
        clicouForaBotao
      ) {
        sidebar.classList.remove('show');
      }
    });
  }

  // ======================================
  // FORMATADOR DE MOEDA (inputs .js-money)
  // ======================================
  const valorInputs = document.querySelectorAll('input.js-money');

  function formatMoney(input) {
    let v = input.value.replace(/\D/g, '');
    if (!v) {
      input.value = '';
      return;
    }
    v = (parseInt(v, 10) / 100).toFixed(2);
    v = v.replace('.', ',');
    v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    input.value = v;
  }

  valorInputs.forEach((inp) => {
    ['input', 'blur'].forEach((evt) => {
      inp.addEventListener(evt, () => formatMoney(inp));
    });
  });

  // ======================================
  // MÁSCARA TELEFONE (inputs .js-telefone)
  // ======================================
  const telInputs = document.querySelectorAll('.js-telefone');

  function maskPhone(value) {
    value = value.replace(/\D/g, '');
    if (value.length > 11) value = value.slice(0, 11);

    if (value.length > 10) {
      value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
    } else if (value.length > 6) {
      value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
    } else if (value.length > 2) {
      value = value.replace(/^(\d{2})(\d{0,5}).*/, '($1) $2');
    } else if (value.length > 0) {
      value = value.replace(/^(\d{0,2})/, '($1');
    }
    return value;
  }

  telInputs.forEach((input) => {
    ['input', 'blur'].forEach((evt) => {
      input.addEventListener(evt, () => {
        input.value = maskPhone(input.value);
      });
    });
  });

  // ======================================
  // LABELS DE DATA (inicio/entrega projeto)
  // ======================================
  const inicioInput = document.getElementById('dataInicioProjeto');
  const entregaInput = document.getElementById('dataEntregaProjeto');
  const inicioLabel = document.getElementById('labelDataInicioProjeto');
  const entregaLabel = document.getElementById('labelDataEntregaProjeto');

  const formatBR = (dateStr) => {
    if (!dateStr) return '—/—/----';
    const [y, m, d] = dateStr.split('-');
    return `${d}/${m}/${y}`;
  };

  if (inicioInput && inicioLabel) {
    inicioInput.addEventListener('change', () => {
      inicioLabel.textContent = formatBR(inicioInput.value);
    });
  }

  if (entregaInput && entregaLabel) {
    entregaInput.addEventListener('change', () => {
      entregaLabel.textContent = formatBR(entregaInput.value);
    });
  }

  // ======================================
  // MODAL EDITAR TAREFA (KANBAN)
  // ======================================
  const colEdit = document.getElementById('editTarefaColunaSelect');
  const colEditH = document.getElementById('editTarefaColuna');
  const modalEdit = document.getElementById('modalEditarTarefa');

  if (colEdit && colEditH) {
    colEdit.addEventListener('change', () => {
      colEditH.value = colEdit.value;
    });
  }

  if (modalEdit) {
    modalEdit.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget;
      if (!button) return;

      const tarefaId = button.getAttribute('data-id');
      const titulo = button.getAttribute('data-titulo') || '';
      const descricao = button.getAttribute('data-descricao') || '';
      const coluna = button.getAttribute('data-coluna') || 'backlog';

      document.getElementById('editTarefaId').value = tarefaId;
      document.getElementById('editTarefaTitulo').value = titulo;
      document.getElementById('editTarefaDescricao').value = descricao;
      colEdit.value = coluna;
      colEditH.value = coluna;
    });
  }

  // ======================================
  // MODAL EDITAR PROJETO (carrega dados via AJAX)
  // ======================================
  const modalEditarProjeto = document.getElementById('modalEditarProjeto');

  if (modalEditarProjeto) {
    modalEditarProjeto.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget;
      if (!button) return;

      const id = button.getAttribute('data-id');
      document.getElementById('editProjetoId').value = id;

      fetch('/app/Controllers/ProjetoController.php?acao=getProjeto&id=' + encodeURIComponent(id))
        .then((r) => (r.ok ? r.json() : null))
        .then((data) => {
          if (!data) return;
          document.getElementById('editNomeProjeto').value = data.nome_projeto || '';
          // TODO: preencher demais campos (tipo_projeto, cliente_id, datas, status, descricao)
        });
    });
  }

// função auxiliar para formatar número em "1.234,56"
function floatToBrMoney(v) {
    if (v === null || v === undefined || v === '') return '';
    const num = Number(v) || 0;
    return num
        .toFixed(2)                    // "8000.00"
        .replace('.', ',')             // "8000,00"
        .replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // "8.000,00"
}

document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.btn-editar-entrada');
    if (!btn) return;

    const id = btn.getAttribute('data-id');
    if (!id) return;

    const modal = document.getElementById('modalNovaEntrada');
    const form  = modal.querySelector('form');

    const titleEl = modal.querySelector('.modal-title');
    if (titleEl) {
        titleEl.textContent = 'Editar entrada';
    }

    try {
        const resp = await fetch(`/app/Controllers/FinanceiroController.php?acao=buscar_entrada&id=${id}`);
        const data = await resp.json();
        if (!data || !data.id) return;

        let idInput = form.querySelector('input[name="id"]');
        if (!idInput) {
            idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            form.appendChild(idInput);
        }
        idInput.value = data.id;

        const acaoInput = form.querySelector('input[name="acao"]');
        if (acaoInput) {
            acaoInput.value = 'salvar_entrada';
        }

        if (form.querySelector('[name="data_lancamento"]'))
            form.querySelector('[name="data_lancamento"]').value = data.data_lancamento || '';

        if (form.querySelector('[name="descricao"]'))
            form.querySelector('[name="descricao"]').value = data.descricao || '';

        if (form.querySelector('[name="servico"]'))
            form.querySelector('[name="servico"]').value = data.servico || 'outro';

        if (form.querySelector('[name="tipo_pagamento"]'))
            form.querySelector('[name="tipo_pagamento"]').value = data.tipo_pagamento || 'integral';

        if (form.querySelector('[name="forma_pagamento"]'))
            form.querySelector('[name="forma_pagamento"]').value = data.forma_pagamento || 'pix';

        if (form.querySelector('[name="valor_a_receber"]'))
            form.querySelector('[name="valor_a_receber"]').value =
                floatToBrMoney(data.valor_a_receber);

        if (form.querySelector('[name="valor_recebido"]'))
            form.querySelector('[name="valor_recebido"]').value =
                floatToBrMoney(data.valor_recebido);

        if (form.querySelector('[name="observacoes"]'))
            form.querySelector('[name="observacoes"]').value = data.observacoes || '';

        if (form.querySelector('[name="cliente_id"]'))
            form.querySelector('[name="cliente_id"]').value = data.cliente_id || '';
    } catch (err) {
        console.error('Erro ao carregar entrada para edição', err);
    }
});


  // ======================================
  // BUSCA GLOBAL NO PAINEL (autocomplete)
  // ======================================
  const inputBusca = document.getElementById('global-search');
  const boxResultados = document.getElementById('search-results');

  if (inputBusca && boxResultados) {
    let timer = null;

    inputBusca.addEventListener('input', () => {
      const q = inputBusca.value.trim();
      clearTimeout(timer);

      if (q.length < 2) {
        boxResultados.style.display = 'none';
        boxResultados.innerHTML = '';
        return;
      }

      timer = setTimeout(() => {
        fetch('/app/Controllers/SearchController.php?q=' + encodeURIComponent(q))
          .then((r) => (r.ok ? r.json() : []))
          .then((data) => {
            if (!data.length) {
              boxResultados.innerHTML =
                '<div class="list-group-item small text-muted">Nada encontrado.</div>';
              boxResultados.style.display = 'block';
              return;
            }

            boxResultados.innerHTML = data
              .map((item) => {
                let url = '#';
                let icon = '';

                if (item.tipo === 'cliente') {
                  url = '/modules/painel.php?mod=cliente&id=' + item.id;
                  icon = 'ri-user-fill';
                } else if (item.tipo === 'projeto') {
                  url = '/modules/painel.php?mod=projeto_detalhe&id=' + item.id;
                  icon = 'bi-kanban';
                } else if (item.tipo === 'tarefa') {
                  url = '/modules/painel.php?mod=projeto_detalhe&id=' + item.projeto_id;
                  icon = 'bi-check2-square';
                }

                return `
                  <a href="${url}"
                     class="list-group-item list-group-item-action d-flex align-items-start gap-2">
                    <div class="mt-2"><i class="${icon}"></i></div>
                    <div class="flex-grow-1">
                      <div class="fw-semibold small">${item.titulo || ''}</div>
                      <div class="small text-muted">${item.subtitulo || ''}</div>
                    </div>
                  </a>`;
              })
              .join('');

            boxResultados.style.display = 'block';
          })
          .catch(() => {
            boxResultados.style.display = 'none';
          });
      }, 300); // debounce
    });

    // Esconde resultados ao clicar fora
    document.addEventListener('click', (e) => {
      if (!boxResultados.contains(e.target) && e.target !== inputBusca) {
        boxResultados.style.display = 'none';
      }
    });
  }

  // ======================================
  // TOGGLE DADOS SENSÍVEIS (blur/eye icon)
  // ======================================
  const toggleBtn = document.getElementById('toggleSensitive');
  const sensitiveEls = document.querySelectorAll('.sensitive-value');

  if (toggleBtn && sensitiveEls.length) {
    const icon = toggleBtn.querySelector('i');
    let hidden = false;

    toggleBtn.addEventListener('click', () => {
      hidden = !hidden;

      sensitiveEls.forEach((el) => {
        el.classList.toggle('sensitive-blur', hidden);
      });

      if (icon) {
        icon.className = hidden ? 'ri-eye-line' : 'ri-eye-off-line';
      }
    });
  }
});

// ========================================
// KANBAN: DRAG & DROP (fora do DOMContentLoaded)
// ========================================

let kanbanDragId = null;

// chamado em ondragstart
function kanbanDrag(ev) {
  kanbanDragId = ev.target.getAttribute('data-id');
  ev.dataTransfer.effectAllowed = 'move';
}

// chamado em ondragover
function kanbanAllowDrop(ev) {
  ev.preventDefault();
  ev.dataTransfer.dropEffect = 'move';
}

// chamado em ondrop
function kanbanDrop(ev) {
  ev.preventDefault();
  const colunaEl = ev.currentTarget;
  const coluna = colunaEl.getAttribute('data-coluna');
  if (!kanbanDragId || !coluna) return;

  const item = document.querySelector(`.kanban-item[data-id="${kanbanDragId}"]`);
  if (item && colunaEl) {
    colunaEl.appendChild(item);
  }

  fetch('/app/Controllers/ProjetoController.php?acao=moverTarefa', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
    body: new URLSearchParams({
      tarefa_id: kanbanDragId,
      coluna,
    }),
  }).catch(() => {
    // opcional: tratar erro (ex.: recarregar página)
  });

  kanbanDragId = null;
}

document.addEventListener('DOMContentLoaded', function () {
  const columns = document.querySelectorAll('.pipeline-column-body');

  let draggedCard = null;

  document.addEventListener('dragstart', function (e) {
    const card = e.target.closest('.pipeline-card');
    if (!card) return;
    draggedCard = card;
    e.dataTransfer.effectAllowed = 'move';
  });

  document.addEventListener('dragend', function () {
    draggedCard = null;
  });

  columns.forEach(col => {
    col.addEventListener('dragover', function (e) {
      e.preventDefault();
      e.dataTransfer.dropEffect = 'move';
    });

    col.addEventListener('drop', async function (e) {
      e.preventDefault();
      if (!draggedCard) return;

      // move o card visualmente
      this.appendChild(draggedCard);

      const cardId     = draggedCard.getAttribute('data-id');
      const columnWrap = this.closest('.pipeline-column');
      const estagioId  = columnWrap.getAttribute('data-estagio-id');

      try {
        const formData = new FormData();
        formData.append('id', cardId);
        formData.append('funil_estagio_id', estagioId);

        await fetch('/app/Controllers/PipelineController.php?acao=mover', {
          method: 'POST',
          body: formData,
        });
      } catch (err) {
        console.error('Erro ao mover oportunidade', err);
      }
    });
  });
});

document.addEventListener('click', async function (e) {
  const btn = e.target.closest('[data-bs-target="#modalEditarOportunidade"]');
  if (!btn) return;

  const id = btn.getAttribute('data-id');
  if (!id) return;

  const resp = await fetch(`/app/Controllers/PipelineController.php?acao=buscar&id=${id}`);
  const data = await resp.json();
  if (!data || !data.id) return;

  const modal = document.getElementById('modalEditarOportunidade');
  const form  = modal.querySelector('#form-editar-oportunidade');

  form.querySelector('input[name="id"]').value  = data.id;
  form.querySelector('input[name="titulo"]').value = data.titulo || '';
  form.querySelector('select[name="cliente_id"]').value = data.cliente_id;
  form.querySelector('select[name="funil_estagio_id"]').value = data.funil_estagio_id;

  form.querySelector('input[name="valor_previsto"]').value =
    data.valor_previsto || 0;

  form.querySelector('input[name="probabilidade"]').value =
    data.probabilidade || 0;

  form.querySelector('input[name="origem_lead"]').value =
    data.origem_lead || '';

  form.querySelector('input[name="responsavel"]').value =
    data.responsavel || '';

  form.querySelector('input[name="data_prevista_fechamento"]').value =
    data.data_prevista_fechamento || '';

  form.querySelector('textarea[name="observacoes"]').value =
    data.observacoes || '';
});


// IDs fixos dos estágios (ajuste para os IDs reais da funil_estagios)
const ID_ESTAGIO_GANHO  = 3; // exemplo
const ID_ESTAGIO_PERDIDO = 4; // exemplo

document.addEventListener('click', async function (e) {
  // GANHAR
  const btnGanha = e.target.closest('.btn-op-ganha');
  if (btnGanha) {
    const id = btnGanha.getAttribute('data-id');
    if (!id) return;

    const formData = new FormData();
    formData.append('id', id);
    formData.append('estagio_ganho_id', ID_ESTAGIO_GANHO);

    await fetch('/app/Controllers/PipelineController.php?acao=marcar_ganha', {
      method: 'POST',
      body: formData,
    });

    // opção simples: recarregar a página
    location.reload();
    return;
  }

  // PERDER (pergunta motivo via prompt por enquanto)
  const btnPerder = e.target.closest('.btn-op-perder');
  if (btnPerder) {
    const id = btnPerder.getAttribute('data-id');
    if (!id) return;

    const motivo = prompt('Motivo da perda (opcional):', '');
    const formData = new FormData();
    formData.append('id', id);
    formData.append('estagio_perdido_id', ID_ESTAGIO_PERDIDO);
    formData.append('motivo_perda', motivo || '');

    await fetch('/app/Controllers/PipelineController.php?acao=marcar_perdida', {
      method: 'POST',
      body: formData,
    });

    location.reload();
    return;
  }
});