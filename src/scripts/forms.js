document.addEventListener("DOMContentLoaded", function () {
  function mostrarAlerta(mensagem, tipo) {
    const caixa = document.getElementById("formAlert");
    if (!caixa) {
      alert(mensagem);
      return;
    }
    caixa.textContent = mensagem;
    caixa.className = "formAlert " + (tipo === "erro" ? "formAlert--erro" : "formAlert--sucesso");
    caixa.style.display = "block";
  }

  function enviarFormulario(form) {
    const botao = form.querySelector('button[type="submit"]');
    if (botao) botao.disabled = true;

    fetch(form.getAttribute("action"), {
      method: "POST",
      body: new FormData(form),
      credentials: "same-origin",
    })
      .then(function (resposta) {
        return resposta.json().then(function (dados) {
          return { status: resposta.status, dados: dados };
        });
      })
      .then(function (resultado) {
        const dados = resultado.dados;
        mostrarAlerta(dados.mensagem, dados.sucesso ? "sucesso" : "erro");
        if (dados.sucesso && dados.redirect) {
          window.setTimeout(function () {
            window.location.href = dados.redirect;
          }, 600);
        } else if (botao) {
          botao.disabled = false;
        }
      })
      .catch(function () {
        mostrarAlerta("Erro de conexão. Tente novamente.", "erro");
        if (botao) botao.disabled = false;
      });
  }

  ["formLogin", "formCadastro", "formInteresse", "formCriarAnuncio"].forEach(function (id) {
    const form = document.getElementById(id);
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

      const dados = new FormData();
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
        const card = b.closest(".col-md-4");
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
        const item = b.closest(".list-group-item");
        if (item) item.remove();
      }
    );
  });
});
