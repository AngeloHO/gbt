// Arquivo modal-closer.js
// Script simplificado para fechar o modal automaticamente após um cadastro bem-sucedido

document.addEventListener('DOMContentLoaded', function() {
    console.log('Modal Closer inicializado');
    
    // Função para fechar o modal
    function fecharModalFuncionario() {
        const modal = document.getElementById('cadastroFuncionarioModal');
        if (!modal) return;
        
        // Tenta usar Bootstrap
        if (typeof bootstrap !== 'undefined') {
            try {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                    console.log('Modal fechado via Bootstrap API');
                    return;
                }
            } catch (e) {
                console.log('Erro ao usar Bootstrap para fechar:', e);
            }
        }
        
        // Tenta usar jQuery
        if (typeof $ !== 'undefined') {
            try {
                $(modal).modal('hide');
                console.log('Modal fechado via jQuery');
                return;
            } catch (e) {
                console.log('Erro ao usar jQuery para fechar:', e);
            }
        }
        
        // Método direto via DOM
        try {
            modal.style.display = 'none';
            modal.classList.remove('show');
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
            
            console.log('Modal fechado manualmente via DOM');
        } catch (e) {
            console.error('Erro ao fechar modal manualmente:', e);
        }
    }
    
    // Monitora o botão de salvar para detectar cadastro bem-sucedido
    const btnSalvar = document.getElementById('btnSalvarFuncionario');
    if (!btnSalvar) return;
    
    btnSalvar.addEventListener('click', function() {
        console.log('Botão salvar clicado, monitorando resposta...');
        
        // Aguardar por uma notificação de sucesso
        const checkForSuccess = setInterval(function() {
            const toasts = document.querySelectorAll('.toast.bg-success');
            if (toasts.length > 0) {
                console.log('Notificação de sucesso encontrada!');
                clearInterval(checkForSuccess);
                
                // Aguardar um momento para o usuário ver a notificação
                setTimeout(function() {
                    fecharModalFuncionario();
                }, 1500); // 1.5 segundos
            }
        }, 200); // Verificar a cada 200ms
        
        // Parar de verificar após 10 segundos (timeout)
        setTimeout(function() {
            clearInterval(checkForSuccess);
        }, 10000);
    });
    
    console.log('Monitoramento do botão salvar configurado');
});