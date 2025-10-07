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
            0% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
            }
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
                            <a class="sidebar-link active" href="aso.php">
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
                                <i class="bi bi-gear"></i> Desligamentos
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
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                    <div>
                        <h1 class="h2 mb-0"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                            <i class="bi bi-heart-pulse me-2"></i>Controle de ASO
                        </h1>
                        <p class="text-muted small mb-0">Gestão de Atestados de Saúde Ocupacional</p>
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
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            ASO Vigentes</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-vigentes">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-check-circle-fill fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Vencem em 30 dias</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-vence30">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-exclamation-triangle-fill fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-danger h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            ASO Vencidos</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-vencidos">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-x-circle-fill fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Total Funcionários</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-funcionarios">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-people-fill fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros e Controles -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="filtro-status" class="form-label">Status ASO:</label>
                                        <select class="form-select" id="filtro-status">
                                            <option value="">Todos</option>
                                            <option value="VIGENTE">Vigentes</option>
                                            <option value="VENCE_30_DIAS">Vencem em 30 dias</option>
                                            <option value="VENCE_60_DIAS">Vencem em 60 dias</option>
                                            <option value="VENCIDO">Vencidos</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="filtro-tipo" class="form-label">Tipo Exame:</label>
                                        <select class="form-select" id="filtro-tipo">
                                            <option value="">Todos</option>
                                            <option value="Admissional">Admissional</option>
                                            <option value="Periódico">Periódico</option>
                                            <option value="Retorno ao trabalho">Retorno ao trabalho</option>
                                            <option value="Mudança de função">Mudança de função</option>
                                            <option value="Demissional">Demissional</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="busca-funcionario" class="form-label">Buscar:</label>
                                        <input type="text" class="form-control" id="busca-funcionario" placeholder="Nome, CPF...">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCadastrarASO">
                                    <i class="bi bi-plus-circle me-1"></i> Novo ASO
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de ASO -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-heart-pulse me-2"></i>Controle de ASO
                        </h5>
                        <button type="button" class="btn btn-success btn-sm" onclick="exportarRelatorio()">
                            <i class="bi bi-file-earmark-excel me-1"></i> Exportar
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="tabelaASO">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Funcionário</th>
                                        <th>CPF</th>
                                        <th>Tipo Exame</th>
                                        <th>Data Exame</th>
                                        <th>Validade</th>
                                        <th>Resultado</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-aso">
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            Carregando ASO...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <nav aria-label="Navegação de páginas" class="mt-3">
                            <ul class="pagination justify-content-center" id="paginacao">
                                <!-- Será preenchida via JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>

                <!-- Modal Cadastrar/Editar ASO -->
                <div class="modal fade" id="modalCadastrarASO" tabindex="-1" aria-labelledby="modalCadastrarASOLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalCadastrarASOLabel">
                                    <i class="bi bi-heart-pulse me-2"></i>Cadastrar ASO
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="formASO" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <input type="hidden" id="aso_id" name="aso_id">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="funcionario_id" class="form-label">Funcionário *</label>
                                            <select class="form-select" id="funcionario_id" name="funcionario_id" required>
                                                <option value="">Selecione um funcionário</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="tipo_exame" class="form-label">Tipo de Exame *</label>
                                            <select class="form-select" id="tipo_exame" name="tipo_exame" required>
                                                <option value="">Selecione o tipo</option>
                                                <option value="Admissional">Admissional</option>
                                                <option value="Periódico">Periódico</option>
                                                <option value="Retorno ao trabalho">Retorno ao trabalho</option>
                                                <option value="Mudança de função">Mudança de função</option>
                                                <option value="Demissional">Demissional</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="data_exame" class="form-label">Data do Exame *</label>
                                            <input type="date" class="form-control" id="data_exame" name="data_exame" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="data_validade" class="form-label">Data de Validade *</label>
                                            <input type="date" class="form-control" id="data_validade" name="data_validade" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="resultado" class="form-label">Resultado *</label>
                                            <select class="form-select" id="resultado" name="resultado" required>
                                                <option value="">Selecione o resultado</option>
                                                <option value="APTO">Apto</option>
                                                <option value="INAPTO">Inapto</option>
                                                <option value="APTO_COM_RESTRICOES">Apto com Restrições</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="numero_documento" class="form-label">Número do Documento</label>
                                            <input type="text" class="form-control" id="numero_documento" name="numero_documento">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="medico_responsavel" class="form-label">Médico Responsável *</label>
                                            <input type="text" class="form-control" id="medico_responsavel" name="medico_responsavel" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="crm_medico" class="form-label">CRM</label>
                                            <input type="text" class="form-control" id="crm_medico" name="crm_medico">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="clinica_exame" class="form-label">Clínica/Local do Exame</label>
                                            <input type="text" class="form-control" id="clinica_exame" name="clinica_exame">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="exames_realizados" class="form-label">Exames Realizados</label>
                                            <textarea class="form-control" id="exames_realizados" name="exames_realizados" rows="3"
                                                placeholder="Ex: Exame clínico, audiometria, exame oftalmológico..."></textarea>
                                        </div>
                                    </div>

                                    <div class="row" id="restricoes_container" style="display: none;">
                                        <div class="col-md-12">
                                            <label for="restricoes" class="form-label">Restrições</label>
                                            <textarea class="form-control" id="restricoes" name="restricoes" rows="3"
                                                placeholder="Descreva as restrições do funcionário..."></textarea>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="observacoes" class="form-label">Observações</label>
                                            <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="arquivo_aso" class="form-label">Arquivo ASO (PDF)</label>
                                            <input type="file" class="form-control" id="arquivo_aso" name="arquivo_aso" accept=".pdf,.jpg,.jpeg,.png">
                                            <div class="form-text">Formatos aceitos: PDF, JPG, PNG (máx. 5MB)</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i>Salvar ASO
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Visualizar ASO -->
                <div class="modal fade" id="modalVisualizarASO" tabindex="-1" aria-labelledby="modalVisualizarASOLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalVisualizarASOLabel">
                                    <i class="bi bi-eye me-2"></i>Detalhes do ASO
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" id="asoTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="detalhes-tab" data-bs-toggle="tab" data-bs-target="#detalhes-pane" type="button" role="tab" aria-controls="detalhes-pane" aria-selected="true">
                                            <i class="bi bi-info-circle me-1"></i>Detalhes Atuais
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="historico-tab" data-bs-toggle="tab" data-bs-target="#historico-pane" type="button" role="tab" aria-controls="historico-pane" aria-selected="false">
                                            <i class="bi bi-clock-history me-1"></i>Histórico de ASO
                                        </button>
                                    </li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content mt-3" id="asoTabContent">
                                    <!-- Aba Detalhes -->
                                    <div class="tab-pane fade show active" id="detalhes-pane" role="tabpanel" aria-labelledby="detalhes-tab">
                                        <div id="detalhes-aso">
                                            <!-- Será preenchido via JavaScript -->
                                        </div>
                                    </div>

                                    <!-- Aba Histórico -->
                                    <div class="tab-pane fade" id="historico-pane" role="tabpanel" aria-labelledby="historico-tab">
                                        <div id="historico-aso">
                                            <div class="text-center">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Carregando histórico...</span>
                                                </div>
                                                <p class="mt-2">Carregando histórico de ASO...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                <button type="button" class="btn btn-primary" onclick="imprimirASO()">
                                    <i class="bi bi-printer me-1"></i>Imprimir
                                </button>
                                <button type="button" class="btn btn-info" onclick="gerarRelatorioFuncionario()" id="btn-relatorio-funcionario">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>Relatório Completo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
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
        const registrosPorPagina = 10;
        let editandoASO = false;

        // Inicialização da página
        $(document).ready(function() {
            carregarEstatisticas();
            carregarFuncionarios();
            carregarListaASO();

            // Event listeners
            $('#filtro-status, #filtro-tipo, #busca-funcionario').on('change keyup', function() {
                carregarListaASO();
            });

            $('#resultado').on('change', function() {
                toggleRestricoesContainer();
            });

            $('#formASO').on('submit', function(e) {
                e.preventDefault();
                salvarASO();
            });

            // Limpar formulário ao fechar modal
            $('#modalCadastrarASO').on('hidden.bs.modal', function() {
                limparFormulario();
            });

            // Limpar modal de visualização ao fechar
            $('#modalVisualizarASO').on('hidden.bs.modal', function() {
                // Resetar para a primeira aba
                $('#detalhes-tab').tab('show');
                // Limpar conteúdo do histórico
                $('#historico-aso').html(`
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando histórico...</span>
                        </div>
                        <p class="mt-2">Carregando histórico de ASO...</p>
                    </div>
                `);
            });
        });

        // Carregar estatísticas do dashboard
        function carregarEstatisticas() {
            $.ajax({
                url: 'buscar_estatisticas_aso.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Estatísticas recebidas:', data);
                    if (data.status === 'success') {
                        $('#total-vigentes').text(data.vigentes);
                        $('#total-vence30').text(data.vence30);
                        $('#total-vencidos').text(data.vencidos);
                        $('#total-funcionarios').text(data.total_funcionarios);

                        console.log('Cards atualizados com:', {
                            vigentes: data.vigentes,
                            vence30: data.vence30,
                            vencidos: data.vencidos,
                            total_funcionarios: data.total_funcionarios
                        });
                    } else {
                        console.error('Erro nas estatísticas:', data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro AJAX nas estatísticas:', {
                        xhr: xhr,
                        status: status,
                        error: error
                    });
                    console.error('Resposta do servidor:', xhr.responseText);
                }
            });
        }

        // Carregar lista de funcionários para o select
        function carregarFuncionarios() {
            $.ajax({
                url: 'listar_funcionarios_aso.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.status === 'success') {
                        let options = '<option value="">Selecione um funcionário</option>';
                        data.funcionarios.forEach(function(funcionario) {
                            options += `<option value="${funcionario.id}">${funcionario.nome} - ${funcionario.cpf}</option>`;
                        });
                        $('#funcionario_id').html(options);
                    }
                },
                error: function() {
                    Swal.fire('Erro', 'Erro ao carregar lista de funcionários', 'error');
                }
            });
        }

        // Carregar lista de ASO
        function carregarListaASO(pagina = 1) {
            paginaAtual = pagina;

            const filtros = {
                status: $('#filtro-status').val(),
                tipo: $('#filtro-tipo').val(),
                busca: $('#busca-funcionario').val(),
                pagina: pagina,
                limite: registrosPorPagina
            };

            console.log('Carregando ASO com filtros:', filtros);

            $.ajax({
                url: 'listar_aso.php',
                type: 'GET',
                data: filtros,
                dataType: 'json',
                success: function(data) {
                    console.log('Resposta do servidor:', data);
                    if (data.status === 'success') {
                        preencherTabelaASO(data.asos);
                        criarPaginacao(data.paginacao);
                    } else {
                        console.error('Erro na resposta:', data.message);
                        $('#tbody-aso').html('<tr><td colspan="8" class="text-center text-muted">Nenhum ASO encontrado</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro AJAX:', {
                        xhr: xhr,
                        status: status,
                        error: error
                    });
                    console.error('Resposta do servidor:', xhr.responseText);
                    $('#tbody-aso').html('<tr><td colspan="8" class="text-center text-danger">Erro ao carregar dados: ' + error + '</td></tr>');
                }
            });
        }

        // Preencher tabela de ASO
        function preencherTabelaASO(asos) {
            let html = '';

            if (asos.length === 0) {
                html = '<tr><td colspan="8" class="text-center text-muted">Nenhum ASO encontrado</td></tr>';
            } else {
                asos.forEach(function(aso) {
                    const statusClass = getStatusClass(aso.status_vencimento);
                    const resultadoClass = getResultadoClass(aso.resultado);

                    html += `
                        <tr>
                            <td>${aso.funcionario_nome}</td>
                            <td>${aso.funcionario_cpf}</td>
                            <td>${aso.tipo_exame}</td>
                            <td>${formatarData(aso.data_exame)}</td>
                            <td>${formatarData(aso.data_validade)}</td>
                            <td><span class="badge ${resultadoClass}">${formatarResultado(aso.resultado)}</span></td>
                            <td><span class="badge ${statusClass}">${formatarStatusVencimentoComDias(aso.status_vencimento, aso.dias_para_vencimento)}</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="visualizarASO(${aso.aso_id})" title="Visualizar">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="editarASO(${aso.aso_id})" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="excluirASO(${aso.aso_id})" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#tbody-aso').html(html);
        }

        // Criar paginação
        function criarPaginacao(paginacao) {
            let html = '';

            // Botão anterior
            html += `<li class="page-item ${paginacao.pagina_atual === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="carregarListaASO(${paginacao.pagina_atual - 1})">Anterior</a>
                     </li>`;

            // Números das páginas
            for (let i = 1; i <= paginacao.total_paginas; i++) {
                html += `<li class="page-item ${i === paginacao.pagina_atual ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="carregarListaASO(${i})">${i}</a>
                         </li>`;
            }

            // Botão próximo
            html += `<li class="page-item ${paginacao.pagina_atual === paginacao.total_paginas ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="carregarListaASO(${paginacao.pagina_atual + 1})">Próximo</a>
                     </li>`;

            $('#paginacao').html(html);
        }

        // Salvar ASO
        function salvarASO() {
            const formData = new FormData($('#formASO')[0]);
            const url = editandoASO ? 'atualizar_aso.php' : 'cadastrar_aso.php';

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(data) {
                    if (data.status === 'success') {
                        Swal.fire('Sucesso', data.message, 'success');
                        $('#modalCadastrarASO').modal('hide');
                        carregarListaASO();
                        carregarEstatisticas();
                    } else {
                        Swal.fire('Erro', data.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Erro', 'Erro ao salvar ASO', 'error');
                }
            });
        }

        // Visualizar ASO
        function visualizarASO(id) {
            $.ajax({
                url: 'buscar_aso.php',
                type: 'GET',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status === 'success') {
                        preencherDetalhesASO(data.aso);
                        // Guardar o ID do funcionário para carregar histórico
                        $('#modalVisualizarASO').data('funcionario-id', data.aso.funcionario_id);
                        $('#modalVisualizarASO').modal('show');
                    } else {
                        Swal.fire('Erro', data.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Erro', 'Erro ao buscar dados do ASO', 'error');
                }
            });
        }

        // Carregar histórico quando a aba for clicada
        $('#historico-tab').on('click', function() {
            const funcionarioId = $('#modalVisualizarASO').data('funcionario-id');
            if (funcionarioId) {
                carregarHistoricoASO(funcionarioId);
            }
        });

        // Função para carregar histórico de ASO do funcionário
        function carregarHistoricoASO(funcionarioId) {
            $.ajax({
                url: 'buscar_historico_aso.php',
                type: 'GET',
                data: {
                    funcionario_id: funcionarioId
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status === 'success') {
                        preencherHistoricoASO(data.historico, data.funcionario);
                    } else {
                        $('#historico-aso').html('<div class="alert alert-warning">Nenhum histórico encontrado</div>');
                    }
                },
                error: function() {
                    $('#historico-aso').html('<div class="alert alert-danger">Erro ao carregar histórico</div>');
                }
            });
        }

        // Editar ASO
        function editarASO(id) {
            editandoASO = true;
            $('#modalCadastrarASOLabel').html('<i class="bi bi-pencil me-2"></i>Editar ASO');

            $.ajax({
                url: 'buscar_aso.php',
                type: 'GET',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status === 'success') {
                        preencherFormularioASO(data.aso);
                        $('#modalCadastrarASO').modal('show');
                    } else {
                        Swal.fire('Erro', data.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Erro', 'Erro ao buscar dados do ASO', 'error');
                }
            });
        }

        // Excluir ASO
        function excluirASO(id) {
            Swal.fire({
                title: 'Confirmar exclusão',
                text: 'Tem certeza que deseja excluir este ASO?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'excluir_aso.php',
                        type: 'POST',
                        data: {
                            id: id
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.status === 'success') {
                                Swal.fire('Sucesso', data.message, 'success');
                                carregarListaASO();
                                carregarEstatisticas();
                            } else {
                                Swal.fire('Erro', data.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Erro', 'Erro ao excluir ASO', 'error');
                        }
                    });
                }
            });
        }

        // Funções auxiliares
        function getStatusClass(status) {
            const classes = {
                'VIGENTE': 'bg-success',
                'VENCE_30_DIAS': 'bg-warning text-dark',
                'VENCE_60_DIAS': 'bg-info text-dark',
                'VENCIDO': 'bg-danger'
            };
            return classes[status] || 'bg-secondary';
        }

        function getResultadoClass(resultado) {
            const classes = {
                'APTO': 'bg-success',
                'INAPTO': 'bg-danger',
                'APTO_COM_RESTRICOES': 'bg-warning text-dark'
            };
            return classes[resultado] || 'bg-secondary';
        }

        function formatarData(data) {
            return new Date(data + 'T00:00:00').toLocaleDateString('pt-BR');
        }

        function formatarResultado(resultado) {
            const resultados = {
                'APTO': 'Apto',
                'INAPTO': 'Inapto',
                'APTO_COM_RESTRICOES': 'Apto c/ Restrições'
            };
            return resultados[resultado] || resultado;
        }

        function formatarStatusVencimento(status) {
            const statusTexto = {
                'VIGENTE': 'Vigente',
                'VENCE_30_DIAS': 'Vence em 30 dias',
                'VENCE_60_DIAS': 'Vence em 31-60 dias',
                'VENCIDO': 'Vencido'
            };
            return statusTexto[status] || status;
        }

        function formatarStatusVencimentoComDias(status, dias) {
            if (status === 'VENCE_30_DIAS' || status === 'VENCE_60_DIAS') {
                return `Vence em ${dias} dias`;
            }
            return formatarStatusVencimento(status);
        }

        function toggleRestricoesContainer() {
            if ($('#resultado').val() === 'APTO_COM_RESTRICOES') {
                $('#restricoes_container').show();
                $('#restricoes').prop('required', true);
            } else {
                $('#restricoes_container').hide();
                $('#restricoes').prop('required', false);
            }
        }

        function limparFormulario() {
            $('#formASO')[0].reset();
            $('#aso_id').val('');
            $('#restricoes_container').hide();
            editandoASO = false;
            $('#modalCadastrarASOLabel').html('<i class="bi bi-heart-pulse me-2"></i>Cadastrar ASO');
        }

        function preencherFormularioASO(aso) {
            $('#aso_id').val(aso.aso_id);
            $('#funcionario_id').val(aso.funcionario_id);
            $('#tipo_exame').val(aso.tipo_exame);
            $('#data_exame').val(aso.data_exame);
            $('#data_validade').val(aso.data_validade);
            $('#resultado').val(aso.resultado);
            $('#numero_documento').val(aso.numero_documento);
            $('#medico_responsavel').val(aso.medico_responsavel);
            $('#crm_medico').val(aso.crm_medico);
            $('#clinica_exame').val(aso.clinica_exame);
            $('#exames_realizados').val(aso.exames_realizados);
            $('#restricoes').val(aso.restricoes);
            $('#observacoes').val(aso.observacoes);

            toggleRestricoesContainer();
        }

        function preencherDetalhesASO(aso) {
            const statusClass = getStatusClass(aso.status_vencimento);
            const resultadoClass = getResultadoClass(aso.resultado);

            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6><strong>Funcionário:</strong></h6>
                        <p>${aso.funcionario_nome}</p>
                        
                        <h6><strong>CPF:</strong></h6>
                        <p>${aso.funcionario_cpf}</p>
                        
                        <h6><strong>Função:</strong></h6>
                        <p>${aso.funcionario_funcao}</p>
                        
                        <h6><strong>Tipo de Exame:</strong></h6>
                        <p>${aso.tipo_exame}</p>
                        
                        <h6><strong>Data do Exame:</strong></h6>
                        <p>${formatarData(aso.data_exame)}</p>
                    </div>
                    <div class="col-md-6">
                        <h6><strong>Data de Validade:</strong></h6>
                        <p>${formatarData(aso.data_validade)}</p>
                        
                        <h6><strong>Resultado:</strong></h6>
                        <p><span class="badge ${resultadoClass}">${formatarResultado(aso.resultado)}</span></p>
                        
                        <h6><strong>Status:</strong></h6>
                        <p><span class="badge ${statusClass}">${formatarStatusVencimentoComDias(aso.status_vencimento, aso.dias_para_vencimento)}</span></p>
                        
                        <h6><strong>Médico Responsável:</strong></h6>
                        <p>${aso.medico_responsavel}${aso.crm_medico ? ' - CRM: ' + aso.crm_medico : ''}</p>
                        
                        <h6><strong>Clínica:</strong></h6>
                        <p>${aso.clinica_exame || 'Não informado'}</p>
                    </div>
                </div>
                
                ${aso.exames_realizados ? `
                <div class="row mt-3">
                    <div class="col-12">
                        <h6><strong>Exames Realizados:</strong></h6>
                        <p>${aso.exames_realizados}</p>
                    </div>
                </div>
                ` : ''}
                
                ${aso.restricoes ? `
                <div class="row">
                    <div class="col-12">
                        <h6><strong>Restrições:</strong></h6>
                        <div class="alert alert-warning">${aso.restricoes}</div>
                    </div>
                </div>
                ` : ''}
                
                ${aso.observacoes ? `
                <div class="row">
                    <div class="col-12">
                        <h6><strong>Observações:</strong></h6>
                        <p>${aso.observacoes}</p>
                    </div>
                </div>
                ` : ''}
                
                ${aso.arquivo_path ? `
                <div class="row">
                    <div class="col-12">
                        <h6><strong>Arquivo:</strong></h6>
                        <a href="${aso.arquivo_path}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-file-earmark-pdf"></i> Visualizar Arquivo
                        </a>
                    </div>
                </div>
                ` : ''}
            `;

            $('#detalhes-aso').html(html);
        }

        function preencherHistoricoASO(historico, funcionario) {
            let html = `
                <div class="mb-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-person-circle me-2"></i>${funcionario.nome}
                            </h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>CPF:</strong> ${funcionario.cpf}
                                </div>
                                <div class="col-md-4">
                                    <strong>Função:</strong> ${funcionario.funcao}
                                </div>
                                <div class="col-md-4">
                                    <strong>Total de ASO:</strong> ${historico.length}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            if (historico.length === 0) {
                html += '<div class="alert alert-info">Nenhum histórico de ASO encontrado para este funcionário.</div>';
            } else {
                html += '<div class="timeline">';

                historico.forEach(function(aso, index) {
                    const statusClass = getStatusClass(aso.status_vencimento);
                    const resultadoClass = getResultadoClass(aso.resultado);
                    const isAtivo = aso.status_aso === 'ATIVO';
                    const isFirst = index === 0;

                    html += `
                        <div class="timeline-item ${isFirst ? 'timeline-item-current' : ''}">
                            <div class="timeline-marker ${isAtivo ? 'timeline-marker-active' : 'timeline-marker-inactive'}">
                                <i class="bi ${isAtivo ? 'bi-check-circle-fill' : 'bi-circle'}"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="card ${isAtivo ? 'border-primary' : ''}">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="bi bi-heart-pulse me-1"></i>
                                            ${aso.tipo_exame}
                                            ${isAtivo ? '<span class="badge bg-success ms-2">ATUAL</span>' : '<span class="badge bg-secondary ms-2">HISTÓRICO</span>'}
                                        </h6>
                                        <small class="text-muted">${formatarData(aso.data_exame)}</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Data do Exame:</strong> ${formatarData(aso.data_exame)}</p>
                                                <p><strong>Data de Validade:</strong> ${formatarData(aso.data_validade)}</p>
                                                <p><strong>Resultado:</strong> <span class="badge ${resultadoClass}">${formatarResultado(aso.resultado)}</span></p>
                                                ${isAtivo ? `<p><strong>Status:</strong> <span class="badge ${statusClass}">${formatarStatusVencimentoComDias(aso.status_vencimento, aso.dias_para_vencimento)}</span></p>` : ''}
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Médico:</strong> ${aso.medico_responsavel}</p>
                                                ${aso.crm_medico ? `<p><strong>CRM:</strong> ${aso.crm_medico}</p>` : ''}
                                                ${aso.clinica_exame ? `<p><strong>Clínica:</strong> ${aso.clinica_exame}</p>` : ''}
                                                ${aso.numero_documento ? `<p><strong>Documento:</strong> ${aso.numero_documento}</p>` : ''}
                                            </div>
                                        </div>
                                        
                                        ${aso.exames_realizados ? `
                                        <div class="mt-2">
                                            <strong>Exames Realizados:</strong>
                                            <p class="small text-muted">${aso.exames_realizados}</p>
                                        </div>
                                        ` : ''}
                                        
                                        ${aso.restricoes ? `
                                        <div class="alert alert-warning small mt-2">
                                            <strong><i class="bi bi-exclamation-triangle me-1"></i>Restrições:</strong><br>
                                            ${aso.restricoes}
                                        </div>
                                        ` : ''}
                                        
                                        ${aso.observacoes ? `
                                        <div class="mt-2">
                                            <strong>Observações:</strong>
                                            <p class="small text-muted">${aso.observacoes}</p>
                                        </div>
                                        ` : ''}
                                        
                                        <div class="mt-3 d-flex justify-content-between align-items-center">
                                            <div>
                                                ${aso.arquivo_path ? `
                                                <a href="${aso.arquivo_path}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-file-earmark-pdf me-1"></i>Ver Arquivo
                                                </a>
                                                ` : ''}
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted">
                                                    Cadastrado em: ${formatarDataHora(aso.created_at)}<br>
                                                    ${aso.updated_at !== aso.created_at ? `Atualizado em: ${formatarDataHora(aso.updated_at)}` : ''}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
            }

            $('#historico-aso').html(html);
        }

        // Função para gerar relatório completo do funcionário
        function gerarRelatorioFuncionario() {
            const funcionarioId = $('#modalVisualizarASO').data('funcionario-id');
            if (funcionarioId) {
                window.open(`relatorio_funcionario_aso.php?funcionario_id=${funcionarioId}`, '_blank');
            }
        }

        // Função auxiliar para formatar data e hora
        function formatarDataHora(dataHora) {
            const data = new Date(dataHora);
            return data.toLocaleDateString('pt-BR') + ' às ' + data.toLocaleTimeString('pt-BR', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function exportarRelatorio() {
            window.open('relatorio_aso.php', '_blank');
        }

        function imprimirASO() {
            window.print();
        }
    </script>
</body>

</html>