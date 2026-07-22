document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".details__gallery").forEach(function (galeria) {
    var fotos;
    try {
      fotos = JSON.parse(galeria.getAttribute("data-photos") || "[]");
    } catch (e) {
      fotos = [];
    }
    if (fotos.length < 2) {
      var main = galeria.querySelector(".details__gallery-main img");
      if (main) main.style.cursor = "default";
      if (fotos.length === 1 && main) {
        main.style.cursor = "pointer";
        main.addEventListener("click", function () { abrirLightbox(fotos, 0); });
      }
      return;
    }

    var mainImg = galeria.querySelector(".details__gallery-main img");
    var thumbs = galeria.querySelectorAll(".details__gallery-thumbs img");
    var indiceAtual = 0;

    function mostrarNaPrincipal(indice) {
      indiceAtual = indice;
      mainImg.src = fotos[indice];
    }

    mainImg.style.cursor = "pointer";
    mainImg.addEventListener("click", function () {
      abrirLightbox(fotos, indiceAtual);
    });

    thumbs.forEach(function (thumb, i) {
      thumb.style.cursor = "pointer";
      thumb.addEventListener("click", function () {
        mostrarNaPrincipal(i + 1);
      });
    });
  });

  var overlay = null;
  var fotosAtuais = [];
  var indiceAtualLightbox = 0;

  function criarLightbox() {
    var el = document.createElement("div");
    el.className = "lightbox";
    el.innerHTML =
      '<button type="button" class="lightbox__close" aria-label="Fechar"><i class="bi bi-x-lg"></i></button>' +
      '<button type="button" class="lightbox__prev" aria-label="Foto anterior"><i class="bi bi-chevron-left"></i></button>' +
      '<img class="lightbox__image" src="" alt="Foto do veículo em tamanho ampliado">' +
      '<button type="button" class="lightbox__next" aria-label="Próxima foto"><i class="bi bi-chevron-right"></i></button>' +
      '<div class="lightbox__counter"></div>';
    document.body.appendChild(el);

    el.querySelector(".lightbox__close").addEventListener("click", fecharLightbox);
    el.querySelector(".lightbox__prev").addEventListener("click", function () { navegar(-1); });
    el.querySelector(".lightbox__next").addEventListener("click", function () { navegar(1); });
    el.addEventListener("click", function (e) {
      if (e.target === el) fecharLightbox();
    });

    return el;
  }

  function atualizarLightbox() {
    overlay.querySelector(".lightbox__image").src = fotosAtuais[indiceAtualLightbox];
    overlay.querySelector(".lightbox__counter").textContent =
      (indiceAtualLightbox + 1) + " / " + fotosAtuais.length;
    var multiplo = fotosAtuais.length > 1;
    overlay.querySelector(".lightbox__prev").style.display = multiplo ? "" : "none";
    overlay.querySelector(".lightbox__next").style.display = multiplo ? "" : "none";
    overlay.querySelector(".lightbox__counter").style.display = multiplo ? "" : "none";
  }

  function navegar(passo) {
    indiceAtualLightbox = (indiceAtualLightbox + passo + fotosAtuais.length) % fotosAtuais.length;
    atualizarLightbox();
  }

  function abrirLightbox(fotos, indiceInicial) {
    if (!overlay) overlay = criarLightbox();
    fotosAtuais = fotos;
    indiceAtualLightbox = indiceInicial;
    atualizarLightbox();
    overlay.classList.add("lightbox--aberto");
    document.body.style.overflow = "hidden";
  }

  function fecharLightbox() {
    if (!overlay) return;
    overlay.classList.remove("lightbox--aberto");
    document.body.style.overflow = "";
  }

  document.addEventListener("keydown", function (e) {
    if (!overlay || !overlay.classList.contains("lightbox--aberto")) return;
    if (e.key === "Escape") fecharLightbox();
    if (e.key === "ArrowRight") navegar(1);
    if (e.key === "ArrowLeft") navegar(-1);
  });
});
