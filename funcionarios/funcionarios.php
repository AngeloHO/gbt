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

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/funcionarios.css">

    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            border-radius: 0 20px 20px 0;
        }

        .sidebar-link {
            padding: 10px 15px;
            color: #adb5bd;
            text-decoration: none;
            display: block;
            border-left: 3px solid transparent;
            border-radius: 10px;
            margin: 5px 10px;
            transition: all 0.3s ease;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-left-color: #667eea;
            transform: translateX(10px);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo-subtitle {
            font-size: 10px;
            letter-spacing: 1px;
            color: #adb5bd;
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

        .btn-danger {
            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
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

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
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

        .alert {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        /* Cards de estatísticas */
        .stats-card {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-left: 5px solid #667eea;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .stats-card.success {
            background: linear-gradient(135deg, rgba(78, 205, 196, 0.1) 0%, rgba(68, 160, 141, 0.1) 100%);
            border-left-color: #44a08d;
        }

        .stats-card.info {
            background: linear-gradient(135deg, rgba(137, 247, 254, 0.1) 0%, rgba(102, 166, 255, 0.1) 100%);
            border-left-color: #66a6ff;
        }

        .stats-card.warning {
            background: linear-gradient(135deg, rgba(253, 187, 45, 0.1) 0%, rgba(34, 193, 195, 0.1) 100%);
            border-left-color: #fdbb2d;
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

        /* Inputs com maiúsculo automático */
        .text-uppercase-input {
            text-transform: uppercase;
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
                            <a class="sidebar-link active" href="funcionarios/funcionarios.php">
                                <i class="bi bi-people"></i> Funcionários
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
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                    <div>
                        <h1 class="h2 mb-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                            <i class="bi bi-people-fill me-2"></i>Gestão de Funcionários
                        </h1>
                        <p class="text-muted small mb-0">Controle completo dos colaboradores da empresa</p>
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
                            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i> Sair</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Cards de estatísticas -->
                <div class="row g-4 mb-4">
                    <div class="col-xl-3 col-lg-6">
                        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border-left: 5px solid #667eea !important;">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="p-3 rounded-circle" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="bi bi-people text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="small text-muted fw-semibold">Total de Funcionários</div>
                                    <div class="h4 mb-0 fw-bold counter" id="totalFuncionarios">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-lg-6">
                        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, rgba(78, 205, 196, 0.1) 0%, rgba(68, 160, 141, 0.1) 100%); border-left: 5px solid #44a08d !important;">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="p-3 rounded-circle" style="background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);">
                                        <i class="bi bi-person-check text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="small text-muted fw-semibold">Funcionários Ativos</div>
                                    <div class="h4 mb-0 fw-bold counter" id="funcionariosAtivos">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-lg-6">
                        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 133, 27, 0.1) 100%); border-left: 5px solid #fd7e14 !important;">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="p-3 rounded-circle" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                                        <i class="bi bi-person-dash text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="small text-muted fw-semibold">Funcionários Inativos</div>
                                    <div class="h4 mb-0 fw-bold counter" id="funcionariosInativos">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-lg-6">
                        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, rgba(137, 247, 254, 0.1) 0%, rgba(102, 166, 255, 0.1) 100%); border-left: 5px solid #66a6ff !important;">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="p-3 rounded-circle" style="background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);">
                                        <i class="bi bi-calendar-plus text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="small text-muted fw-semibold">Admissões Este Mês</div>
                                    <div class="h4 mb-0 fw-bold counter" id="admissoesEMes">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="h5 mb-0">Lista de Funcionários</h3>
                            <button type="button" id="btnCadastrarFuncionario" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#cadastroFuncionarioModal" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px; padding: 10px 20px;">
                                <i class="bi bi-person-plus me-2"></i>Cadastrar Funcionário
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-lg-3 mb-4">
                    </div>
                    <div class="modal fade" id="cadastroFuncionarioModal" tabindex="-1" aria-labelledby="cadastroFuncionarioModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content rounded-4 shadow">
                                <div class="modal-header bg-primary bg-gradient text-white border-bottom-0">
                                    <h1 class="modal-title fs-4" id="cadastroFuncionarioModalLabel"><i class="bi bi-person-plus-fill me-2"></i>Cadastro de Funcionário</h1>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <form id="formCadastroFuncionario">
                                        <!-- Nav tabs para organização -->
                                        <ul class="nav nav-tabs nav-fill mb-4" id="cadastroTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="dados-pessoais-tab" data-bs-toggle="tab" data-bs-target="#dados-pessoais" type="button" role="tab" aria-controls="dados-pessoais" aria-selected="true">
                                                    <i class="bi bi-person me-1"></i> Dados Pessoais
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="endereco-tab" data-bs-toggle="tab" data-bs-target="#endereco" type="button" role="tab" aria-controls="endereco" aria-selected="false">
                                                    <i class="bi bi-geo-alt me-1"></i> Endereço
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="funcao-tab" data-bs-toggle="tab" data-bs-target="#funcao" type="button" role="tab" aria-controls="funcao" aria-selected="false">
                                                    <i class="bi bi-briefcase me-1"></i> Função
                                                </button>
                                            </li>
                                        </ul>

                                        <!-- Tab panes -->
                                        <div class="tab-content">
                                            <!-- Dados Pessoais -->
                                            <div class="tab-pane fade show active" id="dados-pessoais" role="tabpanel" aria-labelledby="dados-pessoais-tab">
                                                <div class="card border-0 shadow-sm mb-3">
                                                    <div class="card-body">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label for="nome" class="form-label fw-semibold">
                                                                    <i class="bi bi-person-badge me-1 text-primary"></i>Nome Completo
                                                                </label>
                                                                <input type="text" class="form-control form-control-lg border-0 shadow-sm bg-light" id="nome" name="nome" required style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="cpf" class="form-label fw-semibold">
                                                                    <i class="bi bi-card-text me-1 text-primary"></i>CPF
                                                                </label>
                                                                <input type="text" class="form-control border-0 shadow-sm bg-light" id="cpf" name="cpf" placeholder="000.000.000-00" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label for="rg" class="form-label fw-semibold">
                                                                    <i class="bi bi-card-heading me-1 text-primary"></i>RG
                                                                </label>
                                                                <input type="text" class="form-control border-0 shadow-sm bg-light" id="rg" name="rg" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label for="dataNascimento" class="form-label fw-semibold">
                                                                    <i class="bi bi-calendar-event me-1 text-primary"></i>Data de Nascimento
                                                                </label>
                                                                <input type="date" class="form-control border-0 shadow-sm bg-light" id="dataNascimento" name="dataNascimento">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label for="telefone" class="form-label fw-semibold">
                                                                    <i class="bi bi-telephone me-1 text-primary"></i>Telefone/Celular
                                                                </label>
                                                                <input type="text" class="form-control border-0 shadow-sm bg-light" id="telefone" name="telefone" placeholder="(00) 00000-0000">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="email" class="form-label fw-semibold">
                                                                    <i class="bi bi-envelope me-1 text-primary"></i>E-mail
                                                                </label>
                                                                <input type="email" class="form-control border-0 shadow-sm bg-light" id="email" name="email">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="genero" class="form-label fw-semibold">
                                                                    <i class="bi bi-gender-ambiguous me-1 text-primary"></i>Gênero
                                                                </label>
                                                                <select class="form-select border-0 shadow-sm bg-light" id="genero" name="genero">
                                                                    <option value="" selected>Selecione</option>
                                                                    <option value="masculino">Masculino</option>
                                                                    <option value="feminino">Feminino</option>
                                                                    <option value="outro">Outro</option>
                                                                    <option value="naoInformar">Prefiro não informar</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Endereço -->
                                            <div class="tab-pane fade" id="endereco" role="tabpanel" aria-labelledby="endereco-tab">
                                                <div class="card border-0 shadow-sm mb-3">
                                                    <div class="card-body">
                                                        <div class="row g-3">
                                                            <div class="col-md-4">
                                                                <label for="cep" class="form-label fw-semibold">
                                                                    <i class="bi bi-geo me-1 text-primary"></i>CEP
                                                                </label>
                                                                <input type="text" class="form-control border-0 shadow-sm bg-light" id="cep" name="cep" placeholder="00000-000" maxlength="9">
                                                            </div>
                                                            <div class="col-md-8">
                                                                <label for="endereco" class="form-label fw-semibold">
                                                                    <i class="bi bi-signpost me-1 text-primary"></i>Endereço
                                                                </label>
                                                                <input type="text" class="form-control border-0 shadow-sm bg-light" id="endereco" name="endereco" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label for="numero" class="form-label fw-semibold">
                                                                    <i class="bi bi-house-door me-1 text-primary"></i>Número
                                                                </label>
                                                                <input type="text" class="form-control border-0 shadow-sm bg-light" id="numero" name="numero" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label for="complemento" class="form-label fw-semibold">
                                                                    <i class="bi bi-plus-square me-1 text-primary"></i>Complemento
                                                                </label>
                                                                <input type="text" class="form-control border-0 shadow-sm bg-light" id="complemento" name="complemento" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label for="bairro" class="form-label fw-semibold">
                                                                    <i class="bi bi-pin-map me-1 text-primary"></i>Bairro
                                                                </label>
                                                                <input type="text" class="form-control border-0 shadow-sm bg-light" id="bairro" name="bairro" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="cidade" class="form-label fw-semibold">
                                                                    <i class="bi bi-building me-1 text-primary"></i>Cidade
                                                                </label>
                                                                <input type="text" class="form-control border-0 shadow-sm bg-light" id="cidade" name="cidade" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="estado" class="form-label fw-semibold">
                                                                    <i class="bi bi-map me-1 text-primary"></i>Estado
                                                                </label>
                                                                <select class="form-select border-0 shadow-sm bg-light" id="estado" name="estado">
                                                                    <option value="" selected>Selecione o Estado</option>
                                                                    <option value="AC">Acre</option>
                                                                    <option value="AL">Alagoas</option>
                                                                    <option value="AP">Amapá</option>
                                                                    <option value="AM">Amazonas</option>
                                                                    <option value="BA">Bahia</option>
                                                                    <option value="CE">Ceará</option>
                                                                    <option value="DF">Distrito Federal</option>
                                                                    <option value="ES">Espírito Santo</option>
                                                                    <option value="GO">Goiás</option>
                                                                    <option value="MA">Maranhão</option>
                                                                    <option value="MT">Mato Grosso</option>
                                                                    <option value="MS">Mato Grosso do Sul</option>
                                                                    <option value="MG">Minas Gerais</option>
                                                                    <option value="PA">Pará</option>
                                                                    <option value="PB">Paraíba</option>
                                                                    <option value="PR">Paraná</option>
                                                                    <option value="PE">Pernambuco</option>
                                                                    <option value="PI">Piauí</option>
                                                                    <option value="RJ">Rio de Janeiro</option>
                                                                    <option value="RN">Rio Grande do Norte</option>
                                                                    <option value="RS">Rio Grande do Sul</option>
                                                                    <option value="RO">Rondônia</option>
                                                                    <option value="RR">Roraima</option>
                                                                    <option value="SC">Santa Catarina</option>
                                                                    <option value="SP">São Paulo</option>
                                                                    <option value="SE">Sergipe</option>
                                                                    <option value="TO">Tocantins</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Função -->
                                            <div class="tab-pane fade" id="funcao" role="tabpanel" aria-labelledby="funcao-tab">
                                                <div class="card border-0 shadow-sm mb-3">
                                                    <div class="card-body">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label for="funcao" class="form-label fw-semibold">
                                                                    <i class="bi bi-briefcase me-1 text-primary"></i>Função
                                                                </label>
                                                                <select class="form-select border-0 shadow-sm bg-light" id="funcao" name="funcao">
                                                                    <option value="" selected>Selecione a função</option>
                                                                    <option value="vigilante">Vigilante</option>
                                                                    <option value="porteiro">Porteiro</option>
                                                                    <option value="seguranca">Segurança</option>
                                                                    <option value="supervisor">Supervisor</option>
                                                                    <option value="coordenador">Coordenador</option>
                                                                    <option value="administrador">Administrador</option>
                                                                    <option value="outro">Outro</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="departamento" class="form-label fw-semibold">
                                                                    <i class="bi bi-diagram-3 me-1 text-primary"></i>Departamento
                                                                </label>
                                                                <select class="form-select border-0 shadow-sm bg-light" id="departamento" name="departamento">
                                                                    <option value="" selected>Selecione o departamento</option>
                                                                    <option value="operacional">Operacional</option>
                                                                    <option value="administrativo">Administrativo</option>
                                                                    <option value="comercial">Comercial</option>
                                                                    <option value="rh">Recursos Humanos</option>
                                                                    <option value="financeiro">Financeiro</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="dataAdmissao" class="form-label fw-semibold">
                                                                    <i class="bi bi-calendar-check me-1 text-primary"></i>Data de Admissão
                                                                </label>
                                                                <input type="date" class="form-control border-0 shadow-sm bg-light" id="dataAdmissao" name="dataAdmissao">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="status" class="form-label fw-semibold">
                                                                    <i class="bi bi-toggle-on me-1 text-primary"></i>Status
                                                                </label>
                                                                <select class="form-select border-0 shadow-sm bg-light" id="status" name="status">
                                                                    <option value="ativo" selected>Ativo</option>
                                                                    <option value="inativo">Inativo</option>
                                                                    <option value="ferias">Em férias</option>
                                                                    <option value="licenca">Em licença</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="turno" class="form-label fw-semibold">
                                                                    <i class="bi bi-clock me-1 text-primary"></i>Turno
                                                                </label>
                                                                <select class="form-select border-0 shadow-sm bg-light" id="turno" name="turno">
                                                                    <option value="" selected>Selecione o turno</option>
                                                                    <option value="manha">Manhã (06:00 - 14:00)</option>
                                                                    <option value="tarde">Tarde (14:00 - 22:00)</option>
                                                                    <option value="noite">Noite (22:00 - 06:00)</option>
                                                                    <option value="comercial">Comercial (08:00 - 17:00)</option>
                                                                    <option value="escala12x36">Escala 12x36</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="salario" class="form-label fw-semibold">
                                                                    <i class="bi bi-cash-coin me-1 text-primary"></i>Salário
                                                                </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text bg-light border-0 shadow-sm">R$</span>
                                                                    <input type="text" class="form-control border-0 shadow-sm bg-light" id="salario" name="salario" placeholder="0,00">
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <label for="observacoes" class="form-label fw-semibold">
                                                                    <i class="bi bi-chat-square-text me-1 text-primary"></i>Observações
                                                                </label>
                                                                <textarea class="form-control border-0 shadow-sm bg-light" id="observacoes" name="observacoes" rows="3" placeholder="Informações adicionais sobre a função do funcionário..."></textarea>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="certificacoes" class="form-label fw-semibold">
                                                                    <i class="bi bi-award me-1 text-primary"></i>Certificações
                                                                </label>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" value="vigilante" id="certVigilante" name="certificacoes[]">
                                                                    <label class="form-check-label" for="certVigilante">
                                                                        Curso de Vigilante
                                                                    </label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" value="reciclagem" id="certReciclagem" name="certificacoes[]">
                                                                    <label class="form-check-label" for="certReciclagem">
                                                                        Reciclagem em dia
                                                                    </label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" value="armadefogo" id="certArma" name="certificacoes[]">
                                                                    <label class="form-check-label" for="certArma">
                                                                        Porte de arma
                                                                    </label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" value="segurancapessoal" id="certSegPessoal" name="certificacoes[]">
                                                                    <label class="form-check-label" for="certSegPessoal">
                                                                        Segurança Pessoal
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer border-top-0 justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-2"></i>Cancelar
                                    </button>
                                    <div>
                                        <button type="button" class="btn btn-outline-primary rounded-pill px-4 me-2" id="btnLimpar">
                                            <i class="bi bi-eraser me-2"></i>Limpar
                                        </button>
                                        <button type="button" class="btn btn-primary rounded-pill px-4" id="btnSalvarFuncionario">
                                            <i class="bi bi-check-circle me-2"></i>Salvar Cadastro
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conteúdo principal -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-people-fill me-1"></i> Lista de Funcionários
                        </div>
                        <div>
                            <div class="input-group">
                                <input type="text" id="pesquisaFuncionario" class="form-control form-control-sm" placeholder="Pesquisar funcionário...">
                                <button class="btn btn-sm btn-outline-primary" type="button" id="btnPesquisar">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="tabelaFuncionarios">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>CPF</th>
                                        <th>Função</th>
                                        <th>Telefone</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="6" class="text-center">Carregando funcionários...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="paginacao" class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <span id="totalRegistros">0</span> registros encontrados
                            </div>
                            <nav aria-label="Paginação">
                                <ul class="pagination pagination-sm" id="paginacaoLinks">
                                    <!-- Links de paginação serão inseridos aqui via JavaScript -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JQuery (necessário para algumas funcionalidades) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Script de Debugging -->
    <script>
        // Função para log de erro personalizado
        window.onerror = function(message, source, lineno, colno, error) {
            console.error('Erro JavaScript:', message);
            console.error('Arquivo:', source);
            console.error('Linha:', lineno);
            console.error('Coluna:', colno);
            console.error('Objeto de erro:', error);

            // Criar elemento visual para mostrar erro
            var errorDiv = document.createElement('div');
            errorDiv.style.position = 'fixed';
            errorDiv.style.top = '10px';
            errorDiv.style.left = '10px';
            errorDiv.style.backgroundColor = 'red';
            errorDiv.style.color = 'white';
            errorDiv.style.padding = '10px';
            errorDiv.style.borderRadius = '5px';
            errorDiv.style.zIndex = '9999';
            errorDiv.innerHTML = 'Erro: ' + message + '<br>Arquivo: ' + source + '<br>Linha: ' + lineno;
            document.body.appendChild(errorDiv);

            // Remover após 10 segundos
            setTimeout(function() {
                document.body.removeChild(errorDiv);
            }, 10000);

            return false; // Permite que o erro seja processado normalmente
        };

        // Definir função adicionarEventosAcoes globalmente como fallback
        window.adicionarEventosAcoes = window.adicionarEventosAcoes || function() {
            console.log('[Fallback] Adicionando eventos aos botões');

            document.querySelectorAll('.btn-visualizar').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    console.log('Clique em visualizar ID:', id);
                    alert('Função de visualização não disponível. ID: ' + id);
                });
            });

            document.querySelectorAll('.btn-editar').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    console.log('Clique em editar ID:', id);
                    alert('Função de edição não disponível. ID: ' + id);
                });
            });

            document.querySelectorAll('.btn-status').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const status = this.getAttribute('data-status');
                    console.log('Clique em alterar status ID:', id, 'Status:', status);
                    alert('Função de alteração de status não disponível. ID: ' + id + ', Status: ' + status);
                });
            });
        };
    </script>

    <!-- Funções para ações de funcionários (visualizar, editar, alterar status) -->
    <script src="../assets/js/funcionarios-acoes.js"></script>
    <!-- Custom JS -->
    <script src="../assets/js/funcionarios.js"></script>
    <!-- Fix para o modal (versão avançada) -->
    <script src="../assets/js/modal-fix-enhanced.js"></script>
    <!-- Eventos de modal -->
    <script src="../assets/js/modal-events.js"></script>

    <!-- Script para garantir limpeza dos backdrops e reset de formulário -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Função para limpar todos os backdrops e restaurar o body
            function limparBackdrops() {
                // Remover todas as classes relacionadas à modal do body
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';

                // Remover backdrop manualmente
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(function(backdrop) {
                    backdrop.classList.remove('show');
                    backdrop.classList.remove('fade');
                    backdrop.parentNode.removeChild(backdrop);
                });

                console.log('Limpeza forçada de backdrops realizada');
            }

            // Limpar backdrops no carregamento inicial
            limparBackdrops();

            // Verificar backdrops a cada 3 segundos
            setInterval(function() {
                const hasBackdrop = document.querySelectorAll('.modal-backdrop').length > 0;
                const modals = document.querySelectorAll('.modal.show');

                if (hasBackdrop && modals.length === 0) {
                    console.log('Backdrop detectado sem modal ativa, limpando...');
                    limparBackdrops();
                }
            }, 3000);

            // Adicionar evento ao botão de cadastrar funcionário para limpar o formulário
            const btnCadastrarFuncionario = document.getElementById('btnCadastrarFuncionario');
            if (btnCadastrarFuncionario) {
                btnCadastrarFuncionario.addEventListener('click', function() {
                    console.log('Botão cadastrar funcionário clicado, limpando formulário');

                    // Resetar o formulário
                    const form = document.getElementById('formCadastroFuncionario');
                    if (form) {
                        // Limpar todos os campos
                        form.reset();

                        // Remover campo de ID se existir (usado na edição)
                        const idField = form.querySelector('input[name="id"]');
                        if (idField) {
                            idField.remove();
                        }

                        // Restaurar o modo do formulário para cadastro
                        form.setAttribute('data-mode', 'create');

                        // Restaurar o título do modal
                        const modalTitle = document.querySelector('#cadastroFuncionarioModalLabel');
                        if (modalTitle) {
                            modalTitle.innerHTML = '<i class="bi bi-person-plus-fill me-2"></i>Cadastro de Funcionário';
                        }

                        // Restaurar texto do botão salvar
                        const btnSalvar = document.getElementById('btnSalvarFuncionario');
                        if (btnSalvar) {
                            btnSalvar.textContent = 'Salvar';
                            btnSalvar.disabled = false;
                        }

                        console.log('Formulário resetado com sucesso');
                    } else {
                        console.error('Formulário não encontrado');
                    }
                });
            } else {
                console.error('Botão cadastrar funcionário não encontrado');
            }
        });
    </script>

    <!-- Verificação inicial dos botões após carregamento -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM completamente carregado no script de verificação');

            // Forçar o carregamento da tabela
            if (typeof window.carregarFuncionarios === 'function') {
                window.carregarFuncionarios();
            } else {
                console.error('Função carregarFuncionarios não está disponível');
            }

            // Forçar a adição de eventos aos botões de ação após um pequeno delay
            setTimeout(function() {
                console.log('Verificando botões de ação...');

                // Teste de disponibilidade das funções
                if (typeof window.visualizarFuncionario !== 'function') {
                    console.error('Função visualizarFuncionario não está disponível!');
                } else {
                    console.log('Função visualizarFuncionario está disponível');
                }

                if (typeof window.editarFuncionario !== 'function') {
                    console.error('Função editarFuncionario não está disponível!');
                } else {
                    console.log('Função editarFuncionario está disponível');
                }

                if (typeof window.alterarStatusFuncionario !== 'function') {
                    console.error('Função alterarStatusFuncionario não está disponível!');
                } else {
                    console.log('Função alterarStatusFuncionario está disponível');
                }

                // Adicionar novamente os listeners, por segurança
                document.querySelectorAll('.btn-visualizar').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        console.log('Clique em visualizar ID:', id);
                        window.visualizarFuncionario(id);
                    });
                });

                document.querySelectorAll('.btn-editar').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        console.log('Clique em editar ID:', id);
                        window.editarFuncionario(id);
                    });
                });

                document.querySelectorAll('.btn-status').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const status = this.getAttribute('data-status');
                        console.log('Clique em alterar status ID:', id, 'Para status:', status);
                        window.alterarStatusFuncionario(id, status, this);
                    });
                });

            }, 500);
        });
    </script>

    <!-- Script para conversão automática para maiúsculas e melhorias visuais -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Aplicar conversão automática para maiúsculas em campos de texto específicos
            const textInputs = document.querySelectorAll('input[type="text"]:not([id="cpf"]):not([id="cep"]):not([id="telefone"]):not([id="telefoneEmergencia"]):not([id="dataAdmissao"]):not([id="dataNascimento"]):not([id="email"])');
            
            textInputs.forEach(input => {
                // Adicionar classes para maiúsculo
                input.classList.add('text-uppercase-input');
                input.style.textTransform = 'uppercase';
                
                // Adicionar evento de input para conversão
                input.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            });

            // Adicionar animações aos cards
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.animationDelay = (index * 0.1) + 's';
            });

            // Adicionar efeitos visuais aos botões
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Melhorar feedback visual nos formulários
            const formControls = document.querySelectorAll('.form-control, .form-select');
            formControls.forEach(control => {
                control.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                    this.parentElement.style.transition = 'all 0.3s ease';
                });
                
                control.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });

            // Adicionar contador animado para estatísticas (se existirem)
            function animateCounter(element, target) {
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        element.textContent = target;
                        clearInterval(timer);
                    } else {
                        element.textContent = Math.floor(current);
                    }
                }, 30);
            }

            // Aplicar contador animado se houver elementos com classe 'counter'
            const counters = document.querySelectorAll('.counter');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent);
                if (!isNaN(target)) {
                    counter.textContent = '0';
                    animateCounter(counter, target);
                }
            });

            console.log('Scripts de melhorias visuais carregados com sucesso');
        });

        // Função para aplicar maiúsculo em tempo real em qualquer campo
        function applyUppercase(fieldId) {
            const field = document.getElementById(fieldId);
            if (field) {
                field.style.textTransform = 'uppercase';
                field.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            }
        }

        // Função para validar campos obrigatórios com feedback visual
        function validateRequiredFields() {
            const requiredFields = document.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    field.style.borderColor = '#dc3545';
                    field.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                    field.style.borderColor = '#28a745';
                    field.style.boxShadow = '0 0 0 0.2rem rgba(40, 167, 69, 0.25)';
                }
            });

            return isValid;
        }

        // Aplicar validação em tempo real
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formCadastroFuncionario');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!validateRequiredFields()) {
                        e.preventDefault();
                        // Adicionar shake animation ao modal
                        const modal = document.querySelector('.modal-content');
                        if (modal) {
                            modal.style.animation = 'shake 0.5s ease-in-out';
                            setTimeout(() => {
                                modal.style.animation = '';
                            }, 500);
                        }
                    }
                });
            }
        });

        // Adicionar keyframe para animação shake
        const style = document.createElement('style');
        style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }
        `;
        document.head.appendChild(style);

        // Carregar estatísticas dos funcionários
        function carregarEstatisticas() {
            fetch('listar_funcionarios.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.funcionarios) {
                        const funcionarios = data.funcionarios;
                        const total = funcionarios.length;
                        const ativos = funcionarios.filter(f => f.status === 'ativo').length;
                        const inativos = funcionarios.filter(f => f.status === 'inativo').length;
                        
                        // Calcular admissões deste mês
                        const hoje = new Date();
                        const mesAtual = hoje.getMonth();
                        const anoAtual = hoje.getFullYear();
                        
                        const admissoesEMes = funcionarios.filter(f => {
                            if (f.data_admissao) {
                                const dataAdmissao = new Date(f.data_admissao);
                                return dataAdmissao.getMonth() === mesAtual && dataAdmissao.getFullYear() === anoAtual;
                            }
                            return false;
                        }).length;

                        // Atualizar contadores com animação
                        setTimeout(() => {
                            animateCounter(document.getElementById('totalFuncionarios'), total);
                        }, 100);
                        
                        setTimeout(() => {
                            animateCounter(document.getElementById('funcionariosAtivos'), ativos);
                        }, 200);
                        
                        setTimeout(() => {
                            animateCounter(document.getElementById('funcionariosInativos'), inativos);
                        }, 300);
                        
                        setTimeout(() => {
                            animateCounter(document.getElementById('admissoesEMes'), admissoesEMes);
                        }, 400);
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar estatísticas:', error);
                });
        }

        // Carregar estatísticas quando a página carrega
        document.addEventListener('DOMContentLoaded', function() {
            carregarEstatisticas();
        });
    </script>
</body>

</html>