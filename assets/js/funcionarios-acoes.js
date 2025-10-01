console.log("Carregando funcionarios-acoes.js");
// ===== FUNÇÕES PARA MANIPULAÇÃO DE FUNCIONÁRIOS =====

// Remova o evento DOMContentLoaded para não interferir na definição global das funções

// Evento para adicionar ouvintes de eventos após o carregamento do DOM
document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM carregado em funcionarios-acoes.js - adicionando eventos");
  setTimeout(function () {
    // Adicionando eventos de clique diretamente aos botões existentes após um pequeno delay
    console.log("Adicionando eventos aos botões após delay");
    try {
      document.querySelectorAll(".btn-visualizar").forEach(function (btn) {
        btn.addEventListener("click", function () {
          console.log("Clique no botão visualizar");
          const id = this.getAttribute("data-id");
          if (window.visualizarFuncionario) {
            window.visualizarFuncionario(id);
          } else {
            console.error(
              "Função visualizarFuncionario não está disponível no momento do clique"
            );
          }
        });
      });

      document.querySelectorAll(".btn-editar").forEach(function (btn) {
        btn.addEventListener("click", function () {
          console.log("Clique no botão editar");
          const id = this.getAttribute("data-id");
          if (window.editarFuncionario) {
            window.editarFuncionario(id);
          } else {
            console.error(
              "Função editarFuncionario não está disponível no momento do clique"
            );
          }
        });
      });

      document.querySelectorAll(".btn-status").forEach(function (btn) {
        btn.addEventListener("click", function () {
          console.log("Clique no botão status");
          const id = this.getAttribute("data-id");
          const status = this.getAttribute("data-status");
          if (window.alterarStatusFuncionario) {
            window.alterarStatusFuncionario(id, status, this);
          } else {
            console.error(
              "Função alterarStatusFuncionario não está disponível no momento do clique"
            );
          }
        });
      });
      console.log("Eventos adicionados com sucesso");
    } catch (error) {
      console.error("Erro ao adicionar eventos:", error);
    }
  }, 500);
});

