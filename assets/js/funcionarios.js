// Início do script
console.log("Carregando funcionarios.js");

// Script para depurar a conexão com o backend
try {
  console.log("Testando conexão com listar_funcionarios.php...");
  fetch("listar_funcionarios.php?teste=1")
    .then((response) => {
      console.log("Status da resposta:", response.status, response.statusText);
      if (!response.ok) {
        throw new Error(
          `Erro de HTTP: ${response.status} ${response.statusText}`
        );
      }
      return response.text(); // Obter texto bruto para depuração
    })
    .then((text) => {
      console.log(
        "Resposta bruta do servidor (primeiros 100 caracteres):",
        text.substring(0, 100)
      );
      try {
        // Tentar converter para JSON para verificar se é JSON válido
        const json = JSON.parse(text);
        console.log("JSON válido recebido:", json);
      } catch (jsonError) {
        console.error("Resposta não é JSON válido:", jsonError);

        // Mostrar um aviso no DOM
        const errorMsg = document.createElement("div");
        errorMsg.style.backgroundColor = "orange";
        errorMsg.style.color = "black";
        errorMsg.style.padding = "10px";
        errorMsg.style.position = "fixed";
        errorMsg.style.top = "0";
        errorMsg.style.left = "0";
        errorMsg.style.right = "0";
        errorMsg.style.zIndex = "9999";
        errorMsg.innerHTML =
          "Resposta não é JSON válido. <br>Dados recebidos: " +
          text.substring(0, 100);
        document.body.appendChild(errorMsg);
      }
    })
    .catch((error) => {
      console.error("Erro ao testar conexão com backend:", error);
      // Mostrar um aviso no DOM
      const errorMsg = document.createElement("div");
      errorMsg.style.backgroundColor = "red";
      errorMsg.style.color = "white";
      errorMsg.style.padding = "10px";
      errorMsg.style.position = "fixed";
      errorMsg.style.top = "0";
      errorMsg.style.left = "0";
      errorMsg.style.right = "0";
      errorMsg.style.zIndex = "9999";
      errorMsg.textContent = "Erro de conexão com o servidor: " + error.message;
      document.body.appendChild(errorMsg);
    });
} catch (e) {
  console.error("Erro ao executar teste de conexão:", e);
}

// Definindo a função para adicionar eventos aos botões de ação no escopo global
window.adicionarEventosAcoes = function () {
  console.log("Adicionando eventos aos botões de ação");

  // Remover event listeners existentes para evitar duplicação
  document.querySelectorAll(".btn-visualizar").forEach((btn) => {
    const newBtn = btn.cloneNode(true);
    btn.parentNode.replaceChild(newBtn, btn);
  });

  document.querySelectorAll(".btn-editar").forEach((btn) => {
    const newBtn = btn.cloneNode(true);
    btn.parentNode.replaceChild(newBtn, btn);
  });

  document.querySelectorAll(".btn-status").forEach((btn) => {
    const newBtn = btn.cloneNode(true);
    btn.parentNode.replaceChild(newBtn, btn);
  });

  // Readicionar eventos
  document.querySelectorAll(".btn-visualizar").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation(); // Evitar propagação do evento
      console.log("Clique no botão visualizar detectado");
      const id = this.getAttribute("data-id");
      if (typeof window.visualizarFuncionario === "function") {
        window.visualizarFuncionario(id);
      } else {
        console.error("Função visualizarFuncionario não está disponível");
        alert("Erro: Função de visualização não está disponível");
      }
    });
  });

  document.querySelectorAll(".btn-editar").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation(); // Evitar propagação do evento
      console.log("Clique no botão editar detectado");
      const id = this.getAttribute("data-id");
      if (typeof window.editarFuncionario === "function") {
        window.editarFuncionario(id);
      } else {
        console.error("Função editarFuncionario não está disponível");
        alert("Erro: Função de edição não está disponível");
      }
    });
  });

  document.querySelectorAll(".btn-status").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation(); // Evitar propagação do evento
      console.log("Clique no botão status detectado");
      const id = this.getAttribute("data-id");
      const status = this.getAttribute("data-status");
      if (typeof window.alterarStatusFuncionario === "function") {
        // Passar o próprio botão como terceiro parâmetro
        window.alterarStatusFuncionario(id, status, this);
      } else {
        console.error("Função alterarStatusFuncionario não está disponível");
        alert("Erro: Função de alteração de status não está disponível");
      }
    });
  });
};

