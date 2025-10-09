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
    <title>ASO - Sistema Gebert</title>

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

        /* Conversão automática para maiúsculo em inputs */
        .form-control.uppercase,
        textarea.uppercase {
            text-transform: uppercase;
        }
        
        /* Estilo visual uppercase para selects (sem interferir na funcionalidade) */
        .form-select {
            text-transform: uppercase;
        }
        
        /* Efeito glass morphism */
        .glass-effect {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        /* Timeline Styles */
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -19px;
            top: 10px;
            bottom: -30px;
            width: 2px;
            background: linear-gradient(to bottom, #667eea, #764ba2);
        }

        .timeline-item:last-child::before {
            bottom: auto;
            height: 20px;
        }

        .timeline-item-current::before {
            background: linear-gradient(to bottom, #28a745, #20c997);
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
        }

        .timeline-marker {
            position: absolute;
            left: -30px;
            top: 5px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            font-size: 12px;
        }

        .timeline-marker-active {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            box-shadow: 0 3px 10px rgba(40, 167, 69, 0.4);
            animation: pulse 2s infinite;
        }

        .timeline-marker-inactive {
            background: #6c757d;
            color: white;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
        }

        .timeline-content {
            margin-left: 15px;
        }

        /* Melhorias nas tabs */
        .nav-tabs .nav-link {
            border-radius: 15px 15px 0 0 !important;
            border: none !important;
            margin-right: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
            background: rgba(102, 126, 234, 0.1);
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .nav-tabs .nav-link:hover:not(.active) {
            background: rgba(102, 126, 234, 0.2);
            transform: translateY(-1px);
        }

        /* Cards do histórico */
        .timeline-item .card {
            transition: all 0.3s ease;
            border-radius: 15px;
        }

        .timeline-item .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .timeline-item .card.border-primary {
            border-width: 2px !important;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
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
                            <a class="sidebar-link " href="../avaliacoes/avaliacoes.php">
                                <i class="bi bi-star-fill"></i> Avaliações
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link " href="../aso/aso.php">
                                <i class="bi bi-heart-pulse"></i> ASO
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="../epi/epi.php">
                                <i class="bi bi-building"></i> EPI
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link active" href="desligamento.php">
                                <i class="bi bi-person-dash"></i> Desligamentos
                            </a>
                        </li>
                        <li class="nav-item mt-5">
                            <a class="sidebar-link " href="../logout.php">
                                <i class="bi bi-box-arrow-right"></i> Sair
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                    <div>
                        <h1 class="h2 mb-0"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                            <i class="bi bi-person-dash me-2"></i>Controle de Desligamentos   
                        </h1>
                        <p class="text-muted small mb-0">Gestão de Desligamentos de Funcionários</p>
                    </div>
                    <div class="dropdown">
                        <a href="#" class="d-block text-decoration-none dropdown-toggle" id="dropdownUser"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i>
                            <?php echo htmlspecialchars($user['nome'] ?? 'Usuário'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-1"></i> Perfil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-1"></i> Configurações</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../logout.php"><i class="bi bi-box-arrow-right me-1"></i>
                                    Sair</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Cards de Estatísticas -->
                <div class="row mb-4">
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-primary h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total de Desligamentos
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-desligamentos">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-person-x-fill fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-warning h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Pendentes
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendentes">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-exclamation-triangle-fill fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-success h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Mês Atual
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="mes-atual">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-calendar-check-fill fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Gestão de Desligamentos</h3>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovoDesligamento">
                                <i class="bi bi-plus-circle me-1"></i> Novo Desligamento
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="filtroStatus" class="form-label">Status</label>
                                <select class="form-select" id="filtroStatus">
                                    <option value="">Todos</option>
                                    <option value="solicitado">Solicitado</option>
                                    <option value="em_andamento">Em Andamento</option>
                                    <option value="finalizado">Finalizado</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filtroTipo" class="form-label">Tipo</label>
                                <select class="form-select" id="filtroTipo">
                                    <option value="">Todos</option>
                                    <option value="demissao_sem_justa_causa">Demissão sem Justa Causa</option>
                                    <option value="demissao_com_justa_causa">Demissão com Justa Causa</option>
                                    <option value="pedido_demissao">Pedido de Demissão</option>
                                    <option value="termino_contrato">Término de Contrato</option>
                                    <option value="aposentadoria">Aposentadoria</option>
                                    <option value="morte">Morte</option>
                                    <option value="acordo_mutuo">Acordo Mútuo</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="dataInicio" class="form-label">Data Início</label>
                                <input type="date" class="form-control" id="dataInicio">
                            </div>
                            <div class="col-md-2">
                                <label for="dataFim" class="form-label">Data Fim</label>
                                <input type="date" class="form-control" id="dataFim">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-primary w-100" onclick="aplicarFiltros()">
                                    <i class="bi bi-search"></i> Filtrar
                                </button>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-10">
                                <input type="text" class="form-control uppercase" id="busca" placeholder="BUSCAR POR NOME, CPF OU MOTIVO...">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="limparFiltros()">
                                    <i class="bi bi-x-circle"></i> Limpar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Desligamentos -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Lista de Desligamentos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="tabelaDesligamentos">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Funcionário</th>
                                        <th>Tipo</th>
                                        <th>Data Solicitação</th>
                                        <th>Data Desligamento</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="corpoTabela">
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="spinner-border" role="status">
                                                <span class="visually-hidden">Carregando...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginação -->
                        <nav aria-label="Navegação da tabela">
                            <ul class="pagination justify-content-center" id="paginacao">
                                <!-- Será preenchido via JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Novo Desligamento -->
    <div class="modal fade" id="modalNovoDesligamento" tabindex="-1" aria-labelledby="modalNovoDesligamentoLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovoDesligamentoLabel">
                        <i class="bi bi-person-x me-2"></i>Novo Desligamento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNovoDesligamento">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="funcionario" class="form-label">Funcionário *</label>
                                    <select class="form-select" id="funcionario" name="funcionario_id" required>
                                        <option value="">Selecione um funcionário</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="dataSolicitacao" class="form-label">Data Solicitação *</label>
                                    <input type="date" class="form-control" id="dataSolicitacao" name="data_solicitacao" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="dataDesligamento" class="form-label">Data Desligamento *</label>
                                    <input type="date" class="form-control" id="dataDesligamento" name="data_desligamento" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipoDesligamento" class="form-label">Tipo de Desligamento *</label>
                                    <select class="form-select" id="tipoDesligamento" name="tipo_desligamento" required>
                                        <option value="">Selecione o tipo</option>
                                        <option value="demissao_sem_justa_causa">Demissão sem Justa Causa</option>
                                        <option value="demissao_com_justa_causa">Demissão com Justa Causa</option>
                                        <option value="pedido_demissao">Pedido de Demissão</option>
                                        <option value="termino_contrato">Término de Contrato</option>
                                        <option value="aposentadoria">Aposentadoria</option>
                                        <option value="morte">Morte</option>
                                        <option value="acordo_mutuo">Acordo Mútuo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="avisoPrevio" class="form-label">Aviso Prévio</label>
                                    <select class="form-select" id="avisoPrevio" name="aviso_previo">
                                        <option value="nao_aplicavel">Não Aplicável</option>
                                        <option value="trabalhado">Trabalhado</option>
                                        <option value="indenizado">Indenizado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="diasAvisoPrevio" class="form-label">Dias Aviso Prévio</label>
                                    <input type="number" class="form-control" id="diasAvisoPrevio" name="dias_aviso_previo" value="0" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="motivo" class="form-label">Motivo</label>
                                    <textarea class="form-control uppercase" id="motivo" name="motivo" rows="3" placeholder="DESCREVA O MOTIVO DO DESLIGAMENTO..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="observacoes" class="form-label">Observações</label>
                                    <textarea class="form-control uppercase" id="observacoes" name="observacoes" rows="3" placeholder="OBSERVAÇÕES ADICIONAIS..."></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarDesligamento()">
                        <i class="bi bi-save me-1"></i>Salvar Desligamento
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Visualizar Desligamento -->
    <div class="modal fade" id="modalVisualizarDesligamento" tabindex="-1" aria-labelledby="modalVisualizarDesligamentoLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVisualizarDesligamentoLabel">
                        <i class="bi bi-eye me-2"></i>Detalhes do Desligamento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="conteudoVisualizacao">
                    <!-- Será preenchido via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Desligamento -->
    <div class="modal fade" id="modalEditarDesligamento" tabindex="-1" aria-labelledby="modalEditarDesligamentoLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarDesligamentoLabel">
                        <i class="bi bi-pencil me-2"></i>Editar Desligamento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarDesligamento">
                        <input type="hidden" id="editDesligamentoId" name="desligamento_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editFuncionario" class="form-label">Funcionário *</label>
                                    <select class="form-select" id="editFuncionario" name="funcionario_id" required>
                                        <option value="">Selecione um funcionário</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="editDataSolicitacao" class="form-label">Data Solicitação *</label>
                                    <input type="date" class="form-control" id="editDataSolicitacao" name="data_solicitacao" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="editDataDesligamento" class="form-label">Data Desligamento *</label>
                                    <input type="date" class="form-control" id="editDataDesligamento" name="data_desligamento" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editTipoDesligamento" class="form-label">Tipo de Desligamento *</label>
                                    <select class="form-select" id="editTipoDesligamento" name="tipo_desligamento" required>
                                        <option value="">Selecione o tipo</option>
                                        <option value="demissao_sem_justa_causa">Demissão sem Justa Causa</option>
                                        <option value="demissao_com_justa_causa">Demissão com Justa Causa</option>
                                        <option value="pedido_demissao">Pedido de Demissão</option>
                                        <option value="termino_contrato">Término de Contrato</option>
                                        <option value="aposentadoria">Aposentadoria</option>
                                        <option value="morte">Morte</option>
                                        <option value="acordo_mutuo">Acordo Mútuo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="editAvisoPrevio" class="form-label">Aviso Prévio</label>
                                    <select class="form-select" id="editAvisoPrevio" name="aviso_previo">
                                        <option value="nao_aplicavel">Não Aplicável</option>
                                        <option value="trabalhado">Trabalhado</option>
                                        <option value="indenizado">Indenizado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="editDiasAvisoPrevio" class="form-label">Dias Aviso Prévio</label>
                                    <input type="number" class="form-control" id="editDiasAvisoPrevio" name="dias_aviso_previo" value="0" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="editMotivo" class="form-label">Motivo</label>
                                    <textarea class="form-control uppercase" id="editMotivo" name="motivo" rows="3" placeholder="DESCREVA O MOTIVO DO DESLIGAMENTO..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="editObservacoes" class="form-label">Observações</label>
                                    <textarea class="form-control uppercase" id="editObservacoes" name="observacoes" rows="3" placeholder="OBSERVAÇÕES ADICIONAIS..."></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="atualizarDesligamento()">
                        <i class="bi bi-save me-1"></i>Atualizar Desligamento
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Máscara para inputs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <script>
        // Variáveis globais
        let paginaAtual = 1;
        let totalPaginas = 1;
        
        // Inicialização da página
        $(document).ready(function() {
            carregarEstatisticas();
            carregarFuncionarios();
            carregarDesligamentos();
            
            // Define data de hoje como padrão
            const hoje = new Date().toISOString().split('T')[0];
            $('#dataSolicitacao').val(hoje);
            
            // Conversão automática para maiúsculo
            inicializarConversaoMaiusculo();
            
            // Filtros em tempo real
            $('#busca').on('keyup', function() {
                if ($(this).val().length >= 3 || $(this).val().length === 0) {
                    carregarDesligamentos();
                }
            });
            
            // Evento para limpar o formulário de edição quando o modal for aberto
            $('#modalEditarDesligamento').on('show.bs.modal', function() {
                document.getElementById('formEditarDesligamento').reset();
                $('#editFuncionario').html('<option value="">Carregando funcionários...</option>');
            });
        });
        
        // Função para inicializar conversão automática para maiúsculo
        function inicializarConversaoMaiusculo() {
            // Aplica a conversão apenas em inputs de texto e textareas com classe uppercase
            // Exclui campos select, date, number, etc.
            $(document).on('input keyup', 'input[type="text"].uppercase, textarea.uppercase', function() {
                const element = $(this);
                const cursorPosition = element[0].selectionStart;
                const value = element.val().toUpperCase();
                element.val(value);
                
                // Restaura a posição do cursor
                if (element[0].setSelectionRange) {
                    element[0].setSelectionRange(cursorPosition, cursorPosition);
                }
            });
            
            // Para inputs que não são text, remove a classe uppercase para evitar conflitos
            $('input:not([type="text"]), select').removeClass('uppercase');
        }
        
        // Função para carregar estatísticas
        function carregarEstatisticas() {
            $.ajax({
                url: 'buscar_estatisticas_desligamento.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        const stats = response.data;
                        $('#total-desligamentos').text(stats.total_desligamentos || 0);
                        $('#pendentes').text(stats.pendentes || 0);
                        $('#mes-atual').text(stats.mes_atual || 0);
                    }
                },
                error: function() {
                    console.error('Erro ao carregar estatísticas');
                }
            });
        }
        
        // Função para carregar funcionários no select
        function carregarFuncionarios() {
            $.ajax({
                url: 'listar_funcionarios_desligamento.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        const select = $('#funcionario');
                        select.empty().append('<option value="">Selecione um funcionário</option>');
                        
                        response.data.forEach(function(funcionario) {
                            select.append(`<option value="${funcionario.id}" data-salario="${funcionario.salario_numerico}">
                                ${funcionario.nome} - ${funcionario.cpf} (${funcionario.funcao})
                            </option>`);
                        });
                    }
                },
                error: function() {
                    mostrarAlerta('Erro ao carregar funcionários', 'error');
                }
            });
        }
        
        // Função para carregar desligamentos
        function carregarDesligamentos() {
            const filtros = {
                pagina: paginaAtual,
                limite: 10,
                busca: $('#busca').val(),
                status: $('#filtroStatus').val(),
                tipo: $('#filtroTipo').val(),
                data_inicio: $('#dataInicio').val(),
                data_fim: $('#dataFim').val()
            };
            
            $.ajax({
                url: 'listar_desligamentos.php',
                type: 'GET',
                data: filtros,
                dataType: 'json',
                beforeSend: function() {
                    $('#corpoTabela').html(`
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </td>
                        </tr>
                    `);
                },
                success: function(response) {
                    if (response.status === 'success') {
                        preencherTabela(response.data);
                        atualizarPaginacao(response.pagination);
                    } else {
                        $('#corpoTabela').html(`
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    Nenhum desligamento encontrado
                                </td>
                            </tr>
                        `);
                    }
                },
                error: function() {
                    $('#corpoTabela').html(`
                        <tr>
                            <td colspan="7" class="text-center text-danger">
                                Erro ao carregar desligamentos
                            </td>
                        </tr>
                    `);
                }
            });
        }
        
        // Função para preencher a tabela
        function preencherTabela(desligamentos) {
            let html = '';
            
                if (desligamentos.length === 0) {
                html = `
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            Nenhum desligamento encontrado
                        </td>
                    </tr>
                `;
            } else {
                desligamentos.forEach(function(des) {
                    html += `
                        <tr>
                            <td>#${des.des_id}</td>
                            <td>
                                <strong>${des.funcionario_nome}</strong><br>
                                <small class="text-muted">${des.funcionario_cpf}</small>
                            </td>
                            <td>${des.tipo_desligamento}</td>
                            <td>${des.data_solicitacao}</td>
                            <td>${des.data_desligamento}</td>
                            <td>
                                <span class="badge bg-${des.status_class}">${des.status}</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary" onclick="visualizarDesligamento(${des.des_id})" title="Visualizar">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    ${des.status_codigo !== 'finalizado' ? `
                                        <button type="button" class="btn btn-outline-warning" onclick="editarDesligamento(${des.des_id})" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    ` : ''}
                                    ${des.status_codigo === 'solicitado' ? `
                                        <button type="button" class="btn btn-outline-success" onclick="aprovarDesligamento(${des.des_id})" title="Aprovar">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    ` : ''}
                                    ${des.status_codigo === 'em_andamento' ? `
                                        <button type="button" class="btn btn-outline-info" onclick="finalizarDesligamento(${des.des_id})" title="Finalizar">
                                            <i class="bi bi-check-all"></i>
                                        </button>
                                    ` : ''}
                                </div>
                            </td>
                        </tr>
                    `;
                });
            }            $('#corpoTabela').html(html);
        }
        
        // Função para atualizar paginação
        function atualizarPaginacao(pagination) {
            totalPaginas = pagination.total_paginas;
            paginaAtual = pagination.pagina_atual;
            
            let html = '';
            
            // Botão anterior
            html += `
                <li class="page-item ${paginaAtual === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="irParaPagina(${paginaAtual - 1})">Anterior</a>
                </li>
            `;
            
            // Páginas
            for (let i = 1; i <= totalPaginas; i++) {
                if (i === paginaAtual || (i <= 3 || i > totalPaginas - 3 || Math.abs(i - paginaAtual) <= 1)) {
                    html += `
                        <li class="page-item ${i === paginaAtual ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="irParaPagina(${i})">${i}</a>
                        </li>
                    `;
                } else if ((i === 4 && paginaAtual > 5) || (i === totalPaginas - 3 && paginaAtual < totalPaginas - 4)) {
                    html += '<li class="page-item disabled"><a class="page-link">...</a></li>';
                }
            }
            
            // Botão próximo
            html += `
                <li class="page-item ${paginaAtual === totalPaginas ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="irParaPagina(${paginaAtual + 1})">Próximo</a>
                </li>
            `;
            
            $('#paginacao').html(html);
        }
        
        // Função para ir para uma página específica
        function irParaPagina(pagina) {
            if (pagina >= 1 && pagina <= totalPaginas) {
                paginaAtual = pagina;
                carregarDesligamentos();
            }
        }
        
        // Função para aplicar filtros
        function aplicarFiltros() {
            paginaAtual = 1;
            carregarDesligamentos();
        }
        
        // Função para limpar filtros
        function limparFiltros() {
            $('#busca').val('');
            $('#filtroStatus').val('');
            $('#filtroTipo').val('');
            $('#dataInicio').val('');
            $('#dataFim').val('');
            paginaAtual = 1;
            carregarDesligamentos();
        }
        
        // Função para salvar desligamento
        function salvarDesligamento() {
            const formData = $('#formNovoDesligamento').serialize();
            
            $.ajax({
                url: 'cadastrar_desligamento.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    $('button[onclick="salvarDesligamento()"]').prop('disabled', true).html('<i class="bi bi-spinner bi-spin me-1"></i>Salvando...');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        mostrarAlerta(response.message, 'success');
                        $('#modalNovoDesligamento').modal('hide');
                        $('#formNovoDesligamento')[0].reset();
                        carregarDesligamentos();
                        carregarEstatisticas();
                    } else {
                        mostrarAlerta(response.message, 'error');
                    }
                },
                error: function() {
                    mostrarAlerta('Erro ao salvar desligamento', 'error');
                },
                complete: function() {
                    $('button[onclick="salvarDesligamento()"]').prop('disabled', false).html('<i class="bi bi-save me-1"></i>Salvar Desligamento');
                }
            });
        }
        
        // Função para visualizar desligamento
        function visualizarDesligamento(id) {
            $.ajax({
                url: 'visualizar_desligamento.php?id=' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        preencherModalVisualizacao(response.data);
                        $('#modalVisualizarDesligamento').modal('show');
                    } else {
                        mostrarAlerta(response.message, 'error');
                    }
                },
                error: function() {
                    mostrarAlerta('Erro ao carregar dados do desligamento', 'error');
                }
            });
        }
        
        // Função para preencher modal de visualização
        function preencherModalVisualizacao(dados) {
            const statusClass = {
                'solicitado': 'warning',
                'em_andamento': 'info',
                'finalizado': 'success',
                'cancelado': 'danger'
            };
            
            const statusFuncionarioClass = {
                'ativo': 'success',
                'inativo': 'danger',
                'ferias': 'info',
                'licenca': 'warning'
            };
            
            const statusFuncionarioTexto = {
                'ativo': 'Ativo',
                'inativo': 'Inativo',
                'ferias': 'Em Férias',
                'licenca': 'Em Licença'
            };
            
            const html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Dados do Funcionário</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Nome:</strong></td><td>${dados.funcionario.nome}</td></tr>
                            <tr><td><strong>CPF:</strong></td><td>${dados.funcionario.cpf}</td></tr>
                            <tr><td><strong>Função:</strong></td><td>${dados.funcionario.funcao}</td></tr>
                            <tr><td><strong>Salário:</strong></td><td>${dados.funcionario.salario}</td></tr>
                            <tr><td><strong>Data Admissão:</strong></td><td>${dados.funcionario.data_admissao}</td></tr>
                            <tr><td><strong>Status Atual:</strong></td><td><span class="badge bg-${statusFuncionarioClass[dados.funcionario.status_atual] || 'secondary'}">${statusFuncionarioTexto[dados.funcionario.status_atual] || dados.funcionario.status_atual}</span></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Dados do Desligamento</h6>
                        <table class="table table-sm">
                            <tr><td><strong>ID:</strong></td><td>#${dados.desligamento.id}</td></tr>
                            <tr><td><strong>Status:</strong></td><td><span class="badge bg-${statusClass[dados.desligamento.status_codigo]}">${dados.desligamento.status}</span></td></tr>
                            <tr><td><strong>Tipo:</strong></td><td>${dados.desligamento.tipo_desligamento}</td></tr>
                            <tr><td><strong>Data Solicitação:</strong></td><td>${dados.desligamento.data_solicitacao}</td></tr>
                            <tr><td><strong>Data Desligamento:</strong></td><td>${dados.desligamento.data_desligamento}</td></tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-primary">Motivo</h6>
                        <p class="border p-2 bg-light">${dados.desligamento.motivo || 'Não informado'}</p>
                    </div>
                </div>
                
                ${dados.historico.length > 0 ? `
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary">Histórico</h6>
                            <div class="timeline">
                                ${dados.historico.map(item => `
                                    <div class="timeline-item">
                                        <div class="timeline-marker timeline-marker-active">
                                            <i class="bi bi-check"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6 class="card-title">${item.acao}</h6>
                                                    <p class="card-text">${item.observacoes}</p>
                                                    <small class="text-muted">Por: ${item.usuario} em ${item.data}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                ` : ''}
            `;
            
            $('#conteudoVisualizacao').html(html);
        }
        
        // Função para aprovar desligamento
        function aprovarDesligamento(id) {
            Swal.fire({
                title: 'Aprovar Desligamento',
                text: 'Tem certeza que deseja aprovar este desligamento?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, aprovar',
                cancelButtonText: 'Cancelar',
                input: 'textarea',
                inputPlaceholder: 'OBSERVAÇÕES (OPCIONAL)...',
                didOpen: () => {
                    const textarea = Swal.getInput();
                    if (textarea) {
                        textarea.style.textTransform = 'uppercase';
                        textarea.addEventListener('input', function() {
                            this.value = this.value.toUpperCase();
                        });
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    atualizarStatusDesligamento(id, 'aprovar', result.value);
                }
            });
        }
        
        // Função para finalizar desligamento
        function finalizarDesligamento(id) {
            Swal.fire({
                title: 'Finalizar Desligamento',
                html: 'Tem certeza que deseja finalizar este desligamento?<br><br><strong>⚠️ O funcionário será automaticamente marcado como INATIVO.</strong>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, finalizar',
                cancelButtonText: 'Cancelar',
                input: 'textarea',
                inputPlaceholder: 'OBSERVAÇÕES (OPCIONAL)...',
                didOpen: () => {
                    const textarea = Swal.getInput();
                    if (textarea) {
                        textarea.style.textTransform = 'uppercase';
                        textarea.addEventListener('input', function() {
                            this.value = this.value.toUpperCase();
                        });
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    atualizarStatusDesligamento(id, 'finalizar', result.value);
                }
            });
        }
        
        // Função para editar desligamento
        function editarDesligamento(id) {
            // Primeiro, buscar os dados do desligamento
            $.ajax({
                url: 'visualizar_desligamento.php?id=' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        preencherFormularioEdicao(response.data);
                        $('#modalEditarDesligamento').modal('show');
                    } else {
                        mostrarAlerta(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    mostrarAlerta('Erro ao carregar dados do desligamento', 'error');
                }
            });
        }
        
        // Função para preencher o formulário de edição
        function preencherFormularioEdicao(dados) {
            // Carregar funcionários primeiro
            carregarFuncionariosEdicao().then(() => {
                // Aguardar um pouco para garantir que o select foi populado
                setTimeout(() => {
                    // Preencher os campos do formulário
                    $('#editDesligamentoId').val(dados.desligamento.id);
                    $('#editFuncionario').val(dados.funcionario.id);
                    
                    // Converter datas do formato dd/mm/yyyy para yyyy-mm-dd
                    function converterData(dataStr) {
                        if (!dataStr) return '';
                        const partes = dataStr.split('/');
                        if (partes.length === 3) {
                            return partes[2] + '-' + partes[1].padStart(2, '0') + '-' + partes[0].padStart(2, '0');
                        }
                        return dataStr;
                    }
                    
                    const dataSolicitacao = converterData(dados.desligamento.data_solicitacao);
                    const dataDesligamento = converterData(dados.desligamento.data_desligamento);
                    
                    $('#editDataSolicitacao').val(dataSolicitacao);
                    $('#editDataDesligamento').val(dataDesligamento);
                    $('#editTipoDesligamento').val(dados.desligamento.tipo_codigo);
                    $('#editAvisoPrevio').val(dados.desligamento.aviso_previo_codigo);
                    $('#editDiasAvisoPrevio').val(dados.desligamento.dias_aviso_previo || 0);
                    $('#editMotivo').val(dados.desligamento.motivo || '');
                    $('#editObservacoes').val(dados.desligamento.observacoes || '');
                }, 200); // Aumentei o timeout para 200ms
                
            }).catch((error) => {
                mostrarAlerta('Erro ao carregar funcionários para edição', 'error');
            });
        }
        
        // Função para carregar funcionários no select de edição
        function carregarFuncionariosEdicao() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: 'listar_funcionarios_edicao.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            let options = '<option value="">Selecione um funcionário</option>';
                            response.funcionarios.forEach(function(funcionario) {
                                const statusInfo = funcionario.status === 'inativo' ? ' (INATIVO)' : '';
                                options += `<option value="${funcionario.id}">${funcionario.nome} - ${funcionario.funcao}${statusInfo}</option>`;
                            });
                            $('#editFuncionario').html(options);
                            resolve();
                        } else {
                            mostrarAlerta('Erro ao carregar funcionários', 'error');
                            reject();
                        }
                    },
                    error: function() {
                        mostrarAlerta('Erro ao carregar funcionários', 'error');
                        reject();
                    }
                });
            });
        }
        
        // Função para atualizar desligamento
        function atualizarDesligamento() {
            const formData = new FormData(document.getElementById('formEditarDesligamento'));
            
            // Desabilitar botão durante o processamento
            $('button[onclick="atualizarDesligamento()"]').prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1"></i>Atualizando...');
            
            $.ajax({
                url: 'atualizar_desligamento_dados.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        mostrarAlerta(response.message, 'success');
                        $('#modalEditarDesligamento').modal('hide');
                        carregarDesligamentos();
                        carregarEstatisticas();
                        
                        // Resetar formulário
                        document.getElementById('formEditarDesligamento').reset();
                    } else {
                        mostrarAlerta(response.message, 'error');
                    }
                },
                error: function() {
                    mostrarAlerta('Erro ao atualizar desligamento', 'error');
                },
                complete: function() {
                    // Reabilitar botão
                    $('button[onclick="atualizarDesligamento()"]').prop('disabled', false).html('<i class="bi bi-save me-1"></i>Atualizar Desligamento');
                }
            });
        }
        
        // Função para atualizar status do desligamento
        function atualizarStatusDesligamento(id, acao, observacoes = '') {
            $.ajax({
                url: 'atualizar_desligamento.php',
                type: 'POST',
                data: {
                    desligamento_id: id,
                    acao: acao,
                    observacoes: observacoes
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        mostrarAlerta(response.message, 'success');
                        carregarDesligamentos();
                        carregarEstatisticas();
                    } else {
                        mostrarAlerta(response.message, 'error');
                    }
                },
                error: function() {
                    mostrarAlerta('Erro ao atualizar desligamento', 'error');
                }
            });
        }
        
        // Função para mostrar alertas
        function mostrarAlerta(mensagem, tipo) {
            const icon = tipo === 'success' ? 'success' : 'error';
            const title = tipo === 'success' ? 'Sucesso!' : 'Erro!';
            
            Swal.fire({
                title: title,
                text: mensagem,
                icon: icon,
                timer: 3000,
                showConfirmButton: false
            });
        }
    </script>
</body>

</html>