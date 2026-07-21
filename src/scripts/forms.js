document.addEventListener("DOMContentLoaded", function () {
  function mostrarAlerta(mensagem, tipo) {
    var caixa = document.getElementById("formAlert");
    if (!caixa) {
      alert(mensagem);
      return;
    }
    caixa.textContent = mensagem;
    caixa.classList.remove("alert--hidden", "alert--success", "alert--error", "alert--warning", "alert--info");
    caixa.classList.add(tipo === "erro" ? "alert--error" : "alert--success");
  }

  function enviarFormulario(form) {
    var botao = form.querySelector('button[type="submit"]');
    if (botao) botao.disabled = true;

    var temArquivo = form.querySelector('input[type="file"]');
    var body = temArquivo ? new FormData(form) : new URLSearchParams(new FormData(form));

    fetch(form.getAttribute("action"), {
      method: "POST",
      body: body,
      credentials: "same-origin",
    })
      .then(function (resposta) {
        return resposta.json().then(function (dados) {
          return { status: resposta.status, dados: dados };
        });
      })
      .then(function (resultado) {
        var dados = resultado.dados;
        var formId = form.getAttribute("id");
        mostrarAlerta(dados.mensagem, dados.sucesso ? "sucesso" : "erro");

        if (dados.sucesso) {
          // Login: salva sessão
          if (formId === "formLogin" && window.__auth && dados.usuario) {
            window.__auth.salvarSessao(dados.usuario);
          }
          // Cadastro: adiciona aos usuários cadastrados
          if (formId === "formCadastro" && window.__auth) {
            var users = window.__auth.usuariosCadastrados();
            var formData = new FormData(form);
            users.push({
              id: users.length + 1,
              nome: formData.get("nome"),
              cpf: formData.get("cpf"),
              email: formData.get("email"),
              telefone: formData.get("telefone"),
              senha: formData.get("senha"),
            });
            localStorage.setItem("veloCity_usuarios", JSON.stringify(users));
          }
          // Logout: limpa sessão
          if (formId === "formLogout" && window.__auth) {
            window.__auth.encerrarSessao();
          }
          // Redireciona
          if (dados.redirect) {
            window.setTimeout(function () {
              window.location.href = dados.redirect;
            }, 600);
            return;
          }
        }

        if (botao) botao.disabled = false;
      })
      .catch(function () {
        // Fallback local: tenta autenticar sem servidor
        var formId = form.getAttribute("id");
        if (formId === "formLogin" && window.__auth) {
          var formData = new FormData(form);
          var email = formData.get("email");
          var senha = formData.get("senha");
          var users = window.__auth.usuariosCadastrados();
          var user = users.find(function (u) { return u.email === email && u.senha === senha; });
          if (user) {
            window.__auth.salvarSessao(user);
            window.location.href = "/src/pages/area-restrita/principalRestrita.html";
            return;
          }
        }
        mostrarAlerta("Erro de conexão. Tente novamente.", "erro");
        if (botao) botao.disabled = false;
      });
  }

  ["formLogin", "formCadastro", "formInteresse", "formCriarAnuncio", "formLogout"].forEach(function (id) {
    var form = document.getElementById(id);
    if (form) {
      form.addEventListener("submit", function (evento) {
        evento.preventDefault();
        enviarFormulario(form);
      });
    }
  });

  function excluirComConfirmacao(botao, url, campoId, mensagemConfirmacao, aoRemover) {
    botao.addEventListener("click", function () {
      if (!window.confirm(mensagemConfirmacao)) return;

      var dados = new FormData();
      dados.set("csrf_token", window.CSRF_TOKEN || "");
      dados.set(campoId, botao.dataset.id);

      fetch(url, { method: "POST", body: dados, credentials: "same-origin" })
        .then(function (resposta) { return resposta.json(); })
        .then(function (resultado) {
          if (resultado.sucesso) {
            aoRemover(botao);
          } else {
            alert(resultado.mensagem);
          }
        })
        .catch(function () {
          alert("Erro de conexão. Tente novamente.");
        });
    });
  }

  document.querySelectorAll(".btn-excluir-anuncio").forEach(function (botao) {
    excluirComConfirmacao(
      botao,
      "../../../backend/api/anuncio_excluir.php",
      "anuncio_id",
      "Tem certeza que deseja excluir este anúncio? Essa ação não pode ser desfeita.",
      function (b) {
        var card = b.closest(".card-list__item");
        if (card) card.remove();
      }
    );
  });

  document.querySelectorAll(".btn-excluir-interesse").forEach(function (botao) {
    excluirComConfirmacao(
      botao,
      "../../../backend/api/interesse_excluir.php",
      "interesse_id",
      "Tem certeza que deseja excluir esta mensagem?",
      function (b) {
        var item = b.closest(".interest-item");
        if (item) item.remove();
      }
    );
  });
});
