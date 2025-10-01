// Início do script


document.addEventListener('DOMContentLoaded', function() {
    // Referência ao formulário e modal
    const form = document.getElementById('formCadastroFuncionario');
    const modal = document.getElementById('cadastroFuncionarioModal');
    const modalInstance = bootstrap.Modal.getInstance(modal);
    
    // Elementos DOM frequentemente usados
    const elementos = {
        cpfInput: document.getElementById('cpf'),
        salarioInput: document.getElementById('salario'),
        campoPesquisa: document.getElementById('pesquisaFuncionario'),
        btnPesquisar: document.getElementById('btnPesquisar'),
        btnLimpar: document.getElementById('btnLimpar')
    };
    
    // Carregar lista de funcionários ao iniciar
    carregarFuncionarios();
    
    // Configurar evento de pesquisa
    if (elementos.btnPesquisar) {
        elementos.btnPesquisar.addEventListener('click', function() {
            carregarFuncionarios();
        });
    }
    
    // Pesquisa ao pressionar Enter
    if (elementos.campoPesquisa) {
        elementos.campoPesquisa.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                carregarFuncionarios();
            }
        });
    }
    
    // Converter todos os inputs de texto para maiúsculas
    // Aplica nos campos de texto padrão e também nos campos que podem ser adicionados dinamicamente
    function aplicarMaiusculasEmCampos() {
        document.querySelectorAll('input[type="text"]').forEach(input => {
            // Verificar se já possui o evento (evita duplicação)
            if (!input.dataset.maiusculasAplicadas) {
                input.dataset.maiusculasAplicadas = "true";
                
                // Converte ao digitar
                input.addEventListener('input', function(e) {
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
                input.style.textTransform = 'uppercase';
            }
        });
    }
    
    // Aplica inicialmente e também quando o DOM mudar
    aplicarMaiusculasEmCampos();
    
    // Observer para aplicar em novos elementos que possam ser adicionados dinamicamente
    const observer = new MutationObserver(aplicarMaiusculasEmCampos);
    observer.observe(document.body, { childList: true, subtree: true });
    
    // Notificações
    function showNotification(type, message) {
        console.log(`Notificação [${type}]: ${message}`); // Log para depuração
        
        // Cria um elemento toast para notificação
        const toastContainer = document.createElement('div');
        toastContainer.className = 'position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        
        const toastId = 'toast-' + Date.now();
        
        // Define a cor com base no tipo
        let bgColor = 'bg-primary'; // padrão
        let icon = 'info-circle';
        
        if (type === 'success') {
            bgColor = 'bg-success';
            icon = 'check-circle';
        } else if (type === 'error') {
            bgColor = 'bg-danger';
            icon = 'exclamation-circle';
        } else if (type === 'warning') {
            bgColor = 'bg-warning';
            icon = 'exclamation-triangle';
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
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap não está disponível para mostrar o toast');
            alert(message); // Fallback para alert se Bootstrap não estiver disponível
            return;
        }
        
        const toastElement = document.getElementById(toastId);
        if (!toastElement) {
            console.error('Elemento toast não encontrado');
            return;
        }
        
        try {
            const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
            toast.show();
        } catch (error) {
            console.error('Erro ao mostrar toast:', error);
            alert(message); // Fallback para alert em caso de erro
        }
        
        // Remove o elemento após o toast ser escondido
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastContainer.remove();
        });
    }
    
    // Máscara para CPF
    if (elementos.cpfInput) {
        elementos.cpfInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            
            if (value.length > 9) {
                value = value.replace(/^(\d{3})(\d{3})(\d{3})/, '$1.$2.$3-');
            } else if (value.length > 6) {
                value = value.replace(/^(\d{3})(\d{3})/, '$1.$2.');
            } else if (value.length > 3) {
                value = value.replace(/^(\d{3})/, '$1.');
            }
            
            e.target.value = value;
        });
    }
    
    // Máscara para salário
    if (elementos.salarioInput) {
        elementos.salarioInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            // Converte para número e formata com 2 casas decimais
            if (value) {
                value = (parseInt(value) / 100).toFixed(2);
                value = value.replace('.', ',');
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
            
            e.target.value = value;
        });
    }
    
    // Validações do formulário
    function validateForm() {
        // Validar campos obrigatórios
        const nome = document.getElementById('nome').value.trim();
        const cpf = document.getElementById('cpf').value.trim();
        const rg = document.getElementById('rg').value.trim();
        
        let isValid = true;
        let message = '';
        
        if (!nome) {
            isValid = false;
            message += 'Nome completo é obrigatório.<br>';
            document.getElementById('nome').classList.add('is-invalid');
        } else {
            document.getElementById('nome').classList.remove('is-invalid');
        }
        
        if (!cpf) {
            isValid = false;
            message += 'CPF é obrigatório.<br>';
            document.getElementById('cpf').classList.add('is-invalid');
        } else {
            document.getElementById('cpf').classList.remove('is-invalid');
        }
        
        if (!rg) {
            isValid = false;
            message += 'RG é obrigatório.<br>';
            document.getElementById('rg').classList.add('is-invalid');
        } else {
            document.getElementById('rg').classList.remove('is-invalid');
        }
        
        return { isValid, message };
    }

    // Máscara para telefone
    const telefoneInput = document.getElementById('telefone');
    if (telefoneInput) {
        // Adicionar ao objeto elementos
        elementos.telefoneInput = telefoneInput;
        
        telefoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            
            if (value.length > 10) {
                value = value.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (value.length > 6) {
                value = value.replace(/^(\d{2})(\d{4})/, '($1) $2-');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})/, '($1) ');
            }
            
            e.target.value = value;
        });
    }

    // Máscara para CEP - Apenas formatação simples
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 8) value = value.slice(0, 8);
            
            if (value.length > 5) {
                value = value.replace(/^(\d{5})(\d{3})/, '$1-$2');
            }
            
            e.target.value = value;
        });
    }

    // Botão limpar formulário
    const btnLimpar = document.getElementById('btnLimpar');
    if (btnLimpar) {
        btnLimpar.addEventListener('click', function() {
            document.getElementById('formCadastroFuncionario').reset();
            
            // Remove classes de validação
            const inputs = document.querySelectorAll('.is-invalid');
            inputs.forEach(input => {
                input.classList.remove('is-invalid');
            });
        });
    }
    
    // Submissão do formulário
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar o formulário
            const validation = validateForm();
            if (!validation.isValid) {
                showNotification('error', validation.message);
                return;
            }
            
            // Desabilitar botão para evitar múltiplos envios
            const btnSalvar = document.getElementById('btnSalvarFuncionario');
            const btnSalvarOriginalText = btnSalvar.innerHTML;
            btnSalvar.disabled = true;
            btnSalvar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';
            
            // Preparar dados para envio
            const formData = new FormData(form);
            
            // Enviar requisição AJAX
            fetch(form.getAttribute('action'), {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Habilitar botão novamente
                btnSalvar.disabled = false;
                btnSalvar.innerHTML = btnSalvarOriginalText;
                
                if (data.status === 'success') {
                    showNotification('success', data.message);
                    
                    // Limpar formulário
                    form.reset();
                    
                    // Fechar o modal após um breve delay
                    setTimeout(() => {
                        const modalElement = document.getElementById('cadastroFuncionarioModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        modalInstance.hide();
                        
                        // Recarregar a tabela de funcionários (se existir)
                        if (typeof carregarFuncionarios === 'function') {
                            carregarFuncionarios();
                        } else {
                            // Ou recarregar a página após um breve delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        }
                    }, 1500);
                } else {
                    showNotification('error', data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('error', 'Erro ao processar a requisição. Tente novamente.');
                
                // Habilitar botão novamente
                btnSalvar.disabled = false;
                btnSalvar.innerHTML = btnSalvarOriginalText;
            });
        });
    }

    // Navegação entre abas
    const tabs = document.querySelectorAll('#cadastroTab button');
    const nextButtons = document.querySelectorAll('.btn-next-tab');
    const prevButtons = document.querySelectorAll('.btn-prev-tab');

    if (nextButtons) {
        nextButtons.forEach(button => {
            button.addEventListener('click', function() {
                const currentTab = document.querySelector('.tab-pane.active');
                const currentTabId = currentTab.getAttribute('id');
                
                let nextTabIndex;
                for (let i = 0; i < tabs.length; i++) {
                    if (tabs[i].getAttribute('aria-controls') === currentTabId) {
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
        prevButtons.forEach(button => {
            button.addEventListener('click', function() {
                const currentTab = document.querySelector('.tab-pane.active');
                const currentTabId = currentTab.getAttribute('id');
                
                let prevTabIndex;
                for (let i = 0; i < tabs.length; i++) {
                    if (tabs[i].getAttribute('aria-controls') === currentTabId) {
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
    window.carregarFuncionarios = function(pagina = 1) {
        const tabelaBody = document.querySelector('#tabelaFuncionarios tbody');
        const totalRegistros = document.getElementById('totalRegistros');
        const paginacaoLinks = document.getElementById('paginacaoLinks');
        
        // Usa o elemento do objeto centralizado para evitar problemas
        const campoPesquisa = elementos.campoPesquisa || document.getElementById('pesquisaFuncionario');
        
        if (!tabelaBody) {
            console.error('Tabela de funcionários não encontrada no DOM');
            return;
        }
        
        // Exibir indicador de carregamento
        tabelaBody.innerHTML = '<tr><td colspan="6" class="text-center"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Carregando...</td></tr>';
        
        // Preparar parâmetros da busca
        const busca = campoPesquisa ? campoPesquisa.value.trim() : '';
        const limite = 10; // Registros por página
        
        // Fazer requisição AJAX
        fetch(`listar_funcionarios.php?pagina=${pagina}&limite=${limite}&busca=${encodeURIComponent(busca)}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const funcionarios = data.funcionarios;
                    
                    // Atualizar contagem de registros
                    if (totalRegistros) {
                        totalRegistros.textContent = data.paginacao.total_registros;
                    }
                    
                    // Limpar tabela
                    tabelaBody.innerHTML = '';
                    
                    if (funcionarios.length === 0) {
                        tabelaBody.innerHTML = '<tr><td colspan="6" class="text-center">Nenhum funcionário encontrado</td></tr>';
                        return;
                    }
                    
                    // Preencher tabela com os funcionários
                    funcionarios.forEach(funcionario => {
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
                        
                        let html = '';
                        
                        // Botão anterior
                        html += `
                            <li class="page-item ${paginaAtual === 1 ? 'disabled' : ''}">
                                <button class="page-link" data-pagina="${paginaAtual - 1}" ${paginaAtual === 1 ? 'disabled' : ''}>
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
                                <li class="page-item ${i === paginaAtual ? 'active' : ''}">
                                    <button class="page-link" data-pagina="${i}">${i}</button>
                                </li>
                            `;
                        }
                        
                        // Botão próximo
                        html += `
                            <li class="page-item ${paginaAtual === totalPaginas ? 'disabled' : ''}">
                                <button class="page-link" data-pagina="${paginaAtual + 1}" ${paginaAtual === totalPaginas ? 'disabled' : ''}>
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </li>
                        `;
                        
                        paginacaoLinks.innerHTML = html;
                        
                        // Adicionar evento aos links de paginação
                        document.querySelectorAll('#paginacaoLinks .page-link').forEach(link => {
                            link.addEventListener('click', function() {
                                if (this.hasAttribute('disabled')) return;
                                
                                const pagina = parseInt(this.getAttribute('data-pagina'));
                                carregarFuncionarios(pagina);
                            });
                        });
                    }
                    
                    // Adicionar eventos aos botões de ação
                    document.querySelectorAll('.btn-visualizar').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const id = this.getAttribute('data-id');
                            // Implementar visualização detalhada
                            alert('Visualizar funcionário ID: ' + id);
                        });
                    });
                    
                    document.querySelectorAll('.btn-editar').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const id = this.getAttribute('data-id');
                            // Implementar edição
                            alert('Editar funcionário ID: ' + id);
                        });
                    });
                    
                    document.querySelectorAll('.btn-excluir').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const id = this.getAttribute('data-id');
                            if (confirm('Tem certeza que deseja excluir este funcionário?')) {
                                // Implementar exclusão
                                alert('Excluir funcionário ID: ' + id);
                            }
                        });
                    });
                } else {
                    tabelaBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Erro ao carregar funcionários: ${data.message}</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                tabelaBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Erro ao carregar funcionários. Tente novamente.</td></tr>';
            });
    };
});