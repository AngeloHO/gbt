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

// Obter parâmetro de funcionário
$funcionario_id = $_GET['funcionario_id'] ?? null;

try {
    if ($funcionario_id) {
        // Buscar entregas de um funcionário específico
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
                    f.fun_telefone as funcionario_telefone,
                    ep.epi_nome as epi_nome,
                    ep.epi_categoria as epi_categoria,
                    ep.epi_fabricante as epi_fabricante
                FROM EPI_ENTREGAS e
                INNER JOIN FUN_FUNCIONARIO f ON e.entrega_funcionario_id = f.fun_id
                INNER JOIN EPI_EQUIPAMENTOS ep ON e.entrega_epi_id = ep.epi_id
                WHERE f.fun_id = $funcionario_id
                ORDER BY e.entrega_data_entrega DESC";
        
        $funcionario_info = null;
    } else {
        // Buscar todas as entregas com resumo por funcionário
        $sql = "SELECT 
                    f.fun_id as funcionario_id,
                    f.fun_nome_completo as funcionario_nome,
                    f.fun_cpf as funcionario_cpf,
                    f.fun_funcao as funcionario_funcao,
                    COUNT(e.entrega_id) as total_entregas,
                    SUM(CASE WHEN e.entrega_status IN ('ENTREGUE', 'ATIVO') THEN 1 ELSE 0 END) as entregas_ativas,
                    SUM(CASE WHEN e.entrega_status = 'DEVOLVIDO' THEN 1 ELSE 0 END) as entregas_devolvidas,
                    MAX(e.entrega_data_entrega) as ultima_entrega
                FROM FUN_FUNCIONARIO f
                LEFT JOIN EPI_ENTREGAS e ON f.fun_id = e.entrega_funcionario_id
                WHERE f.fun_status = 'ativo'
                GROUP BY f.fun_id, f.fun_nome_completo, f.fun_cpf, f.fun_funcao
                HAVING COUNT(e.entrega_id) > 0
                ORDER BY f.fun_nome_completo ASC";
        
        $funcionario_info = false; // Indica que é relatório geral
    }
    
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        throw new Exception('Erro na consulta: ' . mysqli_error($conn));
    }
    
    $dados = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $dados[] = $row;
    }
    
    // Se for funcionário específico, buscar informações do funcionário
    if ($funcionario_id && !empty($dados)) {
        $funcionario_info = $dados[0];
    }
    
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
    <title>Relatório por Funcionário - Sistema Gebert</title>
    
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
                <h2 class="mt-3 mb-1">
                    <?= $funcionario_id ? 'Relatório Individual de EPIs' : 'Relatório Geral por Funcionário' ?>
                </h2>
                <?php if ($funcionario_info && $funcionario_id): ?>
                    <div class="card mt-3 mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($funcionario_info['funcionario_nome']) ?></h5>
                            <p class="card-text">
                                <strong>CPF:</strong> <?= htmlspecialchars($funcionario_info['funcionario_cpf']) ?><br>
                                <strong>Função:</strong> <?= htmlspecialchars($funcionario_info['funcionario_funcao'] ?? 'Não informado') ?><br>
                                <?php if (!empty($funcionario_info['funcionario_telefone'])): ?>
                                    <strong>Telefone:</strong> <?= htmlspecialchars($funcionario_info['funcionario_telefone']) ?><br>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
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
            
            <?php if (empty($dados)): ?>
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle me-1"></i>
                    <?= $funcionario_id ? 'Nenhuma entrega encontrada para este funcionário.' : 'Nenhum funcionário com entregas encontrado.' ?>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <?php if ($funcionario_id): ?>
                                <!-- Relatório individual detalhado -->
                                <table class="table table-striped table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 8%;">ID</th>
                                            <th style="width: 25%;">EPI</th>
                                            <th style="width: 15%;">Data Entrega</th>
                                            <th style="width: 15%;">Prev. Devolução</th>
                                            <th style="width: 12%;">Status</th>
                                            <th style="width: 10%;">Assinatura</th>
                                            <th style="width: 15%;">Data Devolução</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dados as $entrega): ?>
                                            <tr>
                                                <td><?= str_pad($entrega['id'], 4, '0', STR_PAD_LEFT) ?></td>
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
                            <?php else: ?>
                                <!-- Relatório geral resumido -->
                                <table class="table table-striped table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%;">Funcionário</th>
                                            <th style="width: 20%;">Função</th>
                                            <th style="width: 12%;">Total</th>
                                            <th style="width: 12%;">Ativas</th>
                                            <th style="width: 12%;">Devolvidas</th>
                                            <th style="width: 14%;">Última Entrega</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dados as $funcionario): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($funcionario['funcionario_nome']) ?></strong>
                                                    <br><small class="text-muted">CPF: <?= htmlspecialchars($funcionario['funcionario_cpf']) ?></small>
                                                </td>
                                                <td><?= htmlspecialchars($funcionario['funcionario_funcao'] ?? 'Não informado') ?></td>
                                                <td><span class="badge bg-primary"><?= $funcionario['total_entregas'] ?></span></td>
                                                <td><span class="badge bg-success"><?= $funcionario['entregas_ativas'] ?></span></td>
                                                <td><span class="badge bg-info"><?= $funcionario['entregas_devolvidas'] ?></span></td>
                                                <td>
                                                    <?= $funcionario['ultima_entrega'] ? date('d/m/Y', strtotime($funcionario['ultima_entrega'])) : '-' ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
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
                    Total de registros: <?= count($dados) ?> | 
                    Página gerada automaticamente
                </small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>