// Função para visualizar detalhes do funcionário
window.visualizarFuncionario = function (id) {
  console.log("Visualizando funcionário ID:", id);

  // Criar ou obter o modal de visualização
  let modalVisualizacao = document.getElementById("visualizarFuncionarioModal");

  // Se o modal não existir, criá-lo
  if (!modalVisualizacao) {
    const modalHtml = `
            <div class="modal fade" id="visualizarFuncionarioModal" tabindex="-1" aria-labelledby="visualizarFuncionarioModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title" id="visualizarFuncionarioModalLabel">Detalhes do Funcionário</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <p class="mt-2">Carregando informações...</p>
                            </div>
                            <div id="detalhes-funcionario" style="display: none;">
                                <!-- Os detalhes serão inseridos aqui via JavaScript -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

    // Adicionar o modal ao documento
    const modalContainer = document.createElement("div");
    modalContainer.innerHTML = modalHtml;
    document.body.appendChild(modalContainer);

    modalVisualizacao = document.getElementById("visualizarFuncionarioModal");
  }

  // Mostrar o modal de visualização
  const bsModal = new bootstrap.Modal(modalVisualizacao);
  bsModal.show();

  // Limpar conteúdo anterior e mostrar spinner
  const detalhesContainer = modalVisualizacao.querySelector(
    "#detalhes-funcionario"
  );
  const spinnerContainer = modalVisualizacao.querySelector(".text-center");

  detalhesContainer.style.display = "none";
  spinnerContainer.style.display = "block";

  // Fazer a requisição para obter os dados do funcionário
  fetch(`visualizar_funcionario.php?id=${id}`, {
    method: "GET",
    headers: {
      "X-Requested-With": "XMLHttpRequest",
    },
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Erro ao buscar dados do funcionário");
      }
      return response.json();
    })
    .then((data) => {
      spinnerContainer.style.display = "none";
      detalhesContainer.style.display = "block";

      if (data.status === "success") {
        // Renderizar os dados do funcionário
        renderizarDetalhesFuncionario(detalhesContainer, data.data);
      } else {
        detalhesContainer.innerHTML = `<div class="alert alert-danger">${
          data.message || "Erro ao carregar dados do funcionário"
        }</div>`;
      }
    })
    .catch((error) => {
      console.error("Erro:", error);
      spinnerContainer.style.display = "none";
      detalhesContainer.style.display = "block";
      detalhesContainer.innerHTML = `<div class="alert alert-danger">Erro ao carregar dados: ${error.message}</div>`;
    });
};

// Função para renderizar os detalhes do funcionário no modal
const renderizarDetalhesFuncionario = function (container, dados) {
  // Formatar datas
  const formatarData = (dataStr) => {
    if (!dataStr) return "N/A";
    const data = new Date(dataStr);
    return data.toLocaleDateString("pt-BR");
  };

  // Status estilizado
  const statusBadge =
    dados.status === "ativo"
      ? '<span class="badge bg-success">Ativo</span>'
      : '<span class="badge bg-danger">Inativo</span>';

  // Criar HTML para as certificações
  let certificacoesHtml = '<div class="mt-2"><strong>Certificações:</strong> ';
  const certificacoes = [];

  if (dados.certificacoes.vigilante) certificacoes.push("Vigilante");
  if (dados.certificacoes.reciclagem) certificacoes.push("Reciclagem");
  if (dados.certificacoes.armadefogo) certificacoes.push("Arma de Fogo");
  if (dados.certificacoes.segurancapessoal)
    certificacoes.push("Segurança Pessoal");

  certificacoesHtml +=
    certificacoes.length > 0 ? certificacoes.join(", ") : "Nenhuma";
  certificacoesHtml += "</div>";

  // Montar HTML para exibir detalhes do funcionário
  const html = `
        <div class="row">
            <div class="col-12 mb-3">
                <h4>${dados.nome} ${statusBadge}</h4>
                <hr>
            </div>
            
            <div class="col-md-6">
                <h5 class="text-primary mb-3">Dados Pessoais</h5>
                <p><strong>CPF:</strong> ${dados.cpf || "N/A"}</p>
                <p><strong>RG:</strong> ${dados.rg || "N/A"}</p>
                <p><strong>Data de Nascimento:</strong> ${formatarData(
                  dados.dataNascimento
                )}</p>
                <p><strong>Gênero:</strong> ${dados.genero || "N/A"}</p>
                <p><strong>Telefone:</strong> ${dados.telefone || "N/A"}</p>
                <p><strong>E-mail:</strong> ${dados.email || "N/A"}</p>
            </div>
            
            <div class="col-md-6">
                <h5 class="text-primary mb-3">Endereço</h5>
                <p><strong>CEP:</strong> ${dados.cep || "N/A"}</p>
                <p><strong>Endereço:</strong> ${dados.endereco || "N/A"}, ${
    dados.numero || "S/N"
  }</p>
                <p><strong>Complemento:</strong> ${
                  dados.complemento || "N/A"
                }</p>
                <p><strong>Bairro:</strong> ${dados.bairro || "N/A"}</p>
                <p><strong>Cidade/Estado:</strong> ${dados.cidade || "N/A"} - ${
    dados.estado || "N/A"
  }</p>
            </div>
            
            <div class="col-12">
                <hr>
                <h5 class="text-primary mb-3">Informações Profissionais</h5>
            </div>
            
            <div class="col-md-6">
                <p><strong>Função:</strong> ${dados.funcao || "N/A"}</p>
                <p><strong>Departamento:</strong> ${
                  dados.departamento || "N/A"
                }</p>
                <p><strong>Data de Admissão:</strong> ${formatarData(
                  dados.dataAdmissao
                )}</p>
                <p><strong>Turno:</strong> ${dados.turno || "N/A"}</p>
            </div>
            
            <div class="col-md-6">
                <p><strong>Salário:</strong> R$ ${dados.salario || "0,00"}</p>
                <p><strong>Status:</strong> ${dados.status || "N/A"}</p>
                ${certificacoesHtml}
            </div>
            
            <div class="col-12 mt-3">
                <h5 class="text-primary">Observações</h5>
                <p class="border p-2 rounded bg-light">${
                  dados.observacoes || "Nenhuma observação registrada."
                }</p>
            </div>
        </div>
    `;

  container.innerHTML = html;
};

// Função para editar um funcionário
window.editarFuncionario = function (id) {
  console.log("Editando funcionário ID:", id);

  // Abrir o modal de cadastro mas no modo de edição
  const modal = document.getElementById("cadastroFuncionarioModal");
  if (!modal) {
    alert("Erro: Modal de cadastro não encontrado.");
    return;
  }

  // Limpar o formulário antes de carregar os novos dados
  const form = document.getElementById("formCadastroFuncionario");

  // Verificar se o formulário existe
  if (!form) {
    alert(
      'Erro: Formulário de cadastro não encontrado. Certifique-se de que o ID "formCadastroFuncionario" existe.'
    );
    console.error('Formulário com ID "formCadastroFuncionario" não encontrado');
    return;
  }

  // Reset do formulário
  form.reset();

  // Atualizar o título do modal
  const modalTitle = modal.querySelector(".modal-title");
  if (modalTitle) modalTitle.textContent = "Editar Funcionário";

  // Adicionar um campo oculto para o ID do funcionário
  let idField = form.querySelector('input[name="id"]');
  if (!idField) {
    idField = document.createElement("input");
    idField.type = "hidden";
    idField.name = "id";
    form.appendChild(idField);
  }
  idField.value = id;

  // Definir a ação do formulário para atualização
  if (form) {
    form.setAttribute("data-mode", "edit");
  }

  // Mostrar indicador de carregamento
  const tabs = modal.querySelectorAll(".tab-pane");
  tabs.forEach((tab) => {
    const loadingDiv = document.createElement("div");
    loadingDiv.className = "loading-indicator text-center my-3";
    loadingDiv.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <p class="mt-2">Carregando dados do funcionário...</p>
        `;
    tab.prepend(loadingDiv);
  });

  // Abrir o modal
  const bsModal = new bootstrap.Modal(modal);
  bsModal.show();

  // Buscar dados do funcionário
  fetch(`visualizar_funcionario.php?id=${id}`, {
    method: "GET",
    headers: {
      "X-Requested-With": "XMLHttpRequest",
    },
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Erro ao buscar dados do funcionário");
      }
      return response.json();
    })
    .then((data) => {
      // Remover indicadores de carregamento
      const loadingIndicators = modal.querySelectorAll(".loading-indicator");
      loadingIndicators.forEach((indicator) => indicator.remove());

      if (data.status === "success") {
        // Preencher o formulário com os dados do funcionário
        preencherFormularioEdicao(form, data.data, id);
      } else {
        showNotification(
          "error",
          data.message || "Erro ao carregar dados do funcionário"
        );
      }
    })
    .catch((error) => {
      console.error("Erro:", error);
      const loadingIndicators = modal.querySelectorAll(".loading-indicator");
      loadingIndicators.forEach((indicator) => indicator.remove());
      showNotification("error", `Erro ao carregar dados: ${error.message}`);
    });
};

