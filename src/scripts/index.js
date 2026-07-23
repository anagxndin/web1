document.addEventListener("DOMContentLoaded", function () {
  var container = document.getElementById("cardsContainer");
  if (!container) return;

  var filterMarca = document.getElementById("filterMarca");
  var filterModelo = document.getElementById("filterModelo");
  var filterCidade = document.getElementById("filterCidade");

  var todosAnuncios = [];
  var filtroAtivo = {};

  function formatarValor(valor) {
    return Number(valor).toLocaleString("pt-BR", {
      minimumFractionDigits: 2, maximumFractionDigits: 2,
    });
  }

  function extrairUnicos(array, campo) {
    var valores = {};
    array.forEach(function (item) {
      var val = item[campo];
      if (val) valores[val] = true;
    });
    return Object.keys(valores).sort();
  }

  function popularSelect(select, valores, placeholder) {
    select.innerHTML = '<option value="">' + placeholder + "</option>";
    valores.forEach(function (v) {
      var opt = document.createElement("option");
      opt.value = v;
      opt.textContent = v;
      select.appendChild(opt);
    });
  }

  function buscarModelos() {
    var marca = filterMarca.value;
    filterModelo.innerHTML = '<option value="">Carregando...</option>';
    filterModelo.disabled = true;
    filterCidade.innerHTML = '<option value="">Selecione um modelo</option>';
    filterCidade.disabled = true;

    var params = new URLSearchParams();
    if (marca) params.set("marca", marca);

    fetch("../../../backend/api/anuncios_listar.php?" + params.toString())
      .then(function (r) { return r.json(); })
      .then(function (dados) {
        if (!dados.sucesso) return;
        var modelos = extrairUnicos(dados.anuncios, "modelo");
        popularSelect(filterModelo, modelos, "Todos os modelos");
        filterModelo.disabled = false;
        if (filtroAtivo.modelo) filterModelo.value = filtroAtivo.modelo;
      });
  }

  function buscarCidades() {
    var marca = filterMarca.value;
    var modelo = filterModelo.value;
    filterCidade.innerHTML = '<option value="">Carregando...</option>';
    filterCidade.disabled = true;

    var params = new URLSearchParams();
    if (marca) params.set("marca", marca);
    if (modelo) params.set("modelo", modelo);

    fetch("../../../backend/api/anuncios_listar.php?" + params.toString())
      .then(function (r) { return r.json(); })
      .then(function (dados) {
        if (!dados.sucesso) return;
        var cidades = extrairUnicos(dados.anuncios, "cidade");
        popularSelect(filterCidade, cidades, "Todas as cidades");
        filterCidade.disabled = false;
        if (filtroAtivo.cidade) filterCidade.value = filtroAtivo.cidade;
      });
  }

  filterMarca.addEventListener("change", function () {
    filtroAtivo = {};
    filterModelo.value = "";
    filterCidade.value = "";
    if (filterMarca.value) {
      buscarModelos();
    } else {
      filterModelo.innerHTML = '<option value="">Selecione uma marca</option>';
      filterModelo.disabled = true;
      filterCidade.innerHTML = '<option value="">Selecione um modelo</option>';
      filterCidade.disabled = true;
    }
    buscarAnuncios();
  });

  filterModelo.addEventListener("change", function () {
    filtroAtivo.modelo = filterModelo.value;
    if (filterModelo.value) {
      buscarCidades();
    } else {
      filterCidade.innerHTML = '<option value="">Selecione um modelo</option>';
      filterCidade.disabled = true;
    }
    buscarAnuncios();
  });

  filterCidade.addEventListener("change", function () {
    filtroAtivo.cidade = filterCidade.value;
    buscarAnuncios();
  });

  /* ========== CARD ========== */
  function criarCard(anuncio) {
    var marcaModelo = anuncio.marca + " " + anuncio.modelo;
    var foto = anuncio.foto ? "../../../" + anuncio.foto : "../../assets/images/carLogo.png";

    var link = document.createElement("a");
    link.className = "card card--hover";
    link.style.textDecoration = "none";
    link.style.color = "inherit";
    link.href = "interesse.php?id=" + encodeURIComponent(anuncio.id);

    var img = document.createElement("img");
    img.className = "card__image";
    img.src = foto;
    img.alt = marcaModelo;
    img.loading = "lazy";

    var body = document.createElement("div");
    body.className = "card__body";

    var header = document.createElement("div");
    header.className = "card__header";

    var titulo = document.createElement("h3");
    titulo.className = "card__title";
    titulo.textContent = marcaModelo;

    var ano = document.createElement("span");
    ano.className = "card__badge";
    ano.textContent = anuncio.ano_fabricacao;

    header.appendChild(titulo);
    header.appendChild(ano);

    var cidade = document.createElement("p");
    cidade.className = "card__meta";
    cidade.innerHTML = '<i class="bi bi-geo-alt"></i> ' + anuncio.cidade + ", " + anuncio.estado;

    var preco = document.createElement("p");
    preco.className = "card__price";
    preco.textContent = "R$ " + formatarValor(anuncio.valor);

    body.appendChild(header);
    body.appendChild(cidade);
    body.appendChild(preco);
    link.appendChild(img);
    link.appendChild(body);
    return link;
  }

  function renderizarAnuncios(anuncios) {
    container.innerHTML = "";
    if (!anuncios || anuncios.length === 0) {
      container.innerHTML =
        '<div class="empty" style="grid-column:1/-1">' +
          '<div class="empty__icon"><i class="bi bi-car-front"></i></div>' +
          '<h3 class="empty__title">Nenhum anúncio encontrado</h3>' +
          '<p class="empty__text">Tente ajustar os filtros para encontrar veículos disponíveis.</p>' +
        "</div>";
      return;
    }
    anuncios.forEach(function (anuncio) { container.appendChild(criarCard(anuncio)); });
  }

  function mostrarSkeleton() {
    container.innerHTML = "";
    for (var i = 0; i < 6; i++) {
      var sk = document.createElement("div");
      sk.setAttribute("aria-hidden", "true");
      sk.style.cssText = "background:var(--color-surface);border-radius:var(--radius-lg);overflow:hidden;border:1px solid var(--color-border)";
      sk.innerHTML =
        '<div class="skeleton skeleton--image"></div>' +
        '<div style="padding:1.25rem">' +
          '<div class="skeleton skeleton--title"></div>' +
          '<div class="skeleton skeleton--text"></div>' +
          '<div class="skeleton skeleton--price"></div>' +
        "</div>";
      container.appendChild(sk);
    }
  }

  function buscarAnuncios() {
    var params = new URLSearchParams();
    if (filterMarca.value) params.set("marca", filterMarca.value);
    if (filterModelo.value) params.set("modelo", filterModelo.value);
    if (filterCidade.value) params.set("cidade", filterCidade.value);

    mostrarSkeleton();

    fetch("../../../backend/api/anuncios_listar.php?" + params.toString())
      .then(function (r) { return r.json(); })
      .then(function (dados) {
        renderizarAnuncios(dados.sucesso ? dados.anuncios : []);
      })
      .catch(function () { renderizarAnuncios([]); });
  }

  /* ========== INICIAR ========== */
  fetch("../../../backend/api/anuncios_listar.php")
    .then(function (r) { return r.json(); })
    .then(function (dados) {
      if (!dados.sucesso || !dados.anuncios) return;
      todosAnuncios = dados.anuncios;
      var marcas = extrairUnicos(todosAnuncios, "marca");
      popularSelect(filterMarca, marcas, "Todas as marcas");
      renderizarAnuncios(todosAnuncios);
    });
});