// Definindo a função carregarFuncionarios no escopo global para que esteja disponível em todos os lugares
window.carregarFuncionarios = function (pagina = 1) {
  const tabelaBody = document.querySelector("#tabelaFuncionarios tbody");
  const totalRegistros = document.getElementById("totalRegistros");
  const paginacaoLinks = document.getElementById("paginacaoLinks");

  if (!tabelaBody) {
    console.log(
      "Tabela de funcionários não encontrada no DOM. Ignorando carregamento."
    );
    return;
  }

  // Exibir indicador de carregamento
  tabelaBody.innerHTML =
    '<tr><td colspan="6" class="text-center"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Carregando...</td></tr>';

  // Obter elemento de pesquisa se existir
  const campoPesquisa = document.getElementById("pesquisaFuncionario");

  // Preparar parâmetros da busca
  const busca = campoPesquisa ? campoPesquisa.value.trim() : "";
  const limite = 10; // Registros por página

  // Fazer requisição AJAX
  console.log("Carregando funcionários...");
  fetch(
    `listar_funcionarios.php?pagina=${pagina}&limite=${limite}&busca=${encodeURIComponent(
      busca
    )}`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        console.log("Funcionários carregados com sucesso, atualizando tabela");
        // Usar a versão global da função ou local se global não estiver disponível
        if (typeof window.atualizarTabelaFuncionarios === "function") {
          window.atualizarTabelaFuncionarios(
            data,
            tabelaBody,
            totalRegistros,
            paginacaoLinks
          );
        } else {
          atualizarTabelaFuncionarios(
            data,
            tabelaBody,
            totalRegistros,
            paginacaoLinks
          );
        }

        // Garantir que os event listeners sejam adicionados após renderizar a tabela
        console.log(
          "Verificando disponibilidade dos eventos após renderização"
        );
        if (typeof window.adicionarEventosAcoes === "function") {
          console.log("Readicionando eventos após carregar funcionários");
          setTimeout(window.adicionarEventosAcoes, 100); // Pequeno delay para garantir que o DOM está atualizado
        }
      } else {
        tabelaBody.innerHTML =
          '<tr><td colspan="6" class="text-center">Erro ao carregar funcionários: ' +
          (data.message || "Erro desconhecido") +
          "</td></tr>";
        console.error("Erro:", data.message);
      }
    })
    .catch((error) => {
      console.error("Erro ao carregar funcionários:", error);
      tabelaBody.innerHTML =
        '<tr><td colspan="6" class="text-center">Erro de conexão</td></tr>';
    });
};

// Função auxiliar para atualizar a tabela - definida no escopo global
window.atualizarTabelaFuncionarios = function (
  data,
  tabelaBody,
  totalRegistros,
  paginacaoLinks
) {
  const funcionarios = data.funcionarios;

  // Atualizar contagem de registros
  if (totalRegistros) {
    totalRegistros.textContent = data.paginacao?.total_registros || 0;
  }

  // Limpar tabela
  tabelaBody.innerHTML = "";

  if (funcionarios && funcionarios.length === 0) {
    tabelaBody.innerHTML =
      '<tr><td colspan="6" class="text-center">Nenhum funcionário encontrado</td></tr>';
    return;
  }

  // Preencher tabela com os funcionários
  if (funcionarios) {
    funcionarios.forEach((funcionario) => {
      const statusBtn =
        funcionario.status === "ativo"
          ? `<button type="button" class="btn btn-outline-warning btn-status" data-id="${funcionario.id}" data-status="inativo" title="Desativar">
                    <i class="bi bi-trash"></i>
                </button>`
          : `<button type="button" class="btn btn-outline-success btn-status" data-id="${funcionario.id}" data-status="ativo" title="Reativar">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>`;

      tabelaBody.innerHTML += `
                <tr>
                    <td>${funcionario.nome || ""}</td>
                    <td>${funcionario.cpf || ""}</td>
                    <td>${funcionario.funcao || ""}</td>
                    <td>${funcionario.telefone || ""}</td>
                    <td>
                        <span class="badge bg-${
                          funcionario.status_classe || "secondary"
                        }">
                            ${funcionario.status_texto || "N/A"}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-info btn-visualizar" data-id="${
                              funcionario.id
                            }" title="Visualizar">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-editar" data-id="${
                              funcionario.id
                            }" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            ${statusBtn}
                        </div>
                    </td>
                </tr>
            `;
    });
  }

  // Adicionar event listeners para botões de ação
  if (typeof window.adicionarEventosAcoes === "function") {
    window.adicionarEventosAcoes();
  } else {
    console.error("Função adicionarEventosAcoes não está definida");
  }

  // Configurar paginação se existir
  if (paginacaoLinks && data.paginacao) {
    configurarPaginacao(data.paginacao, paginacaoLinks);
  }
};

