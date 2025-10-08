<?php
require_once '../config/conexao.php'; // Incluir o arquivo de conexão
session_start();
connect_local_mysqli('gebert');
// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: ../index.php');
    exit;
}

// Dados do usuário logado
$user = $_SESSION['user'];
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionários Ativos - Sistema Gebert</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/funcionarios.css">

    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
        }

        .sidebar-link {
            padding: 10px 15px;
            color: #adb5bd;
            text-decoration: none;
            display: block;
            border-left: 3px solid transparent;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background-color: #2c3237;
            color: white;
            border-left-color: #0d6efd;
        }

        .sidebar-link i {
            width: 24px;
            text-align: center;
            margin-right: 5px;
        }

        .content {
            padding: 20px;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 1px;
        }

        .logo-text {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #fff;
        }

        .logo-subtitle {
            font-size: 10px;
            letter-spacing: 1px;
            color: #adb5bd;
        }

        .border-left-primary {
            border-left: 0.25rem solid #007bff !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #28a745 !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #ffc107 !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #17a2b8 !important;
        }

        .text-xs {
            font-size: 0.7rem;
        }

        .fa-2x {
            font-size: 2em;
        }

        .text-gray-300 {
            color: #dddfeb !important;
        }

        .text-gray-800 {
            color: #5a5c69 !important;
        }

        .estoque-baixo {
            background-color: #f8d7da !important;
        }

        .estoque-ok {
            background-color: #d4edda !important;
        }

        /* Melhorias visuais elegantes */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .btn {
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 10px 20px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-success {
            background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
            border: none;
        }

        .btn-info {
            background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);
            border: none;
        }

        .btn-warning {
            background: linear-gradient(135deg, #fdbb2d 0%, #22c1c3 100%);
            border: none;
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .table-dark {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            border-radius: 20px 20px 0 0;
            border-bottom: none;
            padding: 25px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-1px);
        }

        .nav-tabs .nav-link {
            border-radius: 15px 15px 0 0;
            border: none;
            margin-right: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .badge {
            border-radius: 20px;
            padding: 8px 15px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .border-left-primary {
            border-left: 5px solid #667eea !important;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        }

        .border-left-success {
            border-left: 5px solid #44a08d !important;
            background: linear-gradient(135deg, rgba(78, 205, 196, 0.1) 0%, rgba(68, 160, 141, 0.1) 100%);
        }

        .border-left-info {
            border-left: 5px solid #66a6ff !important;
            background: linear-gradient(135deg, rgba(137, 247, 254, 0.1) 0%, rgba(102, 166, 255, 0.1) 100%);
        }

        .sidebar {
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            border-radius: 0 20px 20px 0;
        }

        .sidebar-link {
            border-radius: 10px;
            margin: 5px 10px;
            transition: all 0.3s ease;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transform: translateX(10px);
        }

        .logo-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .alert {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        /* Animações suaves */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeInUp 0.6s ease;
        }

        /* Efeito glass morphism */
        .glass-effect {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="text-center my-4">
                    <div class="logo-text">GEBERT</div>
                    <div class="logo-subtitle">SEGURANÇA PATRIMONIAL</div>
                </div>

                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="sidebar-link" href="../dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="../funcionarios/funcionarios.php">
                                <i class="bi bi-people"></i> Funcionários
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link active" href="avaliacoes.php">
                                <i class="bi bi-star-fill"></i> Avaliações
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="../aso/aso.php">
                                <i class="bi bi-heart-pulse"></i> ASO
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="../epi/epi.php">
                                <i class="bi bi-building"></i> EPI
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="#">
                                <i class="bi bi-clipboard-check"></i> Relatórios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="../desligamento/desligamento.php">
                                <i class="bi bi-person-dash"></i> Desligamentos
                            </a>
                        </li>
                        <li class="nav-item mt-5">
                            <a class="sidebar-link" href="../logout.php">
                                <i class="bi bi-box-arrow-right"></i> Sair
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                    <div>
                        <h1 class="h2 mb-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                            <i class="bi bi-people-fill me-2"></i>Avaliações
                        </h1>
                        <p class="text-muted small mb-0">Lista dos funcionários ativos da empresa</p>
                    </div>
                    <div class="dropdown">
                        <a href="#" class="d-block text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($user['nome'] ?? 'Usuário'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-1"></i> Perfil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-1"></i> Configurações</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../logout.php"><i class="bi bi-box-arrow-right me-1"></i> Sair</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Tabela de Funcionários -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Funcionários Ativos</h5>
                        <span id="totalFuncionarios" class="text-muted"></span>
                        <div id="loading" class="d-none">
                            <i class="bi bi-hourglass-split"></i> Carregando...
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="tabelaFuncionarios">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome Completo</th>
                                        <th>CPF</th>
                                        <th>Função</th>
                                        <th>Telefone</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-funcionarios">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            Carregando funcionários...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal de Avaliação -->
    <div class="modal fade" id="modalEdicao" tabindex="-1" aria-labelledby="modalEdicaoLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEdicaoLabel">
                        <i class="bi bi-clipboard-check"></i> Avaliação de Funcionário
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <form id="formAvaliacao">
                        <!-- Informações Básicas -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="cargo" class="form-label"><strong>Cargo:</strong></label>
                                <input type="text" class="form-control form-control-sm" id="cargo" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="dataFeedback" class="form-label"><strong>Data do Feedback:</strong></label>
                                <input type="date" class="form-control form-control-sm" id="dataFeedback" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="avaliadoPor" class="form-label"><strong>Avaliado por:</strong></label>
                                <input type="text" class="form-control form-control-sm" id="avaliadoPor" placeholder="Nome do coordenador">
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- Abas de Navegação -->
                        <ul class="nav nav-tabs" id="avaliacaoTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="desempenho-tab" data-bs-toggle="tab" data-bs-target="#desempenho" type="button" role="tab">
                                    <i class="bi bi-star"></i> Avaliação de Desempenho
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="feedback-tab" data-bs-toggle="tab" data-bs-target="#feedback" type="button" role="tab">
                                    <i class="bi bi-chat-text"></i> Feedback Qualitativo
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="avaliacaoTabContent">
                            <!-- Aba de Avaliação de Desempenho -->
                            <div class="tab-pane fade show active" id="desempenho" role="tabpanel">
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <h6 class="mb-3 text-primary">Critérios de Avaliação (Escala 1-5)</h6>

                                        <div class="mb-3">
                                            <label class="form-label"><strong>Qualidade do trabalho</strong></label>
                                            <small class="text-muted d-block mb-1">Cumpre as tarefas com atenção e qualidade?</small>
                                            <select class="form-select form-select-sm" name="qualidade_trabalho">
                                                <option value="">Selecione...</option>
                                                <option value="1">1 - Muito Insatisfatório</option>
                                                <option value="2">2 - Insatisfatório</option>
                                                <option value="3">3 - Regular</option>
                                                <option value="4">4 - Satisfatório</option>
                                                <option value="5">5 - Excelente</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label"><strong>Produtividade/entrega</strong></label>
                                            <small class="text-muted d-block mb-1">Cumpre prazos e metas?</small>
                                            <select class="form-select form-select-sm" name="produtividade">
                                                <option value="">Selecione...</option>
                                                <option value="1">1 - Muito Insatisfatório</option>
                                                <option value="2">2 - Insatisfatório</option>
                                                <option value="3">3 - Regular</option>
                                                <option value="4">4 - Satisfatório</option>
                                                <option value="5">5 - Excelente</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label"><strong>Colaboração e trabalho em equipe</strong></label>
                                            <small class="text-muted d-block mb-1">Contribui com colegas e clima da equipe?</small>
                                            <select class="form-select form-select-sm" name="colaboracao">
                                                <option value="">Selecione...</option>
                                                <option value="1">1 - Muito Insatisfatório</option>
                                                <option value="2">2 - Insatisfatório</option>
                                                <option value="3">3 - Regular</option>
                                                <option value="4">4 - Satisfatório</option>
                                                <option value="5">5 - Excelente</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="mb-3 text-primary">&nbsp;</h6>

                                        <div class="mb-3">
                                            <label class="form-label"><strong>Comunicação</strong></label>
                                            <small class="text-muted d-block mb-1">Se expressa de forma clara e respeitosa?</small>
                                            <select class="form-select form-select-sm" name="comunicacao">
                                                <option value="">Selecione...</option>
                                                <option value="1">1 - Muito Insatisfatório</option>
                                                <option value="2">2 - Insatisfatório</option>
                                                <option value="3">3 - Regular</option>
                                                <option value="4">4 - Satisfatório</option>
                                                <option value="5">5 - Excelente</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label"><strong>Comprometimento e responsabilidade</strong></label>
                                            <small class="text-muted d-block mb-1">Demonstra interesse e dedicação?</small>
                                            <select class="form-select form-select-sm" name="comprometimento">
                                                <option value="">Selecione...</option>
                                                <option value="1">1 - Muito Insatisfatório</option>
                                                <option value="2">2 - Insatisfatório</option>
                                                <option value="3">3 - Regular</option>
                                                <option value="4">4 - Satisfatório</option>
                                                <option value="5">5 - Excelente</option>
                                            </select>
                                        </div>

                                        <!-- Resumo da Avaliação -->
                                        <div class="alert alert-light border">
                                            <h6 class="mb-2"><i class="bi bi-calculator"></i> Resumo da Avaliação</h6>
                                            <div id="resumoAvaliacao" class="text-muted">
                                                <small>Preencha as avaliações para ver o resumo</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba de Feedback Qualitativo -->
                            <div class="tab-pane fade" id="feedback" role="tabpanel">
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="pontosFortes" class="form-label"><strong>Pontos fortes</strong></label>
                                                <small class="text-muted d-block mb-1">O que ele faz bem e deve continuar</small>
                                                <textarea class="form-control" id="pontosFortes" rows="4" placeholder="Descreva os pontos fortes..."></textarea>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="pontosMelhoria" class="form-label"><strong>Pontos de melhoria</strong></label>
                                                <small class="text-muted d-block mb-1">Aspectos que precisam ser desenvolvidos</small>
                                                <textarea class="form-control" id="pontosMelhoria" rows="4" placeholder="Pontos a melhorar..."></textarea>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="sugestaoEvolucao" class="form-label"><strong>Sugestões para evolução</strong></label>
                                                <small class="text-muted d-block mb-1">Orientações práticas</small>
                                                <textarea class="form-control" id="sugestaoEvolucao" rows="4" placeholder="Sugestões práticas..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="salvarAvaliacao()">
                        <i class="bi bi-check-circle"></i> Salvar Avaliação
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Histórico de Avaliações -->
    <div class="modal fade" id="modalHistorico" tabindex="-1" aria-labelledby="modalHistoricoLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalHistoricoLabel">
                        <i class="bi bi-clock-history"></i> Histórico de Avaliações
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div id="loadingHistorico" class="text-center py-4 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2">Carregando histórico de avaliações...</p>
                    </div>

                    <!-- Informações do Funcionário -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Funcionário:</strong> <span id="funcionarioNomeHistorico">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Função:</strong> <span id="funcionarioFuncaoHistorico">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Total de Avaliações:</strong> <span id="totalAvaliacoesHistorico">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Abas de Navegação -->
                    <ul class="nav nav-tabs" id="historicoTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="atual-tab" data-bs-toggle="tab" data-bs-target="#avaliacaoAtual" type="button" role="tab">
                                <i class="bi bi-star-fill"></i> Avaliação Atual
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="historico-tab" data-bs-toggle="tab" data-bs-target="#historicoCompleto" type="button" role="tab">
                                <i class="bi bi-clock-history"></i> Histórico Completo
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="historicoTabContent">
                        <!-- Aba de Avaliação Atual -->
                        <div class="tab-pane fade show active" id="avaliacaoAtual" role="tabpanel">
                            <div class="mt-3" id="avaliacaoAtualContent">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Nenhuma avaliação encontrada para este funcionário.
                                </div>
                            </div>
                        </div>

                        <!-- Aba de Histórico Completo -->
                        <div class="tab-pane fade" id="historicoCompleto" role="tabpanel">
                            <div class="mt-3" id="historicoCompletoContent">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Nenhum histórico de avaliações encontrado.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Variável global para armazenar ID do funcionário sendo avaliado
        let funcionarioSendoAvaliado = null;

        // Função para carregar funcionários via AJAX
        function carregarFuncionarios() {
            const loading = document.getElementById('loading');
            const tbody = document.getElementById('tbody-funcionarios');
            const totalSpan = document.getElementById('totalFuncionarios');

            // Mostrar loading
            loading.classList.remove('d-none');

            // Fazer requisição AJAX
            fetch('listar_funcionarios_ajax.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Limpar tbody
                        tbody.innerHTML = '';

                        if (data.data.length > 0) {
                            // Adicionar funcionários na tabela
                            data.data.forEach(funcionario => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td>${funcionario.id}</td>
                                    <td>${funcionario.nome}</td>
                                    <td>${funcionario.cpf}</td>
                                    <td>${funcionario.funcao}</td>
                                    <td>${funcionario.telefone}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="editarFuncionario(${funcionario.id}, '${funcionario.nome}')" title="Avaliar Funcionário">
                                                <i class="bi bi-pencil"></i> Avaliar
                                            </button>
                                            <button type="button" class="btn btn-info btn-sm" onclick="verHistorico(${funcionario.id}, '${funcionario.nome}')" title="Ver Histórico de Avaliações">
                                                <i class="bi bi-clock-history"></i> Histórico
                                            </button>
                                        </div>
                                    </td>
                                `;
                                tbody.appendChild(row);
                            });

                            // Atualizar total
                            totalSpan.textContent = `${data.total} funcionário(s) ativo(s)`;
                        } else {
                            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Nenhum funcionário ativo encontrado</td></tr>';
                            totalSpan.textContent = '0 funcionários ativos';
                        }
                    } else {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Erro ao carregar dados: ' + data.message + '</td></tr>';
                        totalSpan.textContent = 'Erro';
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Erro de conexão</td></tr>';
                    totalSpan.textContent = 'Erro';
                })
                .finally(() => {
                    // Esconder loading
                    loading.classList.add('d-none');
                });
        }

        // Função para abrir modal de avaliação
        function editarFuncionario(id, nome) {
            // Armazenar ID do funcionário
            funcionarioSendoAvaliado = id;

            // Fazer requisição para buscar dados completos do funcionário
            fetch('listar_funcionarios_ajax.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Encontrar o funcionário específico
                        const funcionario = data.data.find(f => f.id == id);
                        if (funcionario) {
                            // Atualizar título do modal
                            document.getElementById('modalEdicaoLabel').innerHTML = `
                                <i class="bi bi-clipboard-check"></i> Avaliação de Funcionário: ${nome}
                            `;

                            // Preencher dados básicos
                            document.getElementById('cargo').value = funcionario.funcao;

                            // Data atual
                            const hoje = new Date();
                            const dataFormatada = hoje.toISOString().split('T')[0];
                            document.getElementById('dataFeedback').value = dataFormatada;

                            // Limpar campos do formulário
                            document.getElementById('avaliadoPor').value = '';
                            document.querySelectorAll('#formAvaliacao select').forEach(select => {
                                select.value = '';
                            });
                            document.getElementById('pontosFortes').value = '';
                            document.getElementById('pontosMelhoria').value = '';
                            document.getElementById('sugestaoEvolucao').value = '';

                            // Adicionar listeners para calcular resumo
                            document.querySelectorAll('#formAvaliacao select[name]').forEach(select => {
                                select.addEventListener('change', calcularResumo);
                            });

                            // Calcular resumo inicial
                            calcularResumo();

                            // Abrir o modal
                            const modal = new bootstrap.Modal(document.getElementById('modalEdicao'));
                            modal.show();
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar dados do funcionário:', error);
                    alert('Erro ao carregar dados do funcionário');
                });
        }

        // Função para calcular resumo da avaliação
        function calcularResumo() {
            const selects = document.querySelectorAll('#formAvaliacao select[name]');
            let total = 0;
            let count = 0;
            let avaliacoes = [];

            selects.forEach(select => {
                if (select.value && select.value !== '') {
                    const valor = parseInt(select.value);
                    total += valor;
                    count++;

                    const nome = select.name.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                    avaliacoes.push(`${nome}: ${valor}`);
                }
            });

            const resumoDiv = document.getElementById('resumoAvaliacao');
            if (count > 0) {
                const media = (total / count).toFixed(1);
                let classificacao = '';
                let corClasse = '';

                if (media >= 4.5) {
                    classificacao = 'Excelente';
                    corClasse = 'text-success';
                } else if (media >= 3.5) {
                    classificacao = 'Satisfatório';
                    corClasse = 'text-primary';
                } else if (media >= 2.5) {
                    classificacao = 'Regular';
                    corClasse = 'text-warning';
                } else {
                    classificacao = 'Insatisfatório';
                    corClasse = 'text-danger';
                }

                resumoDiv.innerHTML = `
                    <div class="row">
                        <div class="col-6">
                            <strong>Média Geral:</strong><br>
                            <span class="h5 ${corClasse}">${media}/5.0</span>
                        </div>
                        <div class="col-6">
                            <strong>Classificação:</strong><br>
                            <span class="h6 ${corClasse}">${classificacao}</span>
                        </div>
                    </div>
                    <small class="text-muted">${count} critério(s) avaliado(s)</small>
                `;
            } else {
                resumoDiv.innerHTML = '<small class="text-muted">Preencha as avaliações para ver o resumo</small>';
            }
        }

        // Função para salvar avaliação
        function salvarAvaliacao() {
            // Validar campos obrigatórios
            const avaliadoPor = document.getElementById('avaliadoPor').value.trim();
            if (!avaliadoPor) {
                alert('Por favor, informe quem está fazendo a avaliação');
                return;
            }

            // Validar se pelo menos uma nota foi dada
            const selects = document.querySelectorAll('#formAvaliacao select[name]');
            let temNota = false;
            selects.forEach(select => {
                if (select.value) temNota = true;
            });

            if (!temNota) {
                alert('Por favor, preencha pelo menos uma avaliação de desempenho');
                return;
            }

            // Verificar se temos ID do funcionário
            if (!funcionarioSendoAvaliado) {
                alert('Erro: ID do funcionário não encontrado');
                return;
            }

            // Coletar dados do formulário
            const dadosAvaliacao = {
                funcionario_id: funcionarioSendoAvaliado,
                avaliado_por: avaliadoPor,
                data_feedback: document.getElementById('dataFeedback').value,
                cargo: document.getElementById('cargo').value,
                qualidade_trabalho: document.querySelector('select[name="qualidade_trabalho"]').value,
                produtividade: document.querySelector('select[name="produtividade"]').value,
                colaboracao: document.querySelector('select[name="colaboracao"]').value,
                comunicacao: document.querySelector('select[name="comunicacao"]').value,
                comprometimento: document.querySelector('select[name="comprometimento"]').value,
                pontos_fortes: document.getElementById('pontosFortes').value.trim(),
                pontos_melhoria: document.getElementById('pontosMelhoria').value.trim(),
                sugestao_evolucao: document.getElementById('sugestaoEvolucao').value.trim()
            };

            // Mostrar loading no botão
            const btnSalvar = document.querySelector('button[onclick="salvarAvaliacao()"]');
            const textoOriginal = btnSalvar.innerHTML;
            btnSalvar.disabled = true;
            btnSalvar.innerHTML = '<i class="bi bi-hourglass-split"></i> Salvando...';

            // Enviar dados via AJAX
            fetch('salvar_avaliacao.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dadosAvaliacao)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Sucesso
                        alert(`✅ ${data.message}\n\nMédia geral: ${data.data.media_geral}/5.0\nCritérios avaliados: ${data.data.criterios_avaliados}`);

                        // Fechar modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEdicao'));
                        modal.hide();

                        // Recarregar lista (opcional)
                        // carregarFuncionarios();
                    } else {
                        // Erro
                        alert(`❌ Erro: ${data.message}`);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('❌ Erro de conexão. Tente novamente.');
                })
                .finally(() => {
                    // Restaurar botão
                    btnSalvar.disabled = false;
                    btnSalvar.innerHTML = textoOriginal;
                });
        }

        // Função para abrir modal de histórico
        function verHistorico(id, nome) {
            // Atualizar título do modal
            document.getElementById('modalHistoricoLabel').innerHTML = `
                <i class="bi bi-clock-history"></i> Histórico de Avaliações: ${nome}
            `;

            // Mostrar loading
            const loading = document.getElementById('loadingHistorico');
            loading.classList.remove('d-none');

            // Limpar conteúdo anterior
            document.getElementById('avaliacaoAtualContent').innerHTML = '';
            document.getElementById('historicoCompletoContent').innerHTML = '';

            // Buscar dados do histórico
            fetch(`buscar_avaliacoes.php?funcionario_id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Preencher informações do funcionário
                        document.getElementById('funcionarioNomeHistorico').textContent = data.data.funcionario.nome;
                        document.getElementById('funcionarioFuncaoHistorico').textContent = data.data.funcionario.funcao;
                        document.getElementById('totalAvaliacoesHistorico').textContent = data.data.total_avaliacoes;

                        // Exibir avaliação atual
                        exibirAvaliacaoAtual(data.data.avaliacao_atual);

                        // Exibir histórico
                        exibirHistoricoCompleto(data.data.historico);

                    } else {
                        // Erro
                        document.getElementById('avaliacaoAtualContent').innerHTML = `
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i> ${data.message}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    document.getElementById('avaliacaoAtualContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> Erro ao carregar histórico
                        </div>
                    `;
                })
                .finally(() => {
                    // Esconder loading
                    loading.classList.add('d-none');
                });

            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById('modalHistorico'));
            modal.show();
        }

        // Função para exibir avaliação atual
        function exibirAvaliacaoAtual(avaliacao) {
            const container = document.getElementById('avaliacaoAtualContent');

            if (!avaliacao) {
                container.innerHTML = `
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Este funcionário ainda não possui avaliações.
                    </div>
                `;
                return;
            }

            container.innerHTML = criarCardAvaliacao(avaliacao, true);
        }

        // Função para exibir histórico completo
        function exibirHistoricoCompleto(historico) {
            const container = document.getElementById('historicoCompletoContent');

            if (!historico || historico.length === 0) {
                container.innerHTML = `
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Não há histórico de avaliações anteriores.
                    </div>
                `;
                return;
            }

            let html = '';
            historico.forEach((avaliacao, index) => {
                html += criarCardAvaliacao(avaliacao, false, index + 2); // Começa do 2º lugar
            });

            container.innerHTML = html;
        }

        // Função para criar card de avaliação
        function criarCardAvaliacao(avaliacao, isAtual = false, posicao = 1) {
            const badge = isAtual ? 'success' : 'secondary';
            const titulo = isAtual ? 'Avaliação Atual' : `${posicao}ª Avaliação`;

            // Cor da classificação
            let corClassificacao = 'text-muted';
            switch (avaliacao.classificacao) {
                case 'Excelente':
                    corClassificacao = 'text-success';
                    break;
                case 'Satisfatório':
                    corClassificacao = 'text-primary';
                    break;
                case 'Regular':
                    corClassificacao = 'text-warning';
                    break;
                case 'Insatisfatório':
                    corClassificacao = 'text-danger';
                    break;
            }

            return `
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-${badge}">${titulo}</span>
                            <strong class="ms-2">${avaliacao.data_feedback}</strong>
                        </div>
                        <div class="text-end">
                            <div class="h5 mb-0 ${corClassificacao}">${avaliacao.media_geral}/5.0</div>
                            <small class="${corClassificacao}">${avaliacao.classificacao}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <small class="text-muted">Avaliado por:</small><br>
                                <strong>${avaliacao.avaliado_por}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Cargo na época:</small><br>
                                <strong>${avaliacao.cargo}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Critérios avaliados:</small><br>
                                <strong>${avaliacao.criterios_avaliados}/5</strong>
                            </div>
                        </div>
                        
                        <!-- Avaliações de Desempenho -->
                        <h6 class="mb-2"><i class="bi bi-star"></i> Avaliações de Desempenho</h6>
                        <div class="row mb-3">
                            ${criarColunaAvaliacao('Qualidade do trabalho', avaliacao.avaliacoes.qualidade_trabalho)}
                            ${criarColunaAvaliacao('Produtividade', avaliacao.avaliacoes.produtividade)}
                            ${criarColunaAvaliacao('Colaboração', avaliacao.avaliacoes.colaboracao)}
                            ${criarColunaAvaliacao('Comunicação', avaliacao.avaliacoes.comunicacao)}
                            ${criarColunaAvaliacao('Comprometimento', avaliacao.avaliacoes.comprometimento)}
                        </div>
                        
                        <!-- Feedback Qualitativo -->
                        <h6 class="mb-2"><i class="bi bi-chat-text"></i> Feedback Qualitativo</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Pontos fortes:</strong>
                                <p class="small">${avaliacao.feedback.pontos_fortes || 'Não informado'}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Pontos de melhoria:</strong>
                                <p class="small">${avaliacao.feedback.pontos_melhoria || 'Não informado'}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Sugestões para evolução:</strong>
                                <p class="small">${avaliacao.feedback.sugestao_evolucao || 'Não informado'}</p>
                            </div>
                        </div>
                        
                        <div class="text-muted text-end">
                            <small>Registrado em: ${avaliacao.data_criacao}</small>
                        </div>
                    </div>
                </div>
            `;
        }

        // Função auxiliar para criar coluna de avaliação
        function criarColunaAvaliacao(titulo, valor) {
            if (!valor) {
                return `
                    <div class="col-md-2 mb-2">
                        <small class="text-muted">${titulo}:</small><br>
                        <span class="text-muted">N/A</span>
                    </div>
                `;
            }

            let cor = 'text-muted';
            if (valor >= 4) cor = 'text-success';
            else if (valor >= 3) cor = 'text-primary';
            else if (valor >= 2) cor = 'text-warning';
            else cor = 'text-danger';

            return `
                <div class="col-md-2 mb-2">
                    <small class="text-muted">${titulo}:</small><br>
                    <span class="h6 ${cor}">${valor}/5</span>
                </div>
            `;
        }

        // Carregar automaticamente ao abrir a página
        document.addEventListener('DOMContentLoaded', function() {
            carregarFuncionarios();
        });
    </script>
</body>

</html>