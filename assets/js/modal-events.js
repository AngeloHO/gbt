// Script para manipular eventos de modais

document.addEventListener("DOMContentLoaded", function () {
  console.log("Modal events script carregado");

  // Função para recarregar event listeners após fechar modais
  function setupModalEvents() {
    // Evento disparado após fechar qualquer modal
    document.querySelectorAll(".modal").forEach(function (modalElement) {
      modalElement.addEventListener("hidden.bs.modal", function () {
        console.log("Modal fechado, recarregando event listeners");

        // Limpar backdrops (adicionado)
        document.body.classList.remove("modal-open");
        document.querySelectorAll(".modal-backdrop").forEach((backdrop) => {
          backdrop.remove();
        });

        // Pequeno delay para garantir que o DOM esteja estável
        setTimeout(function () {
          // Recarregar a tabela para garantir que os dados estão atualizados
          if (typeof window.carregarFuncionarios === "function") {
            window.carregarFuncionarios();
          }

          // Como alternativa, readicionar eventos diretamente
          if (typeof window.adicionarEventosAcoes === "function") {
            window.adicionarEventosAcoes();
          }
        }, 300);
      });

      // Evento disparado quando o modal é aberto
      modalElement.addEventListener("shown.bs.modal", function () {
        console.log("Modal aberto");
      });
    });
  }

  // Configurar eventos iniciais
  setupModalEvents();

  // Observador de mutações para detectar quando novos modais são adicionados ao DOM
  const observer = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
      if (mutation.type === "childList" && mutation.addedNodes.length) {
        mutation.addedNodes.forEach(function (node) {
          if (node.nodeType === Node.ELEMENT_NODE) {
            // Verificar se o nó adicionado é um modal ou contém modais
            const modals =
              node.classList && node.classList.contains("modal")
                ? [node]
                : Array.from(node.querySelectorAll(".modal"));

            if (modals.length) {
              console.log("Novos modais detectados, configurando eventos");
              setupModalEvents();
            }
          }
        });
      }
    });
  });

  // Iniciar observação de mudanças no DOM
  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });
});
