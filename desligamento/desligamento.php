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
                            <a class="sidebar-link" href="#">
                                <i class="bi bi-clipboard-check"></i> Relatórios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link active" href="desligamento.php">
                                <i class="bi bi-gear"></i> Desligamentos
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
                            <i class="bi bi-heart-pulse me-2"></i>Controle de Desligamentos   
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
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-people-fill fa-2x text-gray-300"></i>
                                    </div>
                                </div>
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
    </script>
</body>

</html>