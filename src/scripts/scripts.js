document.addEventListener("DOMContentLoaded", function() {
  const dropdownToggle = document.getElementById("dropdownToggle");
  const dropdownMenu = document.getElementById("dropdownMenu");

  // Toggle dropdown menu
  if (dropdownToggle && dropdownMenu) {
    dropdownToggle.addEventListener("click", function(event) {
      event.stopPropagation();
      dropdownMenu.classList.toggle("active");
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function(event) {
      if (!event.target.closest(".mainHeader")) {
        dropdownMenu.classList.remove("active");
      }
    });

    // Close dropdown when clicking on a menu item
    const dropdownItems = document.querySelectorAll(".dropdownItem");
    dropdownItems.forEach(item => {
      item.addEventListener("click", function() {
        dropdownMenu.classList.remove("active");
      });
    });
  }

  // Mapa de cidades por estado
  const cidadesPorEstado = {
    "SP": ["São Paulo"],
    "RJ": ["Rio de Janeiro"],
    "MG": ["Belo Horizonte"],
    "DF": ["Brasília"],
    "BA": ["Salvador"],
    "PR": ["Curitiba"]
  };

  // Filter functionality
  const filterMarca = document.getElementById("filterMarca");
  const filterModelo = document.getElementById("filterModelo");
  const filterEstado = document.getElementById("filterEstado");
  const filterCidade = document.getElementById("filterCidade");
  const cards = document.querySelectorAll(".card");

  // Atualizar cidades quando estado é selecionado
  if (filterEstado) {
    filterEstado.addEventListener("change", function() {
      const estadoSelecionado = this.value;
      
      // Limpar cidades
      filterCidade.innerHTML = '<option value="">-- Selecionar Cidade --</option>';
      
      if (estadoSelecionado) {
        // Habilitar select de cidade
        filterCidade.disabled = false;
        
        // Preencher cidades do estado selecionado
        const cidades = cidadesPorEstado[estadoSelecionado] || [];
        cidades.forEach(cidade => {
          const option = document.createElement("option");
          option.value = cidade;
          option.textContent = cidade;
          filterCidade.appendChild(option);
        });
      } else {
        // Desabilitar select de cidade
        filterCidade.disabled = true;
        filterCidade.value = "";
      }
      
      filterCards();
    });
  }

  function filterCards() {
    const marcaSelecionada = filterMarca?.value.toLowerCase() || "";
    const modeloSelecionado = filterModelo?.value.toLowerCase() || "";
    const cidadeSelecionada = filterCidade?.value.toLowerCase() || "";

    cards.forEach(card => {
      const cardMarca = card.querySelector(".cardMarcaModelo")?.textContent.split(" ")[0].toLowerCase() || "";
      const cardModelo = card.querySelector(".cardMarcaModelo")?.textContent.split(" ").pop().toLowerCase() || "";
      const cardCidade = card.querySelector(".cardCidade")?.textContent.split(",")[0].trim().toLowerCase() || "";

      const marcaMatch = !marcaSelecionada || cardMarca.includes(marcaSelecionada);
      const modeloMatch = !modeloSelecionado || cardModelo.includes(modeloSelecionado);
      const cidadeMatch = !cidadeSelecionada || cardCidade.includes(cidadeSelecionada);

      if (marcaMatch && modeloMatch && cidadeMatch) {
        card.style.display = "flex";
      } else {
        card.style.display = "none";
      }
    });
  }

  if (filterMarca) filterMarca.addEventListener("change", filterCards);
  if (filterModelo) filterModelo.addEventListener("change", filterCards);
  if (filterCidade) filterCidade.addEventListener("change", filterCards);
});