// Função auxiliar para configurar paginação
function configurarPaginacao(paginacao, paginacaoLinks) {
  const totalPaginas = paginacao.total_paginas;
  const paginaAtual = paginacao.pagina_atual;

  let html = "";

  // Botão anterior
  html += `
        <li class="page-item ${paginaAtual === 1 ? "disabled" : ""}">
            <button class="page-link" data-pagina="${paginaAtual - 1}" ${
    paginaAtual === 1 ? "disabled" : ""
  }>
                <i class="bi bi-chevron-left"></i>
            </button>
        </li>
    `;

  // Páginas
  const mostrarPaginas = 5;
  const metade = Math.floor(mostrarPaginas / 2);

  let inicio = paginaAtual - metade;
  if (inicio < 1) inicio = 1;

  let fim = inicio + mostrarPaginas - 1;
  if (fim > totalPaginas) {
    fim = totalPaginas;
    inicio = Math.max(1, fim - mostrarPaginas + 1);
  }

  for (let i = inicio; i <= fim; i++) {
    html += `
            <li class="page-item ${i === paginaAtual ? "active" : ""}">
                <button class="page-link" data-pagina="${i}">${i}</button>
            </li>
        `;
  }

  // Botão próximo
  html += `
        <li class="page-item ${paginaAtual === totalPaginas ? "disabled" : ""}">
            <button class="page-link" data-pagina="${paginaAtual + 1}" ${
    paginaAtual === totalPaginas ? "disabled" : ""
  }>
                <i class="bi bi-chevron-right"></i>
            </button>
        </li>
    `;

  paginacaoLinks.innerHTML = html;

  // Adicionar eventos aos links de paginação
  document.querySelectorAll("#paginacaoLinks .page-link").forEach((link) => {
    link.addEventListener("click", function () {
      if (this.hasAttribute("disabled")) return;
      const pagina = parseInt(this.getAttribute("data-pagina"), 10);
      carregarFuncionarios(pagina);
    });
  });
}

