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

  // Mostrar todos os cards inicialmente
  filterCards();

  if (filterMarca) filterMarca.addEventListener("change", filterCards);
  if (filterModelo) filterModelo.addEventListener("change", filterCards);
  if (filterCidade) filterCidade.addEventListener("change", filterCards);

  // Validação do formulário de cadastro
  const formCadastro = document.getElementById("formCadastro");
  
  if (formCadastro) {
    // Máscara CPF
    const inputCpf = document.getElementById("cpf");
    if (inputCpf) {
      inputCpf.addEventListener("input", function(e) {
        let value = e.target.value.replace(/\D/g, "");
        if (value.length > 11) value = value.slice(0, 11);
        
        if (value.length <= 3) {
          e.target.value = value;
        } else if (value.length <= 6) {
          e.target.value = value.slice(0, 3) + "." + value.slice(3);
        } else if (value.length <= 9) {
          e.target.value = value.slice(0, 3) + "." + value.slice(3, 6) + "." + value.slice(6);
        } else {
          e.target.value = value.slice(0, 3) + "." + value.slice(3, 6) + "." + value.slice(6, 9) + "-" + value.slice(9);
        }
      });
    }

    // Máscara Telefone
    const inputTelefone = document.getElementById("telefone");
    if (inputTelefone) {
      inputTelefone.addEventListener("input", function(e) {
        let value = e.target.value.replace(/\D/g, "");
        if (value.length > 11) value = value.slice(0, 11);
        
        if (value.length <= 2) {
          e.target.value = value;
        } else if (value.length <= 6) {
          e.target.value = "(" + value.slice(0, 2) + ") " + value.slice(2);
        } else {
          e.target.value = "(" + value.slice(0, 2) + ") " + value.slice(2, 7) + "-" + value.slice(7);
        }
      });
    }

    formCadastro.addEventListener("submit", function(e) {
      e.preventDefault();
      
      const nome = document.getElementById("nome").value.trim();
      const cpf = document.getElementById("cpf").value.trim();
      const email = document.getElementById("email").value.trim();
      const telefone = document.getElementById("telefone").value.trim();
      const senha = document.getElementById("senha").value;
      const confirmarSenha = document.getElementById("confirmarSenha").value;

      let isValid = true;

      // Validar nome
      if (nome.length < 3) {
        showError("nome", "Nome deve ter pelo menos 3 caracteres");
        isValid = false;
      } else {
        clearError("nome");
      }

      // Validar CPF
      if (!validarCpf(cpf)) {
        showError("cpf", "CPF inválido");
        isValid = false;
      } else {
        clearError("cpf");
      }

      // Validar email
      if (!validarEmail(email)) {
        showError("email", "E-mail inválido");
        isValid = false;
      } else {
        clearError("email");
      }

      // Validar telefone
      if (!validarTelefone(telefone)) {
        showError("telefone", "Telefone inválido");
        isValid = false;
      } else {
        clearError("telefone");
      }

      // Validar senha
      if (senha.length < 6) {
        showError("senha", "Senha deve ter pelo menos 6 caracteres");
        isValid = false;
      } else {
        clearError("senha");
      }

      // Validar confirmação de senha
      if (senha !== confirmarSenha) {
        showError("confirmarSenha", "Senhas não conferem");
        isValid = false;
      } else {
        clearError("confirmarSenha");
      }

      if (isValid) {
        alert("Cadastro realizado com sucesso!");
        formCadastro.reset();
      }
    });

    function validarCpf(cpf) {
      cpf = cpf.replace(/\D/g, "");
      if (cpf.length !== 11) return false;
      if (/^(\d)\1{10}$/.test(cpf)) return false;
      
      let sum = 0;
      let remainder;
      
      for (let i = 1; i <= 9; i++) {
        sum += parseInt(cpf.substring(i - 1, i)) * (11 - i);
      }
      
      remainder = (sum * 10) % 11;
      if (remainder === 10 || remainder === 11) remainder = 0;
      if (remainder !== parseInt(cpf.substring(9, 10))) return false;
      
      sum = 0;
      for (let i = 1; i <= 10; i++) {
        sum += parseInt(cpf.substring(i - 1, i)) * (12 - i);
      }
      
      remainder = (sum * 10) % 11;
      if (remainder === 10 || remainder === 11) remainder = 0;
      if (remainder !== parseInt(cpf.substring(10, 11))) return false;
      
      return true;
    }

    function validarEmail(email) {
      const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return regex.test(email);
    }

    function validarTelefone(telefone) {
      const regex = /^\(\d{2}\) \d{4,5}-\d{4}$/;
      return regex.test(telefone);
    }

    function showError(fieldId, message) {
      const field = document.getElementById(fieldId);
      const errorElement = document.getElementById("erro" + fieldId.charAt(0).toUpperCase() + fieldId.slice(1));
      
      if (field && errorElement) {
        field.classList.add("error");
        errorElement.textContent = message;
        errorElement.classList.add("show");
      }
    }

    function clearError(fieldId) {
      const field = document.getElementById(fieldId);
      const errorElement = document.getElementById("erro" + fieldId.charAt(0).toUpperCase() + fieldId.slice(1));
      
      if (field && errorElement) {
        field.classList.remove("error");
        errorElement.textContent = "";
        errorElement.classList.remove("show");
      }
    }
  }

  // Validação do formulário de login
  const formLogin = document.getElementById("formLogin");
  
  if (formLogin) {
    formLogin.addEventListener("submit", function(e) {
      e.preventDefault();
      
      const email = document.getElementById("loginEmail").value.trim();
      const senha = document.getElementById("loginSenha").value;
      const lembrarMe = document.getElementById("lembrarMe").checked;

      let isValid = true;

      // Validar email
      if (!validarEmail(email)) {
        showErrorLogin("loginEmail", "E-mail inválido");
        isValid = false;
      } else {
        clearErrorLogin("loginEmail");
      }

      // Validar senha
      if (senha.length < 1) {
        showErrorLogin("loginSenha", "Digite sua senha");
        isValid = false;
      } else {
        clearErrorLogin("loginSenha");
      }

      if (isValid) {
        alert("Login realizado com sucesso!");
        // Aqui iria a lógica de login real
        if (lembrarMe) {
          localStorage.setItem("lembrarEmail", email);
        }
        formLogin.reset();
      }
    });

    // Preencher e-mail se estava salvo
    window.addEventListener("load", function() {
      const emailSalvo = localStorage.getItem("lembrarEmail");
      if (emailSalvo) {
        document.getElementById("loginEmail").value = emailSalvo;
        document.getElementById("lembrarMe").checked = true;
      }
    });

    function showErrorLogin(fieldId, message) {
      const field = document.getElementById(fieldId);
      const errorElement = document.getElementById("erro" + fieldId.charAt(0).toUpperCase() + fieldId.slice(1));
      
      if (field && errorElement) {
        field.classList.add("error");
        errorElement.textContent = message;
        errorElement.classList.add("show");
      }
    }

    function clearErrorLogin(fieldId) {
      const field = document.getElementById(fieldId);
      const errorElement = document.getElementById("erro" + fieldId.charAt(0).toUpperCase() + fieldId.slice(1));
      
      if (field && errorElement) {
        field.classList.remove("error");
        errorElement.textContent = "";
        errorElement.classList.remove("show");
      }
    }
  }
});