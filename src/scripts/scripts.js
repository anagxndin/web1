document.addEventListener("DOMContentLoaded", function () {

  /* ========== MOBILE NAV TOGGLE ========== */
  var toggle = document.getElementById("dropdownToggle");
  var menu = document.getElementById("dropdownMenu");

  if (toggle && menu) {
    toggle.addEventListener("click", function (e) {
      e.stopPropagation();
      menu.classList.toggle("navbar__links--open");
    });

    document.addEventListener("click", function () {
      menu.classList.remove("navbar__links--open");
    });

    menu.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", function () {
        menu.classList.remove("navbar__links--open");
      });
    });
  }

  /* ========== AUTENTICAÇÃO SIMULADA (localStorage) ========== */

  // Carrega usuários do data.json para validar login localmente
  var usuariosCadastrados = [];

  function carregarUsuarios() {
    var stored = localStorage.getItem("veloCity_usuarios");
    if (stored) {
      try { usuariosCadastrados = JSON.parse(stored); } catch(e) {}
    }
  }

  function salvarUsuarios() {
    localStorage.setItem("veloCity_usuarios", JSON.stringify(usuariosCadastrados));
  }

  function usuarioLogado() {
    var data = localStorage.getItem("veloCity_usuario");
    return data ? JSON.parse(data) : null;
  }

  function salvarSessao(usuario) {
    localStorage.setItem("veloCity_usuario", JSON.stringify({
      id: usuario.id,
      nome: usuario.nome,
      email: usuario.email,
    }));
  }

  function encerrarSessao() {
    localStorage.removeItem("veloCity_usuario");
  }

  // Atualiza navbar de acordo com estado de login
  function atualizarNavbar() {
    var navAuth = document.getElementById("navAuth");
    if (!navAuth) return;
    var user = usuarioLogado();
    if (user) {
      navAuth.innerHTML =
        '<span style="color:rgba(255,255,255,0.7);font-size:var(--text-sm);margin-right:0.5rem;">' + user.nome + '</span>' +
        '<form action="../../../backend/api/logout.php" method="post" style="display:inline;margin:0;">' +
          '<button type="submit" class="btn btn--sm btn--ghost" style="color:rgba(255,255,255,0.7);font-size:var(--text-sm);">Sair</button>' +
        '</form>';
      // Attach logout handler
      navAuth.querySelector("form").addEventListener("submit", function (e) {
        e.preventDefault();
        encerrarSessao();
        window.location.href = "login.html";
      });
    } else {
      var isLoginPage = window.location.pathname.indexOf("login.html") !== -1;
      navAuth.innerHTML = isLoginPage
        ? '<a href="cadastro.html" class="navbar__cta">Cadastre-se</a>'
        : '<a href="login.html" class="navbar__cta">Entrar</a>';
    }
  }

  atualizarNavbar();

  // Guard de páginas restritas
  var body = document.body;
  if (body && body.hasAttribute("data-restricted")) {
    var user = usuarioLogado();
    if (!user) {
      window.location.href = "../public/login.html";
    }
  }

  // Carregar usuários salvos (de cadastros anteriores)
  carregarUsuarios();

  // Se não há usuários salvos, tenta carregar do data.json
  if (usuariosCadastrados.length === 0) {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "../../data/veiculos.json", true);
    xhr.onload = function () {
      if (xhr.status === 200) {
        try {
          var data = JSON.parse(xhr.responseText);
          if (data.usuarios) {
            usuariosCadastrados = data.usuarios;
            salvarUsuarios();
          }
        } catch(e) {}
      }
    };
    xhr.send();
  }

  // Expõe funções de autenticação globalmente para forms.js
  window.__auth = {
    usuarioLogado: usuarioLogado,
    salvarSessao: salvarSessao,
    encerrarSessao: encerrarSessao,
    usuariosCadastrados: function() { return usuariosCadastrados; },
  };

});
