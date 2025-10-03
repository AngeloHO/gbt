<?php
require_once '../config/conexao.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Conecta ao banco de dados
$conn = connect_local_mysqli('gebert');

// Obter parâmetros de data
$data_inicial = $_GET['data_inicial'] ?? date('Y-m-01'); // Primeiro dia do mês atual se não informado
$data_final = $_GET['data_final'] ?? date('Y-m-d'); // Data atual se não informado

// Validar formato das datas
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_inicial) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_final)) {
    echo "Formato de data inválido. Use YYYY-MM-DD";
    exit;
}

try {
    // Buscar entregas no período
    $sql = "SELECT 
                e.entrega_id as id,
                e.entrega_data_entrega as data_entrega,
                e.entrega_data_prevista_devolucao as data_prevista,
                e.entrega_data_devolucao as data_devolucao,
                e.entrega_motivo_entrega as motivo,
                e.entrega_observacoes as observacoes,
                e.entrega_assinatura_funcionario as assinatura,
                e.entrega_status as status,
                f.fun_nome_completo as funcionario_nome,
                f.fun_cpf as funcionario_cpf,
                f.fun_funcao as funcionario_funcao,
                ep.epi_nome as epi_nome,
                ep.epi_categoria as epi_categoria,
                ep.epi_fabricante as epi_fabricante
            FROM EPI_ENTREGAS e
            INNER JOIN FUN_FUNCIONARIO f ON e.entrega_funcionario_id = f.fun_id
            INNER JOIN EPI_EQUIPAMENTOS ep ON e.entrega_epi_id = ep.epi_id
            WHERE DATE(e.entrega_data_entrega) BETWEEN '$data_inicial' AND '$data_final'
            ORDER BY e.entrega_data_entrega DESC, f.fun_nome_completo ASC";
    
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        throw new Exception('Erro na consulta: ' . mysqli_error($conn));
    }
    
    $entregas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $entregas[] = $row;
    }
    
    // Estatísticas
    $total_entregas = count($entregas);
    $entregas_ativas = count(array_filter($entregas, function($e) { return $e['status'] == 'ENTREGUE' || $e['status'] == 'ATIVO'; }));
    $entregas_devolvidas = count(array_filter($entregas, function($e) { return $e['status'] == 'DEVOLVIDO'; }));
    $com_assinatura = count(array_filter($entregas, function($e) { return $e['assinatura'] == '1'; }));
    
} catch (Exception $e) {
    $erro = $e->getMessage();
} finally {
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Entregas - Sistema Gebert</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                font-size: 12px;
            }
            
            .table {
                font-size: 11px;
            }
            
            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
        
        .logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            letter-spacing: 2px;
        }
        
        .logo-subtitle {
            font-size: 12px;
            color: #7f8c8d;
            letter-spacing: 1px;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 12px;
        }
        
        .badge-status {
            font-size: 10px;
            padding: 4px 8px;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Cabeçalho -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <div class="logo-text">GEBERT SEGURANÇA PATRIMONIAL</div>
                <div class="logo-subtitle">SISTEMA DE CONTROLE DE EPI</div>
                <h2 class="mt-3 mb-1">Relatório de Entregas de EPIs</h2>
                <p class="text-muted">
                    Período: <?= date('d/m/Y', strtotime($data_inicial)) ?> até <?= date('d/m/Y', strtotime($data_final)) ?>
                </p>
                <p class="text-muted small">
                    Gerado em: <?= date('d/m/Y H:i:s') ?> | Usuário: <?= $_SESSION['user']['nome'] ?? 'Sistema' ?>
                </p>
            </div>
        </div>
        
        <!-- Botões de Ação -->
        <div class="row mb-3 no-print">
            <div class="col-12">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer me-1"></i>Imprimir
                </button>
                <button onclick="window.close()" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i>Fechar
                </button>
            </div>
        </div>
        
        <?php if (isset($erro)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Erro ao gerar relatório: <?= htmlspecialchars($erro) ?>
            </div>
        <?php else: ?>
            
            <!-- Estatísticas -->
            <div class="row mb-4">
                <div class="col-md-3 col-6 mb-2">
                    <div class="card text-center">
                        <div class="card-body py-2">
                            <h3 class="text-primary mb-0"><?= $total_entregas ?></h3>
                            <small class="text-muted">Total de Entregas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <div class="card text-center">
                        <div class="card-body py-2">
                            <h3 class="text-success mb-0"><?= $entregas_ativas ?></h3>
                            <small class="text-muted">Entregas Ativas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <div class="card text-center">
                        <div class="card-body py-2">
                            <h3 class="text-info mb-0"><?= $entregas_devolvidas ?></h3>
                            <small class="text-muted">Devolvidas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <div class="card text-center">
                        <div class="card-body py-2">
                            <h3 class="text-warning mb-0"><?= $com_assinatura ?></h3>
                            <small class="text-muted">Com Assinatura</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabela de Entregas -->
            <?php if (empty($entregas)): ?>
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle me-1"></i>
                    Nenhuma entrega encontrada no período selecionado.
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 8%;">ID</th>
                                        <th style="width: 22%;">Funcionário</th>
                                        <th style="width: 20%;">EPI</th>
                                        <th style="width: 12%;">Data Entrega</th>
                                        <th style="width: 12%;">Prev. Devolução</th>
                                        <th style="width: 10%;">Status</th>
                                        <th style="width: 8%;">Assinatura</th>
                                        <th style="width: 8%;">Devolvido</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($entregas as $entrega): ?>
                                        <tr>
                                            <td><?= str_pad($entrega['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($entrega['funcionario_nome']) ?></strong>
                                                <br><small class="text-muted"><?= htmlspecialchars($entrega['funcionario_funcao'] ?? '') ?></small>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($entrega['epi_nome']) ?></strong>
                                                <br><small class="text-muted"><?= htmlspecialchars($entrega['epi_fabricante']) ?></small>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($entrega['data_entrega'])) ?></td>
                                            <td>
                                                <?= $entrega['data_prevista'] ? date('d/m/Y', strtotime($entrega['data_prevista'])) : '-' ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status = $entrega['status'];
                                                $badge_class = 'bg-secondary';
                                                if ($status == 'ENTREGUE' || $status == 'ATIVO') $badge_class = 'bg-success';
                                                elseif ($status == 'DEVOLVIDO') $badge_class = 'bg-info';
                                                elseif ($status == 'VENCIDO') $badge_class = 'bg-danger';
                                                ?>
                                                <span class="badge <?= $badge_class ?> badge-status"><?= htmlspecialchars($status) ?></span>
                                            </td>
                                            <td>
                                                <?php if ($entrega['assinatura'] == '1'): ?>
                                                    <i class="bi bi-check-circle-fill text-success"></i> Sim
                                                <?php else: ?>
                                                    <i class="bi bi-x-circle text-danger"></i> Não
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= $entrega['data_devolucao'] ? date('d/m/Y', strtotime($entrega['data_devolucao'])) : '-' ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
        <?php endif; ?>
        
        <!-- Rodapé -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <small class="text-muted">
                    Sistema Gebert - Controle de EPIs | 
                    Total de registros: <?= $total_entregas ?> | 
                    Página gerada automaticamente
                </small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>