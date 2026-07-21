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
