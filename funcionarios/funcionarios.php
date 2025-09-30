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
    <title>Funcionários - Sistema Gebert</title>

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
                            <a class="sidebar-link" href="#">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link active" href="funcionarios/funcionarios.php">
                                <i class="bi bi-people"></i> Funcionários
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="#">
                                <i class="bi bi-building"></i> Clientes
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
                            <a class="sidebar-link" href="logout.php">
                                <i class="bi bi-box-arrow-right"></i> Sair
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Funcionários</h1>
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
                            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i> Sair</a></li>
                        </ul>
                    </div>
                </div>


                <div class="row">
                    <!-- <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-people-fill me-2"></i> Usuários</h5>
                                <p class="card-text display-6">15</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-building me-2"></i> Clientes</h5>
                                <p class="card-text display-6">42</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-calendar2-check me-2"></i> Atendimentos</h5>
                                <p class="card-text display-6">128</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card text-white bg-danger">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-exclamation-triangle me-2"></i> Pendências</h5>
                                <p class="card-text display-6">7</p>
                            </div>
                        </div>
                    </div> -->
                </div>

                <!-- Conteúdo principal -->
                <!-- <div class="card">
                    <div class="card-header">
                        <i class="bi bi-activity me-1"></i> Atividade Recente
                    </div>
                    <div class="card-body">
                        
                    </div>
                </div> -->
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>