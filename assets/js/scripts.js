document.querySelectorAll('#sidebarMenu .nav-link, #sidebarCollapseMobile .nav-link').forEach(btn => {
  btn.addEventListener('click', function(e){
    e.preventDefault();
    // Remove active de todos
    document.querySelectorAll('#sidebarMenu .active, #sidebarCollapseMobile .active').forEach(el => el.classList.remove('active'));
    this.classList.add('active');
    loadContent(this.dataset.content);
    // Fecha o hamburguer menu em mobile após clique
    if(window.innerWidth < 992){
      var mobMenu = document.getElementById('sidebarCollapseMobile');
      if(mobMenu && mobMenu.classList.contains('show'))
        new bootstrap.Collapse(mobMenu).hide();
    }
  });
});


function loadContent(mod) {
  fetch('/flowdesk/modules/contents/dash_' + mod + '.php')
    .then(r => r.text())
    .then(html => {
      document.getElementById('dashboardMain').innerHTML = html;
      document.getElementById('label-page').textContent =
        {home: 'Dashboard', financeiro: 'Financeiro', clientes: 'Clientes', jobs: 'Jobs', hospedagem: 'Hospedagem', configuracoes: 'Configurações'}[mod] || 'Dashboard';
      // Atualiza barra de endereço para refletir o módulo atual
      history.replaceState({}, '', '?mod=' + encodeURIComponent(mod));
    });
}
