<?php
session_start();

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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        :root {
            --primary-color: #1e3a8a;
            --secondary-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
        }

        body {
            background-color: var(--light-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            border-radius: 0 20px 20px 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-link {
            padding: 12px 20px;
            color: #adb5bd;
            text-decoration: none;
            display: flex;
            align-items: center;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            margin: 5px 10px;
            border-radius: 10px;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-left-color: #0d6efd;
            transform: translateX(10px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .sidebar-link i {
            width: 24px;
            text-align: center;
            margin-right: 10px;
            font-size: 1.1em;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 3px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.4);
        }

        .logo-subtitle {
            font-size: 11px;
            letter-spacing: 2px;
            color: #adb5bd;
            font-weight: 300;
        }

        .dashboard-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .card-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }

        .card-number {
            font-size: 2.8rem;
            font-weight: 700;
            margin: 0;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0;
        }

        .gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-success {
            background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
        }

        .gradient-warning {
            background: linear-gradient(135deg, #fdbb2d 0%, #22c1c3 100%);
        }

        .gradient-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #fa5252 100%);
        }

        .gradient-info {
            background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);
        }

        .gradient-purple {
            background: linear-gradient(135deg, #9775fa 0%, #7048e8 100%);
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin: 20px 0;
        }

        .activity-item {
            padding: 15px;
            border-left: 4px solid #667eea;
            margin-bottom: 10px;
            background: white;
            border-radius: 0 10px 10px 0;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            transform: translateX(5px);
            border-left-color: #764ba2;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
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

        .dashboard-card {
            animation: fadeInUp 0.6s ease;
        }

        /* Efeito glass morphism */
        .glass-effect {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .stats-row {
            margin-bottom: 30px;
        }

        .main-content {
            padding: 30px;
        }

        .alert-custom {
            border: none;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .navbar-custom {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 0 0 15px 15px;
            margin-bottom: 20px;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .card-number {
                font-size: 2rem;
            }
            
            .sidebar {
                margin-bottom: 20px;
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .fade-in {
            animation: fadeIn 0.8s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
                            <a class="sidebar-link active" href="#">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="funcionarios/funcionarios.php">
                                <i class="bi bi-people"></i> Funcionários
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="epi/epi.php">
                                <i class="bi bi-shield-check"></i> EPI
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="aso/aso.php">
                                <i class="bi bi-file-medical"></i> ASO
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="desligamento/desligamento.php">
                                <i class="bi bi-person-x"></i> Desligamentos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="avaliacoes/avaliacoes.php">
                                <i class="bi bi-clipboard-check"></i> Avaliações
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
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
                <div class="navbar-custom d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                    <h1 class="h2 mb-0">
                        <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard
                    </h1>
                    <div class="dropdown">
                        <a href="#" class="d-block text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($user['nome'] ?? 'Usuário'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-1"></i> Perfil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-1"></i> Configurações</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i> Sair</a></li>
                        </ul>
                    </div>
                </div>

                <div class="alert alert-custom text-white fade-in" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-3" style="font-size: 1.5rem;"></i>
                        <div>
                            <h5 class="mb-1">Bem-vindo ao Sistema Gebert!</h5>
                        </div>
                    </div>
                </div>

                <!-- Cards de Estatísticas -->
                <div class="row stats-row">
                    <div class="col-md-6 col-lg-2 mb-4">
                        <div class="card dashboard-card gradient-primary text-white fade-in">
                            <div class="card-body text-center">
                                <i class="bi bi-people-fill card-icon"></i>
                                <h2 class="card-number" id="funcionarios-ativos">-</h2>
                                <p class="card-title">Funcionários Ativos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-2 mb-4">
                        <div class="card dashboard-card gradient-success text-white fade-in">
                            <div class="card-body text-center">
                                <i class="bi bi-shield-check card-icon"></i>
                                <h2 class="card-number" id="total-epis">-</h2>
                                <p class="card-title">EPIs Cadastrados</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-2 mb-4">
                        <div class="card dashboard-card gradient-warning text-white fade-in">
                            <div class="card-body text-center">
                                <i class="bi bi-exclamation-triangle card-icon"></i>
                                <h2 class="card-number" id="asos-vencendo">-</h2>
                                <p class="card-title">ASOs Vencendo</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-2 mb-4">
                        <div class="card dashboard-card gradient-info text-white fade-in">
                            <div class="card-body text-center">
                                <i class="bi bi-box-seam card-icon"></i>
                                <h2 class="card-number" id="epis-entregues">-</h2>
                                <p class="card-title">EPIs Entregues</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-2 mb-4">
                        <div class="card dashboard-card gradient-danger text-white fade-in">
                            <div class="card-body text-center">
                                <i class="bi bi-person-x card-icon"></i>
                                <h2 class="card-number" id="desligamentos-mes">-</h2>
                                <p class="card-title">Desligamentos (Mês)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-2 mb-4">
                        <div class="card dashboard-card gradient-purple text-white fade-in">
                            <div class="card-body text-center">
                                <i class="bi bi-people card-icon"></i>
                                <h2 class="card-number" id="total-funcionarios">-</h2>
                                <p class="card-title">Total Funcionários</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos e Atividades -->
                <div class="row">
                    <!-- Gráfico de ASOs -->
                    <div class="col-lg-8 mb-4">
                        <div class="card dashboard-card fade-in">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-bar-chart me-2 text-primary"></i>Status dos ASOs
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="asoChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Atividades Recentes -->
                    <div class="col-lg-4 mb-4">
                        <div class="card dashboard-card fade-in">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-clock-history me-2 text-primary"></i>Atividades Recentes
                                </h5>
                            </div>
                            <div class="card-body" style="max-height: 350px; overflow-y: auto;" id="atividades-container">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Carregando atividades...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alertas e Notificações -->
                <div class="row mb-4" id="alerta-asos" style="display: none;">
                    <div class="col-12">
                        <div class="card dashboard-card border-warning fade-in">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Atenção Necessária
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning mb-0">
                                    <i class="bi bi-file-medical me-2"></i>
                                    <strong id="quantidade-asos-vencendo">0</strong> ASO(s) vencendo nos próximos 30 dias. 
                                    <a href="aso/aso.php" class="alert-link">Clique aqui para verificar</a>.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumo Geral -->
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <div class="card dashboard-card fade-in">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-info-circle me-2 text-primary"></i>Resumo do Sistema
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <div class="border-end">
                                            <h4 class="text-primary mb-1" id="percentual-funcionarios-ativos">-</h4>
                                            <small class="text-muted">Funcionários Ativos</small>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <h4 class="text-success mb-1" id="epis-em-uso">-</h4>
                                        <small class="text-muted">EPIs em Uso</small>
                                    </div>
                                </div>
                                <hr>
                                <p class="mb-0 text-center text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    Última atualização: <span id="ultima-atualizacao">-</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Variáveis globais para armazenar dados
        let estatisticas = {};
        let chartASO = null;

        // Configuração dos gráficos
        document.addEventListener('DOMContentLoaded', function() {
            carregarDashboard();
        });

        // Função principal para carregar todos os dados do dashboard
        async function carregarDashboard() {
            try {
                await carregarEstatisticas();
                await carregarAtividades();
                configurarGraficos();
                atualizarResumo();
                verificarAlertas();
                
                // Auto-refresh dos dados a cada 5 minutos
                setInterval(carregarDashboard, 300000);
            } catch (error) {
                console.error('Erro ao carregar dashboard:', error);
                showNotification('Erro ao carregar dados do dashboard', 'error');
            }
        }

        // Carregar estatísticas via AJAX
        async function carregarEstatisticas() {
            try {
                const response = await fetch('api/estatisticas.php');
                const data = await response.json();
                
                if (data.success) {
                    estatisticas = data.data;
                    atualizarCards(estatisticas);
                    animarContadores();
                } else {
                    throw new Error(data.error || 'Erro ao carregar estatísticas');
                }
            } catch (error) {
                console.error('Erro ao carregar estatísticas:', error);
                showNotification('Erro ao carregar estatísticas', 'error');
            }
        }

        // Carregar atividades via AJAX
        async function carregarAtividades() {
            try {
                const response = await fetch('api/atividades.php');
                const data = await response.json();
                
                if (data.success) {
                    renderizarAtividades(data.data);
                } else {
                    throw new Error(data.error || 'Erro ao carregar atividades');
                }
            } catch (error) {
                console.error('Erro ao carregar atividades:', error);
                document.getElementById('atividades-container').innerHTML = `
                    <p class="text-muted text-center">
                        <i class="bi bi-exclamation-circle me-2"></i>Erro ao carregar atividades.
                    </p>
                `;
            }
        }

        // Atualizar cards com os dados das estatísticas
        function atualizarCards(stats) {
            document.getElementById('funcionarios-ativos').textContent = stats.funcionarios_ativos || 0;
            document.getElementById('total-epis').textContent = stats.total_epis || 0;
            document.getElementById('asos-vencendo').textContent = stats.asos_vencendo || 0;
            document.getElementById('epis-entregues').textContent = stats.epis_entregues || 0;
            document.getElementById('desligamentos-mes').textContent = stats.desligamentos_mes || 0;
            document.getElementById('total-funcionarios').textContent = stats.total_funcionarios || 0;
        }

        // Renderizar atividades na interface
        function renderizarAtividades(atividades) {
            const container = document.getElementById('atividades-container');
            
            if (!atividades || atividades.length === 0) {
                container.innerHTML = `
                    <p class="text-muted text-center">
                        <i class="bi bi-info-circle me-2"></i>Nenhuma atividade recente encontrada.
                    </p>
                `;
                return;
            }

            let html = '';
            atividades.forEach(atividade => {
                const tipoClass = atividade.tipo === 'funcionario' ? 'gradient-primary' : 'gradient-success';
                const dataFormatada = formatarDataHora(atividade.data);
                
                html += `
                    <div class="activity-item">
                        <div class="d-flex align-items-center">
                            <div class="activity-icon ${tipoClass}">
                                <i class="bi ${atividade.icone}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-1 fw-semibold">${escapeHtml(atividade.descricao)}</p>
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    ${dataFormatada}
                                </small>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        // Configurar gráficos
        function configurarGraficos() {
            const asoCtx = document.getElementById('asoChart').getContext('2d');
            
            // Destruir gráfico anterior se existir
            if (chartASO) {
                chartASO.destroy();
            }
            
            chartASO = new Chart(asoCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Válidos', 'Vencendo em 30 dias', 'Vencidos'],
                    datasets: [{
                        data: [
                            Math.max(0, (estatisticas.total_funcionarios || 0) - (estatisticas.asos_vencendo || 0)),
                            estatisticas.asos_vencendo || 0,
                            5  // Exemplo de ASOs vencidos
                        ],
                        backgroundColor: [
                            '#10b981',
                            '#f59e0b',
                            '#ef4444'
                        ],
                        borderWidth: 3,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }

        // Atualizar resumo do sistema
        function atualizarResumo() {
            const totalFunc = Math.max(estatisticas.total_funcionarios || 1, 1);
            const ativosFunc = estatisticas.funcionarios_ativos || 0;
            const percentual = ((ativosFunc / totalFunc) * 100).toFixed(1);
            
            document.getElementById('percentual-funcionarios-ativos').textContent = percentual + '%';
            document.getElementById('epis-em-uso').textContent = estatisticas.epis_entregues || 0;
            document.getElementById('ultima-atualizacao').textContent = new Date().toLocaleString('pt-BR');
        }

        // Verificar e exibir alertas
        function verificarAlertas() {
            const alertaAsos = document.getElementById('alerta-asos');
            const quantidadeAsos = document.getElementById('quantidade-asos-vencendo');
            
            if (estatisticas.asos_vencendo > 0) {
                quantidadeAsos.textContent = estatisticas.asos_vencendo;
                alertaAsos.style.display = 'block';
                
                // Notificação
                setTimeout(() => {
                    showNotification(
                        `<i class="bi bi-exclamation-triangle me-2"></i><strong>Atenção:</strong> ${estatisticas.asos_vencendo} ASO(s) vencendo em breve!`,
                        'warning'
                    );
                }, 2000);
            } else {
                alertaAsos.style.display = 'none';
            }
        }

        // Animação para os números dos cards
        function animarContadores() {
            const cardNumbers = document.querySelectorAll('.card-number');
            cardNumbers.forEach(card => {
                const finalValue = parseInt(card.textContent) || 0;
                let currentValue = 0;
                const increment = finalValue / 30;
                
                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= finalValue) {
                        currentValue = finalValue;
                        clearInterval(timer);
                    }
                    card.textContent = Math.floor(currentValue);
                }, 50);
            });
        }

        // Função para mostrar notificações
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 1050; max-width: 350px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }

        // Função auxiliar para formatar data e hora
        function formatarDataHora(dataHora) {
            const data = new Date(dataHora);
            return data.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Função auxiliar para escapar HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>

</html>