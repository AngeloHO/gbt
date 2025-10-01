// Script simplificado para fechar o modal quando o cadastro for bem-sucedido
// Esse script não interfere com o funcionamento normal do restante da aplicação

document.addEventListener('DOMContentLoaded', function() {
    console.log('[Modal Fix] Script de fechamento de modal carregado');

    // Função para fechar o modal Bootstrap
    function fecharModal() {
        const modalElement = document.getElementById('cadastroFuncionarioModal');
        if (!modalElement) {
            console.log('[Modal Fix] Modal não encontrado no DOM');
            return;
        }
        
        console.log('[Modal Fix] Tentando fechar o modal...');
        
        // Método 1: Usando Bootstrap
        try {
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
                console.log('[Modal Fix] Modal fechado via Bootstrap API');
                return;
            } else {
                console.log('[Modal Fix] Instância do modal não encontrada via Bootstrap');
            }
        } catch (e) {
            console.log('[Modal Fix] Erro ao fechar com Bootstrap:', e);
        }
        
        // Método 2: Manipulação direta do DOM
        try {
            modalElement.style.display = 'none';
            modalElement.classList.remove('show');
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            // Remover backdrop
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function(backdrop) {
                backdrop.classList.remove('show');
                setTimeout(function() {
                    backdrop.remove();
                }, 200);
            });
            
            console.log('[Modal Fix] Modal fechado manualmente via DOM');
        } catch (e) {
            console.error('[Modal Fix] Erro ao fechar modal manualmente:', e);
        }
    }
    
    // Também adicionar evento ao botão salvar como backup
    const btnSalvar = document.getElementById('btnSalvarFuncionario');
    if (btnSalvar) {
        console.log('[Modal Fix] Adicionando evento de clique ao botão salvar');
        btnSalvar.addEventListener('click', function() {
            // Verificar periodicamente por notificações de sucesso
            let checkCount = 0;
            const maxChecks = 50; // 10 segundos (50 * 200ms)
            
            const checkInterval = setInterval(function() {
                checkCount++;
                const toasts = document.querySelectorAll('.toast.bg-success');
                
                if (toasts.length > 0) {
                    console.log('[Modal Fix] Notificação de sucesso encontrada após clique no botão salvar');
                    clearInterval(checkInterval);
                    setTimeout(fecharModal, 1500);
                }
                
                if (checkCount >= maxChecks) {
                    clearInterval(checkInterval);
                }
            }, 200);
        });
    } else {
        console.log('[Modal Fix] Botão salvar não encontrado');
    }
    
    // Monitorar a criação de notificações de sucesso
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type !== 'childList') return;
            
            // Verificar se uma notificação de sucesso foi adicionada
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType !== Node.ELEMENT_NODE) return;
                
                // Buscar toasts de sucesso ou elementos com mensagem de sucesso
                if (node.classList && node.classList.contains('toast') && node.classList.contains('bg-success')) {
                    console.log('[Modal Fix] Toast de sucesso detectado via MutationObserver');
                    setTimeout(fecharModal, 1500); // Fechar após 1.5 segundos
                }
                
                // Buscar dentro do nó também
                const sucessElements = node.querySelectorAll('.toast.bg-success, .alert-success');
                if (sucessElements.length > 0) {
                    console.log('[Modal Fix] Elemento de sucesso detectado dentro do nó via MutationObserver');
                    setTimeout(fecharModal, 1500);
                }
            });
        });
    });
    
    // Configurar o observer para monitorar todo o corpo do documento
    observer.observe(document.body, { 
        childList: true,
        subtree: true
    });
    
    console.log('[Modal Fix] Monitoramento de notificações de sucesso inicializado');
});