document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM totalmente carregado");

  // Referência ao formulário e modal
  const form = document.getElementById("formCadastroFuncionario");
  console.log("Formulário encontrado?", !!form);

  const modal = document.getElementById("cadastroFuncionarioModal");
  console.log("Modal encontrado?", !!modal);

  // Criamos uma variável global para a instância do modal
  window.funcionarioModalInstance = null;

  if (modal) {
    // Primeiro, tentar obter instância existente
    try {
      window.funcionarioModalInstance = bootstrap.Modal.getInstance(modal);
      console.log("Instância de modal existente obtida");
    } catch (e) {
      console.log("Modal ainda não foi inicializado, criando nova instância");
    }

    // Se não existir, inicializar
    if (!window.funcionarioModalInstance && typeof bootstrap !== "undefined") {
      try {
        window.funcionarioModalInstance = new bootstrap.Modal(modal);
        console.log("Nova instância de modal criada");
      } catch (e) {
        console.log("Erro ao criar instância do modal:", e);
      }
    }
  }

  // Verificar botão de salvar
  const btnSalvarCheck = document.getElementById("btnSalvarFuncionario");
  console.log("Botão salvar encontrado?", !!btnSalvarCheck);
  if (btnSalvarCheck) {
    console.log("ID do botão:", btnSalvarCheck.id);
    console.log("Texto do botão:", btnSalvarCheck.textContent);
  }

  // Adiciona listener para o evento de modal sendo mostrado
  if (modal) {
    modal.addEventListener("shown.bs.modal", function () {
      console.log("Modal foi aberto, atualizando referência da instância");
      try {
        window.funcionarioModalInstance = bootstrap.Modal.getInstance(modal);
        console.log("Instância do modal atualizada após abertura");
      } catch (e) {
        console.log("Erro ao obter instância após abertura:", e);
      }
    });
  }

  // Elementos DOM frequentemente usados
  const elementos = {
    cpfInput: document.getElementById("cpf"),
    salarioInput: document.getElementById("salario"),
    campoPesquisa: document.getElementById("pesquisaFuncionario"),
    btnPesquisar: document.getElementById("btnPesquisar"),
    btnLimpar: document.getElementById("btnLimpar"),
  };

  // Tentar carregar lista de funcionários ao iniciar (se a tabela existir)
  if (document.querySelector("#tabelaFuncionarios tbody")) {
    try {
      carregarFuncionarios();
    } catch (e) {
      console.error("Erro ao carregar funcionários:", e);
    }
  }

  // Configurar evento de pesquisa
  if (elementos.btnPesquisar) {
    elementos.btnPesquisar.addEventListener("click", function () {
      carregarFuncionarios();
    });
  }

  // Pesquisa ao pressionar Enter
  if (elementos.campoPesquisa) {
    elementos.campoPesquisa.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        carregarFuncionarios();
      }
    });
  }

  // Converter todos os inputs de texto para maiúsculas
  // Aplica nos campos de texto padrão e também nos campos que podem ser adicionados dinamicamente
  function aplicarMaiusculasEmCampos() {
    document.querySelectorAll('input[type="text"]').forEach((input) => {
      // Verificar se já possui o evento (evita duplicação)
      if (!input.dataset.maiusculasAplicadas) {
        input.dataset.maiusculasAplicadas = "true";

        // Converte ao digitar
        input.addEventListener("input", function (e) {
          const start = this.selectionStart;
          const end = this.selectionEnd;
          this.value = this.value.toUpperCase();
          this.setSelectionRange(start, end);
        });

        // Aplica imediatamente se já tiver valor
        if (input.value) {
          input.value = input.value.toUpperCase();
        }

        // Adiciona o estilo CSS também
        input.style.textTransform = "uppercase";
      }
    });
  }

  // Aplica inicialmente e também quando o DOM mudar
  aplicarMaiusculasEmCampos();

  // Observer para aplicar em novos elementos que possam ser adicionados dinamicamente
  const observer = new MutationObserver(aplicarMaiusculasEmCampos);
  observer.observe(document.body, { childList: true, subtree: true });

  // Notificações
  window.showNotification = function (type, message) {
    console.log(`Notificação [${type}]: ${message}`); // Log para depuração

    // Cria um elemento toast para notificação
    const toastContainer = document.createElement("div");
    toastContainer.className = "position-fixed top-0 end-0 p-3";
    toastContainer.style.zIndex = "9999";

    const toastId = "toast-" + Date.now();

    // Define a cor com base no tipo
    let bgColor = "bg-primary"; // padrão
    let icon = "info-circle";

    if (type === "success") {
      bgColor = "bg-success";
      icon = "check-circle";
    } else if (type === "error") {
      bgColor = "bg-danger";
      icon = "exclamation-circle";
    } else if (type === "warning") {
      bgColor = "bg-warning";
      icon = "exclamation-triangle";
    }

    toastContainer.innerHTML = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgColor} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${icon} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

    document.body.appendChild(toastContainer);

    // Garante que o Bootstrap esteja disponível
    if (typeof bootstrap === "undefined") {
      console.error("Bootstrap não está disponível para mostrar o toast");
      alert(message); // Fallback para alert se Bootstrap não estiver disponível
      return;
    }

    const toastElement = document.getElementById(toastId);
    if (!toastElement) {
      console.error("Elemento toast não encontrado");
      return;
    }

    try {
      const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
      toast.show();
    } catch (error) {
      console.error("Erro ao mostrar toast:", error);
      alert(message); // Fallback para alert em caso de erro
    }

    // Remove o elemento após o toast ser escondido
    toastElement.addEventListener("hidden.bs.toast", function () {
      toastContainer.remove();
    });
  };

  // Máscara para CPF
  if (elementos.cpfInput) {
    elementos.cpfInput.addEventListener("input", function (e) {
      let value = e.target.value.replace(/\D/g, "");
      if (value.length > 11) value = value.slice(0, 11);

      if (value.length > 9) {
        value = value.replace(/^(\d{3})(\d{3})(\d{3})/, "$1.$2.$3-");
      } else if (value.length > 6) {
        value = value.replace(/^(\d{3})(\d{3})/, "$1.$2.");
      } else if (value.length > 3) {
        value = value.replace(/^(\d{3})/, "$1.");
      }

      e.target.value = value;
    });
  }

  // Máscara para salário
  if (elementos.salarioInput) {
    elementos.salarioInput.addEventListener("input", function (e) {
      let value = e.target.value.replace(/\D/g, "");

      // Converte para número e formata com 2 casas decimais
      if (value) {
        value = (parseInt(value) / 100).toFixed(2);
        value = value.replace(".", ",");
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
      }

      e.target.value = value;
    });
  }

  // Validações do formulário
  window.validateForm = function () {
    // Validar campos obrigatórios
    const nome = document.getElementById("nome").value.trim();
    const cpf = document.getElementById("cpf").value.trim();
    const rg = document.getElementById("rg").value.trim();

    let isValid = true;
    let message = "";

    if (!nome) {
      isValid = false;
      message += "Nome completo é obrigatório.<br>";
      document.getElementById("nome").classList.add("is-invalid");
    } else {
      document.getElementById("nome").classList.remove("is-invalid");
    }

    if (!cpf) {
      isValid = false;
      message += "CPF é obrigatório.<br>";
      document.getElementById("cpf").classList.add("is-invalid");
    } else {
      document.getElementById("cpf").classList.remove("is-invalid");
    }

    if (!rg) {
      isValid = false;
      message += "RG é obrigatório.<br>";
      document.getElementById("rg").classList.add("is-invalid");
    } else {
      document.getElementById("rg").classList.remove("is-invalid");
    }

    return { isValid, message };
  };

  // Máscara para telefone
  const telefoneInput = document.getElementById("telefone");
  if (telefoneInput) {
    // Adicionar ao objeto elementos
    elementos.telefoneInput = telefoneInput;

    telefoneInput.addEventListener("input", function (e) {
      let value = e.target.value.replace(/\D/g, "");
      if (value.length > 11) value = value.slice(0, 11);

      if (value.length > 10) {
        value = value.replace(/^(\d{2})(\d{5})(\d{4})/, "($1) $2-$3");
      } else if (value.length > 6) {
        value = value.replace(/^(\d{2})(\d{4})/, "($1) $2-");
      } else if (value.length > 2) {
        value = value.replace(/^(\d{2})/, "($1) ");
      }

      e.target.value = value;
    });
  }

  // Máscara para CEP - Apenas formatação simples
  const cepInput = document.getElementById("cep");
  if (cepInput) {
    cepInput.addEventListener("input", function (e) {
      let value = e.target.value.replace(/\D/g, "");
      if (value.length > 8) value = value.slice(0, 8);

      if (value.length > 5) {
        value = value.replace(/^(\d{5})(\d{3})/, "$1-$2");
      }

      e.target.value = value;
    });
  }

  // Botão limpar formulário
  const btnLimpar = document.getElementById("btnLimpar");
  if (btnLimpar) {
    btnLimpar.addEventListener("click", function () {
      document.getElementById("formCadastroFuncionario").reset();

      // Remove classes de validação
      const inputs = document.querySelectorAll(".is-invalid");
      inputs.forEach((input) => {
        input.classList.remove("is-invalid");
      });
    });
  }

  // Submissão do formulário via botão (não mais via evento submit)
  const btnSalvar = document.getElementById("btnSalvarFuncionario");
  console.log("Configurando evento click no btnSalvar:", btnSalvar);

  if (btnSalvar && form) {
    // Adiciona evento ao botão salvar
    btnSalvar.addEventListener("click", function (e) {
      e.preventDefault(); // Previne qualquer comportamento padrão
      console.log("Botão salvar clicado!");

      // Validar o formulário
      const validation = validateForm();
      if (!validation.isValid) {
        showNotification("error", validation.message);
        return;
      }

      // Desabilitar botão para evitar múltiplos envios
      const btnSalvarOriginalText = this.innerHTML;
      this.disabled = true;
      this.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';

      // Preparar dados para envio
      const formData = new FormData(form);

      console.log("Enviando formulário via AJAX...");

      // Determinar a URL com base no modo do formulário
      let actionUrl = "cadastrar_funcionario.php";
      const formMode = form.getAttribute("data-mode");

      // Se estiver no modo de edição, usar a URL de atualização
      if (formMode === "edit") {
        actionUrl = "atualizar_funcionario.php";
        console.log("Modo de edição detectado, usando URL:", actionUrl);

        // Verificar se temos o ID do funcionário
        const idFuncionario = form.querySelector('input[name="id"]');
        if (!idFuncionario || !idFuncionario.value) {
          // Tentar localizar o ID nas outras funções
          const idFromForms = document.querySelector(
            '#cadastroFuncionarioModal input[name="id"]'
          );
          if (idFromForms && idFromForms.value) {
            console.log(
              "ID encontrado em outro elemento, copiando valor:",
              idFromForms.value
            );

            // Se não existir, criar o campo
            if (!idFuncionario) {
              const newIdInput = document.createElement("input");
              newIdInput.type = "hidden";
              newIdInput.name = "id";
              newIdInput.value = idFromForms.value;
              form.appendChild(newIdInput);
              console.log("Campo de ID criado com valor:", newIdInput.value);
            } else {
              idFuncionario.value = idFromForms.value;
              console.log(
                "Campo de ID atualizado com valor:",
                idFuncionario.value
              );
            }
          } else {
            console.error("ID do funcionário não encontrado para edição!");
            showNotification(
              "error",
              "Erro: ID do funcionário não encontrado para edição"
            );
            return;
          }
        } else {
          console.log("ID do funcionário para edição:", idFuncionario.value);
        }
      } else {
        console.log("Modo de cadastro detectado, usando URL:", actionUrl);
      }

      // Enviar requisição AJAX
      fetch(actionUrl, {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest", // Indica que é uma requisição AJAX
        },
      })
        .then((response) => {
          console.log("Resposta recebida:", response);
          if (!response.ok) {
            throw new Error("Erro na resposta do servidor: " + response.status);
          }
          return response.json();
        })
        .then((data) => {
          console.log("Dados recebidos:", data);
          // Sempre habilita o botão novamente
          this.disabled = false;
          this.innerHTML = btnSalvarOriginalText;

          if (data.status === "success") {
            console.log("Operação bem-sucedida!");
            // Mostrar alerta de sucesso
            const isEdit = form.getAttribute("data-mode") === "edit";
            const message = isEdit
              ? "Funcionário atualizado com sucesso!"
              : "Funcionário cadastrado com sucesso!";
            showNotification("success", data.message || message);

            // Limpar formulário
            form.reset();

            // Recarregar a tabela de funcionários sem recarregar a página
            console.log("Tentando atualizar a tabela de funcionários");
            if (document.querySelector("#tabelaFuncionarios")) {
              try {
                carregarFuncionarios();
              } catch (e) {
                console.error("Erro ao recarregar funcionários:", e);
                setTimeout(() => window.location.reload(), 1500);
              }
            } else if (data.status === "redirect") {
              console.log("Redirecionando requisição para:", data.redirect);

              // Se precisamos redirecionar, reenviar o formulário para a URL correta
              const formData = new FormData(form);

              // Enviar requisição AJAX para a URL correta
              fetch(data.redirect, {
                method: "POST",
                body: formData,
                headers: {
                  "X-Requested-With": "XMLHttpRequest",
                },
              })
                .then((response) => response.json())
                .then((redirectData) => {
                  if (redirectData.status === "success") {
                    showNotification(
                      "success",
                      redirectData.message ||
                        "Funcionário atualizado com sucesso!"
                    );

                    // Recarregar a tabela de funcionários
                    if (typeof carregarFuncionarios === "function") {
                      carregarFuncionarios();
                    }

                    // O modal será fechado automaticamente pelo modal-fix.js
                  } else {
                    showNotification(
                      "error",
                      redirectData.message || "Erro ao atualizar funcionário."
                    );
                  }
                })
                .catch((error) => {
                  console.error("Erro após redirecionamento:", error);
                  showNotification(
                    "error",
                    "Erro ao processar a requisição após redirecionamento."
                  );
                });
            }
          } else {
            // Mostrar mensagem de erro
            showNotification(
              "error",
              data.message || "Erro ao cadastrar funcionário."
            );
          }
        })
        .catch((error) => {
          console.error("Erro durante o envio do formulário:", error);
          showNotification(
            "error",
            "Erro ao processar a requisição: " + error.message
          );

          // Habilitar botão novamente
          this.disabled = false;
          this.innerHTML = btnSalvarOriginalText;
        });
    });
  } else {
    console.error("Botão de salvar ou formulário não encontrado!", {
      btnSalvar: btnSalvar ? "encontrado" : "não encontrado",
      form: form ? "encontrado" : "não encontrado",
    });
  }

  // Navegação entre abas
  const tabs = document.querySelectorAll("#cadastroTab button");
  const nextButtons = document.querySelectorAll(".btn-next-tab");
  const prevButtons = document.querySelectorAll(".btn-prev-tab");

  if (nextButtons) {
    nextButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const currentTab = document.querySelector(".tab-pane.active");
        const currentTabId = currentTab.getAttribute("id");

        let nextTabIndex;
        for (let i = 0; i < tabs.length; i++) {
          if (tabs[i].getAttribute("aria-controls") === currentTabId) {
            nextTabIndex = i + 1;
            break;
          }
        }

        if (nextTabIndex < tabs.length) {
          tabs[nextTabIndex].click();
        }
      });
    });
  }

  if (prevButtons) {
    prevButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const currentTab = document.querySelector(".tab-pane.active");
        const currentTabId = currentTab.getAttribute("id");

        let prevTabIndex;
        for (let i = 0; i < tabs.length; i++) {
          if (tabs[i].getAttribute("aria-controls") === currentTabId) {
            prevTabIndex = i - 1;
            break;
          }
        }

        if (prevTabIndex >= 0) {
          tabs[prevTabIndex].click();
        }
      });
    });
  }

  // Função para carregar funcionários na tabela
  window.carregarFuncionarios = function (pagina = 1) {
    const tabelaBody = document.querySelector("#tabelaFuncionarios tbody");
    const totalRegistros = document.getElementById("totalRegistros");
    const paginacaoLinks = document.getElementById("paginacaoLinks");

    // Usa o elemento do objeto centralizado para evitar problemas
    const campoPesquisa =
      elementos.campoPesquisa || document.getElementById("pesquisaFuncionario");

    if (!tabelaBody) {
      console.error("Tabela de funcionários não encontrada no DOM");
      return;
    }

    // Exibir indicador de carregamento
    tabelaBody.innerHTML =
      '<tr><td colspan="6" class="text-center"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Carregando...</td></tr>';

    // Preparar parâmetros da busca
    const busca = campoPesquisa ? campoPesquisa.value.trim() : "";
    const limite = 10; // Registros por página

    // Fazer requisição AJAX
    fetch(
      `listar_funcionarios.php?pagina=${pagina}&limite=${limite}&busca=${encodeURIComponent(
        busca
      )}`
    )
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          const funcionarios = data.funcionarios;

          // Atualizar contagem de registros
          if (totalRegistros) {
            totalRegistros.textContent = data.paginacao.total_registros;
          }

          // Limpar tabela
          tabelaBody.innerHTML = "";

          if (funcionarios.length === 0) {
            tabelaBody.innerHTML =
              '<tr><td colspan="6" class="text-center">Nenhum funcionário encontrado</td></tr>';
            return;
          }

          // Preencher tabela com os funcionários
          funcionarios.forEach((funcionario) => {
            tabelaBody.innerHTML += `
                            <tr>
                                <td>${funcionario.nome}</td>
                                <td>${funcionario.cpf}</td>
                                <td>${funcionario.funcao}</td>
                                <td>${funcionario.telefone}</td>
                                <td>
                                    <span class="badge bg-${funcionario.status_classe}">
                                        ${funcionario.status_texto}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info btn-visualizar" data-id="${funcionario.id}" title="Visualizar">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary btn-editar" data-id="${funcionario.id}" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-excluir" data-id="${funcionario.id}" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
          });

          // Configurar paginação
          if (paginacaoLinks) {
            const totalPaginas = data.paginacao.total_paginas;
            const paginaAtual = data.paginacao.pagina_atual;

            let html = "";

            // Botão anterior
            html += `
                            <li class="page-item ${
                              paginaAtual === 1 ? "disabled" : ""
                            }">
                                <button class="page-link" data-pagina="${
                                  paginaAtual - 1
                                }" ${paginaAtual === 1 ? "disabled" : ""}>
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                            </li>
                        `;

            // Páginas
            const mostrarPaginas = 5;
            const metade = Math.floor(mostrarPaginas / 2);

            let inicio = paginaAtual - metade;
            if (inicio < 1) inicio = 1;

            let fim = inicio + mostrarPaginas - 1;
            if (fim > totalPaginas) {
              fim = totalPaginas;
              inicio = Math.max(1, fim - mostrarPaginas + 1);
            }

            for (let i = inicio; i <= fim; i++) {
              html += `
                                <li class="page-item ${
                                  i === paginaAtual ? "active" : ""
                                }">
                                    <button class="page-link" data-pagina="${i}">${i}</button>
                                </li>
                            `;
            }

            // Botão próximo
            html += `
                            <li class="page-item ${
                              paginaAtual === totalPaginas ? "disabled" : ""
                            }">
                                <button class="page-link" data-pagina="${
                                  paginaAtual + 1
                                }" ${
              paginaAtual === totalPaginas ? "disabled" : ""
            }>
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </li>
                        `;

            paginacaoLinks.innerHTML = html;

            // Adicionar evento aos links de paginação
            document
              .querySelectorAll("#paginacaoLinks .page-link")
              .forEach((link) => {
                link.addEventListener("click", function () {
                  if (this.hasAttribute("disabled")) return;

                  const pagina = parseInt(this.getAttribute("data-pagina"));
                  carregarFuncionarios(pagina);
                });
              });
          }

          // Configurar botão de visualização
          document.querySelectorAll(".btn-visualizar").forEach((btn) => {
            btn.addEventListener("click", function () {
              const id = this.getAttribute("data-id");
              console.log("Botão visualizar clicado para ID:", id);
              if (typeof window.visualizarFuncionario === "function") {
                window.visualizarFuncionario(id);
              } else {
                console.error("Função visualizarFuncionario não está definida");
                alert(
                  "Erro: Função de visualização não disponível. Verifique o console."
                );
              }
            });
          });

          // Configurar botão de edição
          document.querySelectorAll(".btn-editar").forEach((btn) => {
            btn.addEventListener("click", function () {
              const id = this.getAttribute("data-id");
              console.log("Botão editar clicado para ID:", id);
              if (typeof window.editarFuncionario === "function") {
                window.editarFuncionario(id);
              } else {
                console.error("Função editarFuncionario não está definida");
                alert(
                  "Erro: Função de edição não disponível. Verifique o console."
                );
              }
            });
          });

          // Configurar botão de alterar status (inativar/ativar)
          document.querySelectorAll(".btn-excluir").forEach((btn) => {
            btn.addEventListener("click", function () {
              const id = this.getAttribute("data-id");
              const row = this.closest("tr");
              const statusCell = row.querySelector("td:nth-child(5) .badge");
              const isAtivo =
                statusCell.textContent.trim().toLowerCase() === "ativo";

              const confirmMessage = isAtivo
                ? "Tem certeza que deseja inativar este funcionário?"
                : "Tem certeza que deseja ativar este funcionário?";

              if (confirm(confirmMessage)) {
                console.log("Alterando status para ID:", id);
                if (typeof window.alterarStatusFuncionario === "function") {
                  // Passar o próprio botão de excluir como parâmetro
                  const status = isAtivo ? "inativo" : "ativo";
                  window.alterarStatusFuncionario(id, status, this);
                } else {
                  console.error(
                    "Função alterarStatusFuncionario não está definida"
                  );
                  alert(
                    "Erro: Função de alteração de status não disponível. Verifique o console."
                  );
                }
              }
            });
          });
        } else {
          tabelaBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Erro ao carregar funcionários: ${data.message}</td></tr>`;
        }
      })
      .catch((error) => {
        console.error("Erro:", error);
        tabelaBody.innerHTML =
          '<tr><td colspan="6" class="text-center text-danger">Erro ao carregar funcionários. Tente novamente.</td></tr>';
      });
  };
});
