<?php
require_once '../config/conexao.php'; // Incluir o arquivo de conexão
session_start();
connect_local_mysqli('gebert');
// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: index.php');
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
    <title>Dashboard - Sistema Gebert</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

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
                            <a class="sidebar-link " href="../avaliacoes/avaliacoes.php">
                                <i class="bi bi-star-fill"></i> Avaliações
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link active" href="/epi/epi.php">
                                <i class="bi bi-building"></i> EPI
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="#">
                                <i class="bi bi-clipboard-check"></i> Relatórios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="#">
                                <i class="bi bi-gear"></i> Configurações
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
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="bi bi-shield-check me-2"></i>Gestão de EPI
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-3">
                            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#cadastroEpiModal">
                                <i class="bi bi-plus-circle me-2"></i>Novo EPI
                            </button>
                            <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#entregaEpiModal">
                                <i class="bi bi-box-arrow-right me-2"></i>Nova Entrega
                            </button>
                        </div>
                        <div class="dropdown">
                            <a href="#" class="d-block text-decoration-none dropdown-toggle btn btn-outline-secondary" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-2"></i><?php echo htmlspecialchars($user['nome'] ?? 'Usuário'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser" style="border-radius: 15px; border: none;">
                                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Perfil</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Configurações</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Cards de resumo -->
                <div class="row mb-4">
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-3">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-2">
                                            <i class="bi bi-shield-check me-2"></i>Total de EPIs
                                        </div>
                                        <div class="h4 mb-0 font-weight-bold text-gray-800" id="totalEpis">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">Carregando...</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-shield-check" style="font-size: 3rem; color: #667eea; opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-3">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-2">
                                            <i class="bi bi-calendar-check me-2"></i>Entregas Este Mês
                                        </div>
                                        <div class="h4 mb-0 font-weight-bold text-gray-800" id="entregasMes">
                                            <div class="spinner-border spinner-border-sm text-success" role="status">
                                                <span class="visually-hidden">Carregando...</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-box-arrow-right" style="font-size: 3rem; color: #44a08d; opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-3">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-2">
                                            <i class="bi bi-check-circle me-2"></i>EPIs Ativos
                                        </div>
                                        <div class="h4 mb-0 font-weight-bold text-gray-800" id="episAtivos">
                                            <div class="spinner-border spinner-border-sm text-info" role="status">
                                                <span class="visually-hidden">Carregando...</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-check-circle" style="font-size: 3rem; color: #66a6ff; opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Abas de navegação -->
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="epiTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="equipamentos-tab" data-bs-toggle="tab" data-bs-target="#equipamentos" type="button" role="tab">
                                    <i class="bi bi-shield-check me-1"></i>Equipamentos
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="entregas-tab" data-bs-toggle="tab" data-bs-target="#entregas" type="button" role="tab">
                                    <i class="bi bi-box-arrow-right me-1"></i>Entregas
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="relatorios-tab" data-bs-toggle="tab" data-bs-target="#relatorios" type="button" role="tab">
                                    <i class="bi bi-file-earmark-text me-1"></i>Relatórios
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="epiTabContent">
                            <!-- Aba Equipamentos -->
                            <div class="tab-pane fade show active" id="equipamentos" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Lista de Equipamentos</h5>
                                    <div class="d-flex gap-2">
                                        <input type="text" class="form-control" id="filtroEquipamentos" placeholder="Buscar equipamentos...">
                                        <select class="form-select" id="filtroCategoria">
                                            <option value="">Todas as categorias</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="tabelaEquipamentos">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nome</th>
                                                <th>Categoria</th>
                                                <th>Fabricante</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dados carregados via JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Aba Entregas -->
                            <div class="tab-pane fade" id="entregas" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Histórico de Entregas</h5>
                                    <div class="d-flex gap-2">
                                        <input type="text" class="form-control" id="filtroEntregas" placeholder="Buscar entregas...">
                                        <select class="form-select" id="filtroStatusEntrega">
                                            <option value="">Todos os status</option>
                                            <option value="entregue">Entregue</option>
                                            <option value="devolvido">Devolvido</option>
                                            <option value="perdido">Perdido</option>
                                            <option value="danificado">Danificado</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="tabelaEntregas">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Funcionário</th>
                                                <th>EPI</th>
                                                <th>Data Entrega</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dados carregados via JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Aba Relatórios -->
                            <div class="tab-pane fade" id="relatorios" role="tabpanel">
                                <h5>Relatórios</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h6 class="card-title">Relatório de Entregas por Período</h6>
                                                <div class="mb-3">
                                                    <label class="form-label">Data Inicial</label>
                                                    <input type="date" class="form-control" id="dataInicialEntregas">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Data Final</label>
                                                    <input type="date" class="form-control" id="dataFinalEntregas">
                                                </div>
                                                <button class="btn btn-primary" onclick="gerarRelatorioEntregas()">
                                                    <i class="bi bi-file-earmark-pdf me-1"></i>Gerar Relatório
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h6 class="card-title">Relatório de EPIs por Funcionário</h6>
                                                <div class="mb-3">
                                                    <label class="form-label">Funcionário</label>
                                                    <select class="form-select" id="funcionarioRelatorio">
                                                        <option value="">Todos os funcionários</option>
                                                    </select>
                                                </div>
                                                <button class="btn btn-success" onclick="gerarRelatorioFuncionario()">
                                                    <i class="bi bi-file-earmark-excel me-1"></i>Gerar Relatório
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Cadastro de EPI -->
    <div class="modal fade" id="cadastroEpiModal" tabindex="-1" aria-labelledby="cadastroEpiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="cadastroEpiModalLabel">Cadastrar Novo EPI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="formCadastroEpi">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="epiNome" class="form-label">Nome do EPI *</label>
                                <input type="text" class="form-control" id="epiNome" name="nome" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="epiCategoria" class="form-label">Categoria *</label>
                                <select class="form-select" id="epiCategoria" name="categoria" required>
                                    <option value="">Selecione uma categoria</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="epiDescricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="epiDescricao" name="descricao" rows="3" placeholder="Descrição detalhada do equipamento"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="epiFabricante" class="form-label">Fabricante</label>
                                <input type="text" class="form-control" id="epiFabricante" name="fabricante">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="epiTamanho" class="form-label">Tamanho</label>
                                <input type="text" class="form-control" id="epiTamanho" name="tamanho" placeholder="P, M, G, XG, Único">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="epiObservacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="epiObservacoes" name="observacoes" rows="2" placeholder="Informações adicionais sobre o EPI"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar EPI</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Entrega de EPI -->
    <div class="modal fade" id="entregaEpiModal" tabindex="-1" aria-labelledby="entregaEpiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="entregaEpiModalLabel">Nova Entrega de EPI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="formEntregaEpi">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="entregaFuncionario" class="form-label">Funcionário *</label>
                                <select class="form-select" id="entregaFuncionario" name="funcionario_id" required>
                                    <option value="">Selecione um funcionário</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="entregaEpi" class="form-label">EPI *</label>
                                <select class="form-select" id="entregaEpi" name="epi_id" required>
                                    <option value="">Selecione um EPI</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="entregaDataEntrega" class="form-label">Data da Entrega *</label>
                                <input type="date" class="form-control" id="entregaDataEntrega" name="data_entrega" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="entregaDataPrevista" class="form-label">Previsão de Devolução</label>
                                <input type="date" class="form-control" id="entregaDataPrevista" name="data_prevista">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="entregaMotivo" class="form-label">Motivo da Entrega</label>
                            <textarea class="form-control" id="entregaMotivo" name="motivo" rows="2" placeholder="Ex: Novo funcionário, reposição, etc."></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="entregaObservacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="entregaObservacoes" name="observacoes" rows="2" placeholder="Informações adicionais sobre a entrega"></textarea>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="entregaAssinatura" name="assinatura" value="1">
                            <label class="form-check-label" for="entregaAssinatura">
                                Funcionário assinou o recebimento
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Registrar Entrega</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Visualizar EPI -->
    <div class="modal fade" id="visualizarEpiModal" tabindex="-1" aria-labelledby="visualizarEpiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-dark">
                    <h5 class="modal-title" id="visualizarEpiModalLabel">
                        <i class="bi bi-eye me-2"></i>Detalhes do EPI
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <label class="form-label fw-bold text-dark">
                                <i class="bi bi-shield-check me-1"></i>Nome do EPI:
                            </label>
                            <p id="visualizar-nome" class="border rounded p-2 bg-light mb-0"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bi bi-tags me-1"></i>Categoria:
                                </label>
                                <p id="visualizar-categoria" class="border rounded p-2 bg-light mb-0"></p>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bi bi-building me-1"></i>Fabricante:
                                </label>
                                <p id="visualizar-fabricante" class="border rounded p-2 bg-light mb-0"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bi bi-rulers me-1"></i>Tamanho:
                                </label>
                                <p id="visualizar-tamanho" class="border rounded p-2 bg-light mb-0"></p>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bi bi-activity me-1"></i>Status:
                                </label>
                                <p id="visualizar-status" class="border rounded p-2 bg-light mb-0">
                                    <span id="status-badge"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bi bi-file-text me-1"></i>Descrição:
                                </label>
                                <div id="visualizar-descricao" class="border rounded p-3 bg-light" style="min-height: 80px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar EPI -->
    <div class="modal fade" id="editarEpiModal" tabindex="-1" aria-labelledby="editarEpiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editarEpiModalLabel">
                        <i class="bi bi-pencil me-2"></i>Editar EPI
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="formEditarEpi">
                    <input type="hidden" id="editar-id" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="editar-nome" class="form-label">
                                    <i class="bi bi-shield-check me-1"></i>Nome do EPI *
                                </label>
                                <input type="text" class="form-control" id="editar-nome" name="nome" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editar-categoria" class="form-label">
                                        <i class="bi bi-tags me-1"></i>Categoria *
                                    </label>
                                    <select class="form-select" id="editar-categoria" name="categoria_id" required>
                                        <option value="">Selecione uma categoria</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="editar-fabricante" class="form-label">
                                        <i class="bi bi-building me-1"></i>Fabricante *
                                    </label>
                                    <input type="text" class="form-control" id="editar-fabricante" name="fabricante" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editar-tamanho" class="form-label">
                                        <i class="bi bi-rulers me-1"></i>Tamanho
                                    </label>
                                    <input type="text" class="form-control" id="editar-tamanho" name="tamanho">
                                </div>
                                <div class="mb-3">
                                    <label for="editar-status" class="form-label">
                                        <i class="bi bi-activity me-1"></i>Status *
                                    </label>
                                    <select class="form-select" id="editar-status" name="status" required>
                                        <option value="ATIVO">ATIVO</option>
                                        <option value="INATIVO">INATIVO</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="editar-descricao" class="form-label">
                                        <i class="bi bi-file-text me-1"></i>Descrição
                                    </label>
                                    <textarea class="form-control" id="editar-descricao" name="descricao" rows="3" placeholder="Descreva o EPI..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle me-1"></i>Atualizar EPI
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Visualizar Entrega -->
    <div class="modal fade" id="visualizarEntregaModal" tabindex="-1" aria-labelledby="visualizarEntregaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="visualizarEntregaModalLabel">
                        <i class="bi bi-clipboard-check me-2"></i>Detalhes da Entrega
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bi bi-person me-1"></i>Funcionário:
                                </label>
                                <p id="entrega-funcionario" class="border rounded p-2 bg-light mb-0"></p>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bi bi-shield-check me-1"></i>EPI:
                                </label>
                                <p id="entrega-epi" class="border rounded p-2 bg-light mb-0"></p>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bi bi-calendar me-1"></i>Data da Entrega:
                                </label>
                                <p id="entrega-data" class="border rounded p-2 bg-light mb-0"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bi bi-calendar-event me-1"></i>Previsão de Devolução:
                                </label>
                                <p id="entrega-previsao" class="border rounded p-2 bg-light mb-0"></p>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bi bi-activity me-1"></i>Status:
                                </label>
                                <p id="entrega-status" class="border rounded p-2 bg-light mb-0">
                                    <span id="entrega-status-badge"></span>
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bi bi-check-circle me-1"></i>Assinatura:
                                </label>
                                <p id="entrega-assinatura" class="border rounded p-2 bg-light mb-0"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bi bi-info-circle me-1"></i>Motivo da Entrega:
                                </label>
                                <div id="entrega-motivo" class="border rounded p-3 bg-light" style="min-height: 60px;"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bi bi-file-text me-1"></i>Observações:
                                </label>
                                <div id="entrega-observacoes" class="border rounded p-3 bg-light" style="min-height: 60px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Carregar dados quando a página estiver pronta
        document.addEventListener('DOMContentLoaded', function() {
            carregarDashboard();
            carregarCategorias();
            carregarFuncionarios();
            carregarEquipamentos();
            carregarEntregas();

            // Definir data de hoje como padrão
            document.getElementById('entregaDataEntrega').value = new Date().toISOString().split('T')[0];

            // Carregar funcionários no select de relatórios
            carregarFuncionariosRelatorio();

            // Converter para maiúsculo automaticamente em todos os campos de texto
            const campos = document.querySelectorAll('input[type="text"], textarea');
            campos.forEach(campo => {
                campo.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            });
        });

        // Função para carregar dados do dashboard
        function carregarDashboard() {
            fetch('dashboard_epi.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('totalEpis').innerHTML = `<span class="counter">${data.data.total_epis || 0}</span>`;
                        document.getElementById('entregasMes').innerHTML = `<span class="counter">${data.data.entregas_mes || 0}</span>`;
                        document.getElementById('episAtivos').innerHTML = `<span class="counter">${data.data.epis_ativos || 0}</span>`;

                        // Animar os números
                        animateCounters();
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar dashboard:', error);
                    document.getElementById('totalEpis').textContent = '0';
                    document.getElementById('entregasMes').textContent = '0';
                    document.getElementById('episAtivos').textContent = '0';
                });
        }

        // Função para animar contadores
        function animateCounters() {
            const counters = document.querySelectorAll('.counter');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent);
                let current = 0;
                const increment = target / 20;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }, 50);
            });
        }

        // Função para carregar categorias
        function carregarCategorias() {
            console.log('Carregando categorias...');
            fetch('listar_categorias.php')
                .then(response => {
                    console.log('Response categorias:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Dados categorias:', data);
                    if (data.status === 'success') {
                        const selectCategoria = document.getElementById('epiCategoria');
                        const filtroCategoria = document.getElementById('filtroCategoria');

                        selectCategoria.innerHTML = '<option value="">Selecione uma categoria</option>';
                        filtroCategoria.innerHTML = '<option value="">Todas as categorias</option>';

                        data.data.forEach(categoria => {
                            selectCategoria.innerHTML += `<option value="${categoria.id}">${categoria.nome}</option>`;
                            filtroCategoria.innerHTML += `<option value="${categoria.nome}">${categoria.nome}</option>`;
                        });
                        console.log('Categorias carregadas com sucesso');
                    } else {
                        console.error('Erro ao carregar categorias:', data.message);
                    }
                })
                .catch(error => console.error('Erro ao carregar categorias:', error));
        }

        // Função para carregar funcionários
        function carregarFuncionarios() {
            fetch('../funcionarios/listar_funcionarios.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const selectFuncionario = document.getElementById('entregaFuncionario');
                        selectFuncionario.innerHTML = '<option value="">Selecione um funcionário</option>';

                        data.funcionarios.forEach(funcionario => {
                            if (funcionario.status === 'ativo') {
                                selectFuncionario.innerHTML += `<option value="${funcionario.id}">${funcionario.nome}</option>`;
                            }
                        });
                    }
                })
                .catch(error => console.error('Erro ao carregar funcionários:', error));
        }

        // Função para carregar equipamentos
        function carregarEquipamentos() {
            fetch('listar_equipamentos.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const tbody = document.querySelector('#tabelaEquipamentos tbody');
                        const selectEpi = document.getElementById('entregaEpi');

                        tbody.innerHTML = '';
                        selectEpi.innerHTML = '<option value="">Selecione um EPI</option>';

                        data.data.forEach(epi => {
                            const statusBadge = epi.status === 'ativo' ?
                                '<span class="badge bg-success">Ativo</span>' :
                                '<span class="badge bg-danger">Inativo</span>';

                            tbody.innerHTML += `
                                <tr>
                                    <td>${epi.id}</td>
                                    <td>${epi.nome}</td>
                                    <td>${epi.categoria}</td>
                                    <td>${epi.fabricante || '-'}</td>
                                    <td>${statusBadge}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="visualizarEpi(${epi.id})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editarEpi(${epi.id})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;

                            // Adicionar ao select de entregas apenas EPIs ativos
                            if (epi.status === 'ativo') {
                                selectEpi.innerHTML += `<option value="${epi.id}">${epi.nome}</option>`;
                            }
                        });
                    }
                })
                .catch(error => console.error('Erro ao carregar equipamentos:', error));
        }

        // Função para carregar entregas
        function carregarEntregas() {
            fetch('listar_entregas.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const tbody = document.querySelector('#tabelaEntregas tbody');
                        tbody.innerHTML = '';

                        data.data.forEach(entrega => {
                            let statusBadge;
                            switch (entrega.status) {
                                case 'entregue':
                                    statusBadge = '<span class="badge bg-primary">Entregue</span>';
                                    break;
                                case 'devolvido':
                                    statusBadge = '<span class="badge bg-success">Devolvido</span>';
                                    break;
                                case 'perdido':
                                    statusBadge = '<span class="badge bg-warning">Perdido</span>';
                                    break;
                                case 'danificado':
                                    statusBadge = '<span class="badge bg-danger">Danificado</span>';
                                    break;
                                default:
                                    statusBadge = '<span class="badge bg-secondary">' + entrega.status + '</span>';
                            }

                            tbody.innerHTML += `
                                <tr>
                                    <td>${entrega.id}</td>
                                    <td>${entrega.funcionario_nome}</td>
                                    <td>${entrega.epi_nome}</td>
                                    <td>${new Date(entrega.data_entrega).toLocaleDateString('pt-BR')}</td>
                                    <td>${statusBadge}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="visualizarEntrega(${entrega.id})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        ${entrega.status === 'entregue' ? 
                                            `<button class="btn btn-sm btn-success" onclick="marcarDevolucao(${entrega.id})">
                                                <i class="bi bi-box-arrow-in-left"></i>
                                            </button>` : ''
                                        }
                                    </td>
                                </tr>
                            `;
                        });
                    }
                })
                .catch(error => console.error('Erro ao carregar entregas:', error));
        }

        // Função para cadastrar EPI
        document.getElementById('formCadastroEpi').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('cadastrar_epi.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        bootstrap.Modal.getInstance(document.getElementById('cadastroEpiModal')).hide();
                        showNotification('success', data.message);
                        this.reset();
                        carregarEquipamentos();
                        carregarDashboard();
                    } else {
                        showNotification('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification('error', 'Erro ao cadastrar EPI');
                });
        });

        // Função para registrar entrega
        document.getElementById('formEntregaEpi').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('registrar_entrega.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        bootstrap.Modal.getInstance(document.getElementById('entregaEpiModal')).hide();
                        showNotification('success', data.message);
                        this.reset();
                        document.getElementById('entregaDataEntrega').value = new Date().toISOString().split('T')[0];
                        carregarEntregas();
                        carregarDashboard();
                    } else {
                        showNotification('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification('error', 'Erro ao registrar entrega');
                });
        });

        // Handler para o formulário de edição de EPI
        document.getElementById('formEditarEpi').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('atualizar_epi.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        bootstrap.Modal.getInstance(document.getElementById('editarEpiModal')).hide();
                        showNotification('success', data.message);
                        this.reset();
                        carregarEquipamentos();
                        carregarDashboard();
                    } else {
                        showNotification('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification('error', 'Erro ao atualizar EPI');
                });
        });

        // Função para mostrar notificações
        function showNotification(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(alertDiv);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 5000);
        }

        // Função para visualizar EPI
        function visualizarEpi(id) {
            fetch(`listar_equipamentos.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success' && data.data.length > 0) {
                        const epi = data.data[0];

                        // Preencher os campos do modal
                        document.getElementById('visualizar-nome').textContent = epi.nome || 'Não informado';
                        document.getElementById('visualizar-categoria').textContent = epi.categoria || 'Não informado';
                        document.getElementById('visualizar-fabricante').textContent = epi.fabricante || 'Não informado';
                        document.getElementById('visualizar-tamanho').textContent = epi.tamanho || 'Não informado';
                        document.getElementById('visualizar-descricao').textContent = epi.descricao || 'Nenhuma descrição disponível';

                        // Configurar badge de status
                        const statusBadge = document.getElementById('status-badge');
                        const status = epi.status || 'INDEFINIDO';
                        statusBadge.textContent = status;

                        // Aplicar classes do badge baseado no status
                        statusBadge.className = 'badge fs-6';
                        if (status === 'ATIVO') {
                            statusBadge.classList.add('bg-success');
                        } else if (status === 'INATIVO') {
                            statusBadge.classList.add('bg-danger');
                        } else {
                            statusBadge.classList.add('bg-secondary');
                        }

                        // Armazenar ID para possível edição
                        document.getElementById('visualizarEpiModal').setAttribute('data-epi-id', id);

                        // Exibir o modal
                        new bootstrap.Modal(document.getElementById('visualizarEpiModal')).show();
                    } else {
                        showNotification('error', 'EPI não encontrado');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification('error', 'Erro ao carregar dados do EPI');
                });
        }

        // Função para editar EPI a partir do modal de visualização
        function editarEpiFromVisualizacao() {
            const epiId = document.getElementById('visualizarEpiModal').getAttribute('data-epi-id');
            if (epiId) {
                // Fechar modal de visualização
                bootstrap.Modal.getInstance(document.getElementById('visualizarEpiModal')).hide();
                // Chamar função de edição (se existir)
                if (typeof editarEpi === 'function') {
                    editarEpi(epiId);
                } else {
                    showNotification('info', 'Função de edição ainda não implementada');
                }
            }
        }

        function editarEpi(id) {
            fetch(`listar_equipamentos.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success' && data.data.length > 0) {
                        const epi = data.data[0];

                        // Preencher os campos do formulário
                        document.getElementById('editar-id').value = epi.id;
                        document.getElementById('editar-nome').value = epi.nome || '';
                        document.getElementById('editar-fabricante').value = epi.fabricante || '';
                        document.getElementById('editar-tamanho').value = epi.tamanho || '';
                        document.getElementById('editar-status').value = epi.status || 'ATIVO';
                        document.getElementById('editar-descricao').value = epi.descricao || '';

                        // Carregar categorias e selecionar a atual
                        carregarCategoriasEdicao(epi.categoria_id);

                        // Exibir o modal
                        new bootstrap.Modal(document.getElementById('editarEpiModal')).show();
                    } else {
                        showNotification('error', 'EPI não encontrado');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification('error', 'Erro ao carregar dados do EPI');
                });
        }

        // Função para carregar categorias no modal de edição
        function carregarCategoriasEdicao(categoriaAtual = null) {
            console.log('Carregando categorias para edição...');
            fetch('listar_categorias.php')
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Dados recebidos:', data);
                    if (data.status === 'success') {
                        const select = document.getElementById('editar-categoria');
                        if (!select) {
                            console.error('Elemento editar-categoria não encontrado');
                            return;
                        }

                        select.innerHTML = '<option value="">Selecione uma categoria</option>';

                        if (data.data && data.data.length > 0) {
                            data.data.forEach(categoria => {
                                const option = document.createElement('option');
                                option.value = categoria.id;
                                option.textContent = categoria.nome;

                                // Selecionar a categoria atual se fornecida
                                if (categoriaAtual && categoria.id == categoriaAtual) {
                                    option.selected = true;
                                }

                                select.appendChild(option);
                            });
                            console.log('Categorias carregadas com sucesso');
                        } else {
                            console.log('Nenhuma categoria encontrada');
                        }
                    } else {
                        console.error('Erro no status:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar categorias:', error);
                });
        }

        function visualizarEntrega(id) {
            console.log('Visualizando entrega:', id);
            fetch(`listar_entregas.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Dados da entrega:', data);
                    if (data.status === 'success' && data.data.length > 0) {
                        const entrega = data.data[0];

                        // Preencher os campos do modal
                        document.getElementById('entrega-funcionario').textContent = entrega.funcionario_nome || 'Não informado';
                        document.getElementById('entrega-epi').textContent = entrega.epi_nome || 'Não informado';

                        // Formatar data da entrega
                        const dataEntrega = entrega.data_entrega ? new Date(entrega.data_entrega).toLocaleDateString('pt-BR') : 'Não informado';
                        document.getElementById('entrega-data').textContent = dataEntrega;

                        // Formatar previsão de devolução
                        const dataPrevisao = entrega.data_prevista ? new Date(entrega.data_prevista).toLocaleDateString('pt-BR') : 'Não informado';
                        document.getElementById('entrega-previsao').textContent = dataPrevisao;

                        // Configurar badge de status
                        const statusBadge = document.getElementById('entrega-status-badge');
                        const status = entrega.status || 'INDEFINIDO';
                        statusBadge.textContent = status;

                        // Aplicar classes do badge baseado no status
                        statusBadge.className = 'badge fs-6';
                        if (status === 'ENTREGUE' || status === 'ATIVO') {
                            statusBadge.classList.add('bg-success');
                        } else if (status === 'DEVOLVIDO') {
                            statusBadge.classList.add('bg-info');
                        } else if (status === 'VENCIDO') {
                            statusBadge.classList.add('bg-danger');
                        } else {
                            statusBadge.classList.add('bg-secondary');
                        }

                        // Configurar assinatura
                        const assinatura = entrega.assinatura == '1' ? 'Sim' : 'Não';
                        document.getElementById('entrega-assinatura').textContent = assinatura;

                        // Preencher campos de texto
                        document.getElementById('entrega-motivo').textContent = entrega.motivo || 'Nenhum motivo informado';
                        document.getElementById('entrega-observacoes').textContent = entrega.observacoes || 'Nenhuma observação';

                        // Exibir o modal
                        new bootstrap.Modal(document.getElementById('visualizarEntregaModal')).show();
                    } else {
                        showNotification('error', 'Entrega não encontrada');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification('error', 'Erro ao carregar dados da entrega');
                });
        }

        function marcarDevolucao(id) {
            if (confirm('Confirma a devolução deste EPI?')) {
                fetch('marcar_devolucao.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            entrega_id: id
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showNotification('success', data.message);
                            carregarEntregas();
                            carregarEquipamentos();
                            carregarDashboard();
                        } else {
                            showNotification('error', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        showNotification('error', 'Erro ao marcar devolução');
                    });
            }
        }

        function gerarRelatorioEntregas() {
            const dataInicial = document.getElementById('dataInicialEntregas').value;
            const dataFinal = document.getElementById('dataFinalEntregas').value;

            if (!dataInicial || !dataFinal) {
                showNotification('error', 'Informe as datas inicial e final');
                return;
            }

            // Abrir o gerador de PDF diretamente
            window.open(`gerar_pdf_entregas.php?data_inicial=${dataInicial}&data_final=${dataFinal}`, '_blank');
        }

        // Função para carregar funcionários no relatório
        function carregarFuncionariosRelatorio() {
            fetch('../funcionarios/listar_funcionarios.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const selectFuncionario = document.getElementById('funcionarioRelatorio');
                        selectFuncionario.innerHTML = '<option value="">Todos os funcionários</option>';

                        data.funcionarios.forEach(funcionario => {
                            selectFuncionario.innerHTML += `<option value="${funcionario.id}">${funcionario.nome}</option>`;
                        });
                    }
                })
                .catch(error => console.error('Erro ao carregar funcionários:', error));
        }

        function gerarRelatorioFuncionario() {
            const funcionario = document.getElementById('funcionarioRelatorio').value;
            const params = funcionario ? `?funcionario_id=${funcionario}` : '';
            window.open(`gerar_pdf_funcionario.php${params}`, '_blank');
        }

        function gerarRelatorioEstoque() {
            // Remover função de relatório de estoque
            showNotification('info', 'Relatório de estoque não disponível nesta versão');
        }
    </script>
</body>

</html>