// Função para preencher o formulário de edição com os dados do funcionário
const preencherFormularioEdicao = function (form, dados, formId) {
  // Verificar se o form existe
  if (!form) {
    console.error("Formulário não fornecido para preenchimento");
    showNotification("error", "Erro ao editar: Formulário não encontrado");
    return;
  }

  // Armazenar o ID do funcionário que veio da requisição
  const funcionarioId = dados.id || formId;
  console.log("ID do funcionário recebido para preenchimento:", funcionarioId);

  // Mapear os campos do formulário com os dados recebidos
  const mapeamentoCampos = {
    nome: dados.nome,
    cpf: dados.cpf,
    rg: dados.rg,
    dataNascimento: dados.dataNascimento,
    telefone: dados.telefone,
    email: dados.email,
    genero: dados.genero,
    cep: dados.cep,
    endereco: dados.endereco,
    numero: dados.numero,
    complemento: dados.complemento,
    bairro: dados.bairro,
    cidade: dados.cidade,
    estado: dados.estado,
    funcao: dados.funcao,
    departamento: dados.departamento,
    dataAdmissao: dados.dataAdmissao,
    turno: dados.turno,
    salario: dados.salario,
    observacoes: dados.observacoes,
  };

  try {
    // Preencher os campos de texto, select, etc.
    for (const [campo, valor] of Object.entries(mapeamentoCampos)) {
      const input = form.querySelector(`[name="${campo}"]`);
      if (input) input.value = valor || "";
    }

    // Preencher as certificações (checkboxes)
    if (dados.certificacoes) {
      const certificacoes = dados.certificacoes;
      for (const [cert, checked] of Object.entries(certificacoes)) {
        const checkbox = form.querySelector(
          `[name="certificacoes[]"][value="${cert}"]`
        );
        if (checkbox) checkbox.checked = checked;
      }
    }
  } catch (error) {
    console.error("Erro ao preencher formulário:", error);
    showNotification("error", "Erro ao preencher dados do formulário");
  }

  // Modificar o botão de salvar para indicar que está editando
  const btnSalvar = document.getElementById("btnSalvarFuncionario");
  if (btnSalvar) {
    btnSalvar.textContent = "Salvar Alterações";

    // Adicionar ID do funcionário como campo oculto
    let idFuncInput = form.querySelector('input[name="id"]');
    if (!idFuncInput) {
      idFuncInput = document.createElement("input");
      idFuncInput.type = "hidden";
      idFuncInput.name = "id";
      form.appendChild(idFuncInput);
    }
    idFuncInput.value = funcionarioId;

    console.log(
      "ID do funcionário configurado para edição:",
      idFuncInput.value
    );

    // Não vamos definir um novo handler de evento aqui,
    // apenas configuramos o formulário corretamente para edição
  }
};

