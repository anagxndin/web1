document.addEventListener("DOMContentLoaded", function () {
  const container = document.getElementById("cardsContainer");
  if (!container) return;

  const filterMarca = document.getElementById("filterMarca");
  const filterModelo = document.getElementById("filterModelo");
  const filterCidade = document.getElementById("filterCidade");

  function formatarValor(valor) {
    return Number(valor).toLocaleString("pt-BR", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function renderizarAnuncios(anuncios) {
    container.innerHTML = "";

    if (!anuncios.length) {
      container.innerHTML = '<p class="text-muted">Nenhum anúncio encontrado.</p>';
      return;
    }

    anuncios.forEach(function (anuncio) {
      // Construído via DOM (não innerHTML) porque marca/modelo/cidade vêm de
      // texto livre digitado por usuários e não podem ser tratados como HTML confiável.
      const marcaModelo = anuncio.marca + " " + anuncio.modelo;
      const foto = anuncio.foto ? "../../" + anuncio.foto : "../../assets/images/carLogo.png";

      const link = document.createElement("a");
      link.className = "card";
      link.style.textDecoration = "none";
      link.style.color = "inherit";
      link.href = "interesse.php?id=" + encodeURIComponent(anuncio.id);

      const img = document.createElement("img");
      img.className = "cardImage";
      img.src = foto;
      img.alt = marcaModelo;

      const info = document.createElement("div");
      info.className = "cardInfo";

      const header = document.createElement("div");
      header.className = "cardHeader";

      const titulo = document.createElement("h3");
      titulo.className = "cardMarcaModelo";
      titulo.textContent = marcaModelo;

      const ano = document.createElement("span");
      ano.className = "cardAno";
      ano.textContent = anuncio.ano_fabricacao;

      header.append(titulo, ano);

      const cidade = document.createElement("p");
      cidade.className = "cardCidade";
      cidade.innerHTML = '<i class="bi bi-geo-alt"></i> ';
      cidade.appendChild(document.createTextNode(anuncio.cidade + ", " + anuncio.estado));

      const valor = document.createElement("p");
      valor.className = "cardValor";
      valor.textContent = "R$ " + formatarValor(anuncio.valor);

      info.append(header, cidade, valor);
      link.append(img, info);
      container.appendChild(link);
    });
  }

  function buscarAnuncios() {
    const params = new URLSearchParams();
    if (filterMarca && filterMarca.value) params.set("marca", filterMarca.value);
    if (filterModelo && filterModelo.value) params.set("modelo", filterModelo.value);
    if (filterCidade && filterCidade.value) params.set("cidade", filterCidade.value);

    container.innerHTML = '<p class="text-muted">Carregando anúncios...</p>';

    fetch("../../../backend/api/anuncios_listar.php?" + params.toString())
      .then(function (resposta) { return resposta.json(); })
      .then(function (dados) {
        if (!dados.sucesso) {
          container.innerHTML = '<p class="text-muted">Não foi possível carregar os anúncios.</p>';
          return;
        }
        renderizarAnuncios(dados.anuncios);
      })
      .catch(function () {
        container.innerHTML = '<p class="text-muted">Não foi possível carregar os anúncios.</p>';
      });
  }

  [filterMarca, filterModelo, filterCidade].forEach(function (select) {
    if (select) select.addEventListener("change", buscarAnuncios);
  });

  buscarAnuncios();
});
