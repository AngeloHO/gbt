// Script para fechar o modal quando o cadastro for bem-sucedido
// Este script também limpa completamente o backdrop (fundo escuro) das modais

document.addEventListener("DOMContentLoaded", function () {
  console.log("[Modal Fix] Script de fechamento de modal carregado");

  // Função para fechar o modal Bootstrap e limpar backdops
  window.fecharModalCompleto = function (modalId) {
    const modalElement = document.getElementById(
      modalId || "cadastroFuncionarioModal"
    );
    if (!modalElement) {
      console.log("[Modal Fix] Modal não encontrado no DOM");
      return;
    }

    console.log("[Modal Fix] Tentando fechar o modal e limpar backdrop...");

    // Método 1: Usando Bootstrap
    try {
      const modalInstance = bootstrap.Modal.getInstance(modalElement);
      if (modalInstance) {
        modalInstance.hide();
        console.log("[Modal Fix] Modal fechado via Bootstrap API");
      } else {
        console.log(
          "[Modal Fix] Instância do modal não encontrada via Bootstrap"
        );
      }
    } catch (e) {
      console.log("[Modal Fix] Erro ao fechar com Bootstrap:", e);
    }

    // Método 2: Garantir limpeza completa do DOM após um pequeno delay
    setTimeout(function () {
      try {
        // Remover todas as classes relacionadas à modal do body
        document.body.classList.remove("modal-open");
        document.body.style.overflow = "";
        document.body.style.paddingRight = "";

        // Remover backdrop manualmente
        const backdrops = document.querySelectorAll(".modal-backdrop");
        backdrops.forEach(function (backdrop) {
          backdrop.classList.remove("show");
          backdrop.classList.remove("fade");
          backdrop.remove();
        });

        // Garantir que o modal esteja escondido
        modalElement.style.display = "none";
        modalElement.classList.remove("show");
        modalElement.setAttribute("aria-hidden", "true");

        // Limpar formulários dentro do modal
        const forms = modalElement.querySelectorAll("form");
        forms.forEach(function (form) {
          form.reset();

          // Limpar campos escondidos também
          const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
          hiddenInputs.forEach(function (input) {
            if (input.id !== "acao") {
              input.value = "";
            }
          });

          console.log("[Modal Fix] Formulário resetado dentro do modal");
        });

        console.log(
          "[Modal Fix] Limpeza completa do backdrop e modal realizada"
        );

        // Recarregar a tabela e eventos após fechamento
        if (typeof window.carregarFuncionarios === "function") {
          window.carregarFuncionarios();
        }
      } catch (e) {
        console.error("[Modal Fix] Erro ao limpar modal e backdrop:", e);
      }
    }, 300); // Pequeno delay para garantir que o Bootstrap termine seu processamento
  };

  // Adicionar evento aos botões de fechar modal
  document
    .querySelectorAll('[data-bs-dismiss="modal"]')
    .forEach(function (btn) {
      btn.addEventListener("click", function () {
        console.log("[Modal Fix] Botão de fechar modal clicado");
        const modalId = btn.closest(".modal").id;
        setTimeout(function () {
          window.fecharModalCompleto(modalId);
        }, 300);
      });
    });

  // Também adicionar evento ao botão salvar como backup
  const btnSalvar = document.getElementById("btnSalvarFuncionario");
  if (btnSalvar) {
    console.log("[Modal Fix] Adicionando evento de clique ao botão salvar");
    btnSalvar.addEventListener("click", function () {
      // Verificar periodicamente por notificações de sucesso
      let checkCount = 0;
      const maxChecks = 50; // 10 segundos (50 * 200ms)

      const checkInterval = setInterval(function () {
        checkCount++;
        const toasts = document.querySelectorAll(
          ".toast.bg-success, .alert-success"
        );

        if (toasts.length > 0) {
          console.log(
            "[Modal Fix] Notificação de sucesso encontrada após clique no botão salvar"
          );
          clearInterval(checkInterval);
          setTimeout(function () {
            window.fecharModalCompleto();
          }, 1500);
        }

        if (checkCount >= maxChecks) {
          clearInterval(checkInterval);
        }
      }, 200);
    });
  } else {
    console.log("[Modal Fix] Botão salvar não encontrado");
  }

  // Monitorar a criação de notificações de sucesso
  const observer = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
      if (mutation.type !== "childList") return;

      // Verificar se uma notificação de sucesso foi adicionada
      mutation.addedNodes.forEach(function (node) {
        if (node.nodeType !== Node.ELEMENT_NODE) return;

        // Buscar toasts de sucesso ou elementos com mensagem de sucesso
        if (
          node.classList &&
          ((node.classList.contains("toast") &&
            node.classList.contains("bg-success")) ||
            node.classList.contains("alert-success") ||
            node.textContent.toLowerCase().includes("sucesso"))
        ) {
          console.log(
            "[Modal Fix] Notificação de sucesso detectada via MutationObserver"
          );
          setTimeout(function () {
            window.fecharModalCompleto();
          }, 1500); // Fechar após 1.5 segundos
        }

        // Buscar dentro do nó também
        const sucessElements = node.querySelectorAll(
          ".toast.bg-success, .alert-success"
        );
        if (sucessElements.length > 0) {
          console.log(
            "[Modal Fix] Elemento de sucesso detectado dentro do nó via MutationObserver"
          );
          setTimeout(function () {
            window.fecharModalCompleto();
          }, 1500);
        }
      });
    });
  });

  // Configurar o observer para monitorar todo o corpo do documento
  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });

  console.log(
    "[Modal Fix] Monitoramento de notificações de sucesso inicializado"
  );

  // Adicionar manipulação especial para eventos de modal do Bootstrap
  document.addEventListener("hidden.bs.modal", function (event) {
    console.log(
      "[Modal Fix] Evento hidden.bs.modal detectado, garantindo limpeza"
    );

    // Limpar backdrop e outras classes após fechamento do modal
    setTimeout(function () {
      window.fecharModalCompleto(event.target.id);
    }, 100);
  });

  // Adicionar evento para limpar formulários quando qualquer modal for aberto
  document.addEventListener("show.bs.modal", function (event) {
    console.log("[Modal Fix] Modal sendo aberto, preparando formulário");
    const modalElement = event.target;

    // Se for o modal de cadastro de funcionário e não estiver em modo de edição
    if (modalElement.id === "cadastroFuncionarioModal") {
      // Verificar se estamos em modo de edição checando o data-mode do formulário
      const form = modalElement.querySelector("form");
      const isEditMode = form && form.getAttribute("data-mode") === "edit";

      // Também verificar se já existe um ID configurado
      const idInput = modalElement.querySelector('input[name="id"]');

      console.log("[Modal Fix] Estado do modal:", {
        isEditMode: isEditMode,
        hasIdField: idInput != null,
        idValue: idInput ? idInput.value : "não existe",
      });

      // Se não estamos em modo de edição e não temos um ID, então é um novo cadastro
      if (!isEditMode && (!idInput || !idInput.value)) {
        console.log(
          "[Modal Fix] Modal aberto para novo cadastro, limpando formulário"
        );

        // Se não tem ID ou não estamos em modo de edição, estamos criando novo - limpar o formulário
        const forms = modalElement.querySelectorAll("form");
        forms.forEach(function (form) {
          form.reset();

          // Limpar campos escondidos também
          const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
          hiddenInputs.forEach(function (input) {
            if (input.id !== "acao") {
              input.value = "";
            }
          });

          // Definir o valor da ação para cadastrar
          const acaoInput = form.querySelector("#acao");
          if (acaoInput) {
            acaoInput.value = "cadastrar";
          }

          // Definir o modo do formulário
          form.setAttribute("data-mode", "create");

          console.log(
            "[Modal Fix] Formulário resetado para cadastro de novo funcionário"
          );
        });
      } else {
        console.log(
          "[Modal Fix] Modal aberto para edição, mantendo os dados do formulário"
        );
      }
    }
  });
});