// Função para alterar o status do funcionário (ativar/inativar)
window.alterarStatusFuncionario = function (id, novoStatus, buttonElement) {
  // Garantir que sempre tenhamos um valor para novoStatus
  if (!novoStatus) {
    // Verificar o status atual na linha, se possível
    if (buttonElement) {
      const row = buttonElement.closest("tr");
      if (row) {
        const statusCell = row.querySelector("td:nth-child(5) .badge");
        if (statusCell) {
          const isAtivo = statusCell.textContent.trim().toLowerCase() === "ativo";
          novoStatus = isAtivo ? "inativo" : "ativo";
        }
      }
    }
    
    // Se ainda não temos um valor, usar 'inativo' como padrão
    if (!novoStatus) {
      novoStatus = "inativo";
    }
  }
  
  console.log("Alterando status do funcionário ID:", id, "para:", novoStatus);

  // Usar o botão passado como parâmetro ou encontrar o botão na tabela
  let button = buttonElement;
  if (!button) {
    button = document.querySelector(`.btn-excluir[data-id="${id}"]`);
  }
  
  if (!button) {
    console.error(`Botão com data-id=${id} não encontrado!`);
    
    // Função segura para mostrar notificações
    const safeNotify = (type, message) => {
      if (typeof showNotification === 'function') {
        showNotification(type, message);
      } else if (typeof window.showNotification === 'function') {
        window.showNotification(type, message);
      } else {
        console.log(`Notificação [${type}]: ${message}`);
        alert(message);
      }
    };
    
    // Tentaremos fazer a atualização mesmo sem o botão
    console.log("Continuando sem referência ao botão");
    
    // Preparar dados para envio
    const formData = new FormData();
    formData.append("id", id);
    
    if (novoStatus) {
      formData.append("status", novoStatus);
    }
    
    // Mostrar notificação de carregamento
    safeNotify("info", "Atualizando status do funcionário...");
    
    // Enviar requisição AJAX para alterar o status
    fetch("alterar_status_funcionario.php", {
      method: "POST",
      body: formData,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    })
    .then(response => response.json())
    .then(data => {
      if (data.status === "success") {
        safeNotify("success", data.message);
        // Recarregar a página para mostrar as alterações
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      } else {
        safeNotify("error", data.message || "Erro ao atualizar status do funcionário");
      }
    })
    .catch(error => {
      console.error("Erro:", error);
      safeNotify("error", "Erro de conexão ao atualizar status");
    });
    
    return;
  }
  
  const row = button.closest("tr");
  if (!row) {
    console.error("Não foi possível encontrar a linha do funcionário");
    
    // Se não encontramos a linha, vamos tentar usar apenas o ID para fazer a atualização
    // Isso pode acontecer quando a função é chamada após um refresh da página
    console.log("Tentando atualizar o status sem referência à linha");
    
    // Preparar dados para envio
    const formData = new FormData();
    formData.append("id", id);
    
    if (novoStatus) {
      formData.append("status", novoStatus);
    }
    
    // Mostrar notificação de carregamento
    showNotification("info", "Atualizando status do funcionário...");
    
    // Enviar requisição AJAX para alterar o status
    fetch("alterar_status_funcionario.php", {
      method: "POST",
      body: formData,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    })
    .then(response => response.json())
    .then(data => {
      if (data.status === "success") {
        showNotification("success", data.message);
        
        // Recarregar a tabela para mostrar o status atualizado
        console.log("Recarregando tabela após alterar status com sucesso");
        setTimeout(() => {
          if (typeof window.carregarFuncionarios === "function") {
            window.carregarFuncionarios();
          } else {
            // Fallback - recarregar a página se a função não estiver disponível
            window.location.reload();
          }
        }, 1000); // Pequeno delay para permitir que a notificação seja vista
      } else {
        showNotification("error", data.message || "Erro ao atualizar status do funcionário");
      }
    })
    .catch(error => {
      console.error("Erro:", error);
      showNotification("error", "Erro de conexão ao atualizar status");
    });
    
    return;
  }
  
  row.classList.add("table-warning");

  const statusCell = row.querySelector("td:nth-child(5)");
    if (statusCell) {
      const oldHtml = statusCell.innerHTML;
      statusCell.innerHTML =
        '<div class="spinner-border spinner-border-sm" role="status"></div> Atualizando...';

      // Preparar dados para envio
      const formData = new FormData();
      formData.append("id", id);
      
      // Se um novo status foi especificado, usá-lo
      if (novoStatus) {
        formData.append("status", novoStatus);
        console.log(`Enviando alteração de status para: ${novoStatus}`);
      }

      // Enviar requisição AJAX para alterar o status
      fetch("alterar_status_funcionario.php", {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Erro na resposta do servidor: " + response.status);
          }
          return response.json();
        })
        .then((data) => {
          // Remover classe de destaque
          row.classList.remove("table-warning");

          if (data.status === "success") {
            // Atualizar a exibição do status na tabela
            statusCell.innerHTML = `<span class="badge bg-${data.status_classe}">${data.status_texto}</span>`;

            // Mostrar notificação de sucesso
            showNotification("success", data.message);

            // Atualizar o botão de status - usar o botão original passado como parâmetro
            // ou encontrar na linha
            const statusBtn = button || row.querySelector(".btn-excluir");
            if (statusBtn) {
              // Para botões de excluir/reativar não precisamos alterar o estilo, 
              // apenas atualizar os dados da linha para refletir o novo status
              
              // Atualizar o título conforme o status
              statusBtn.setAttribute(
                "title",
                data.novo_status === "ativo" ? "Inativar" : "Ativar"
              );
              
              console.log("Status do funcionário atualizado com sucesso:", {
                novoStatus: data.novo_status,
                botao: statusBtn
              });
              
              console.log("Botão de status atualizado com sucesso:", {
                novoStatus: data.novo_status,
                novoStatusBtn: statusBtn.getAttribute("data-status"),
                classes: statusBtn.className
              });
            }
          } else {
            // Restaurar o HTML original em caso de erro
            statusCell.innerHTML = oldHtml;

            // Mostrar notificação de erro
            showNotification(
              "error",
              data.message || "Erro ao alterar status do funcionário."
            );
          }
        })
        .catch((error) => {
          console.error("Erro durante a alteração de status:", error);

          // Restaurar o HTML original em caso de erro
          statusCell.innerHTML = oldHtml;

          // Remover classe de destaque
          row.classList.remove("table-warning");

          // Mostrar notificação de erro
          showNotification(
            "error",
            "Erro ao processar a requisição: " + error.message
          );
        });
    }
};
