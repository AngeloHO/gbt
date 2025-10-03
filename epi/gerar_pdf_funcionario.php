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
    die('Erro ao buscar dados: ' . $e->getMessage());
} finally {
    mysqli_close($conn);
}

// Função para gerar HTML otimizado para PDF
function gerarHTMLFuncionarioPDF($dados, $funcionario_info, $funcionario_id) {
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $funcionario_id ? 'Relatório Individual de EPIs - PDF' : 'Relatório Geral por Funcionário - PDF' ?></title>
        <style>
            @page {
                margin: 15mm;
                size: A4 <?= $funcionario_id ? 'portrait' : 'landscape' ?>;
            }
            
            @media print {
                .no-print {
                    display: none !important;
                }
                
                body {
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
            }
            
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                font-size: 11px;
                line-height: 1.3;
                color: #333;
                margin: 0;
                padding: 10px;
                background-color: #fff;
            }
            
            .no-print {
                position: fixed;
                top: 10px;
                right: 10px;
                z-index: 1000;
                background: rgba(0, 0, 0, 0.8);
                padding: 10px;
                border-radius: 5px;
            }
            
            .btn {
                background: #007bff;
                color: white;
                border: none;
                padding: 8px 15px;
                border-radius: 4px;
                cursor: pointer;
                margin: 0 5px;
                font-size: 12px;
            }
            
            .btn:hover {
                background: #0056b3;
            }
            
            .btn-success {
                background: #28a745;
            }
            
            .btn-success:hover {
                background: #1e7e34;
            }
            
            .header {
                text-align: center;
                margin-bottom: 25px;
                border-bottom: 3px solid #2c3e50;
                padding-bottom: 15px;
                page-break-after: avoid;
            }
            
            .logo-text {
                font-size: 22px;
                font-weight: bold;
                color: #2c3e50;
                letter-spacing: 1.5px;
                margin-bottom: 5px;
            }
            
            .logo-subtitle {
                font-size: 11px;
                color: #7f8c8d;
                letter-spacing: 0.8px;
                margin-bottom: 12px;
            }
            
            .report-title {
                font-size: 18px;
                font-weight: bold;
                margin: 12px 0 8px 0;
                color: #2c3e50;
            }
            
            .report-generated {
                font-size: 9px;
                color: #999;
                margin-top: 10px;
            }
            
            .funcionario-info {
                margin: 20px 0;
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                padding: 18px;
                border-radius: 8px;
                border: 1px solid #dee2e6;
                page-break-inside: avoid;
            }
            
            .funcionario-nome {
                font-size: 16px;
                font-weight: bold;
                color: #2c3e50;
                margin-bottom: 10px;
                border-bottom: 2px solid #007bff;
                padding-bottom: 5px;
                display: inline-block;
            }
            
            .funcionario-detalhes {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
                margin-top: 10px;
            }
            
            .detalhe-item {
                background: white;
                padding: 10px;
                border-radius: 5px;
                border: 1px solid #dee2e6;
            }
            
            .detalhe-label {
                font-weight: bold;
                color: #666;
                font-size: 10px;
                margin-bottom: 3px;
            }
            
            .detalhe-valor {
                color: #333;
                font-size: 11px;
            }
            
            .table-section {
                margin-top: 25px;
                page-break-inside: avoid;
            }
            
            .table-title {
                font-size: 15px;
                font-weight: bold;
                margin-bottom: 12px;
                color: #2c3e50;
                border-bottom: 2px solid #007bff;
                padding-bottom: 5px;
                display: inline-block;
            }
            
            .table-container {
                overflow-x: auto;
                border: 1px solid #dee2e6;
                border-radius: 6px;
                margin-top: 10px;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                font-size: <?= $funcionario_id ? '9px' : '8px' ?>;
                background-color: white;
            }
            
            th {
                background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
                font-weight: bold;
                padding: 10px 6px;
                border: 1px solid #adb5bd;
                text-align: center;
                font-size: <?= $funcionario_id ? '9px' : '8px' ?>;
                color: #495057;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            td {
                padding: 8px 6px;
                border: 1px solid #dee2e6;
                text-align: center;
                font-size: <?= $funcionario_id ? '9px' : '8px' ?>;
                background-color: #fff;
            }
            
            tbody tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            
            tbody tr:hover {
                background-color: #e9ecef;
            }
            
            td.text-left {
                text-align: left;
            }
            
            .status-entregue, .status-ativo {
                background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
                color: #155724;
                padding: 3px 6px;
                border-radius: 4px;
                font-size: 7px;
                font-weight: bold;
                display: inline-block;
                min-width: 50px;
            }
            
            .status-devolvido {
                background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
                color: #0c5460;
                padding: 3px 6px;
                border-radius: 4px;
                font-size: 7px;
                font-weight: bold;
                display: inline-block;
                min-width: 50px;
            }
            
            .status-vencido {
                background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
                color: #721c24;
                padding: 3px 6px;
                border-radius: 4px;
                font-size: 7px;
                font-weight: bold;
                display: inline-block;
                min-width: 50px;
            }
            
            .badge {
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 8px;
                font-weight: bold;
                color: white;
                display: inline-block;
                min-width: 25px;
                text-align: center;
            }
            
            .badge-primary { background: #007bff; }
            .badge-success { background: #28a745; }
            .badge-info { background: #17a2b8; }
            
            .footer {
                position: fixed;
                bottom: 8mm;
                left: 0;
                right: 0;
                text-align: center;
                font-size: 8px;
                color: #666;
                background-color: white;
                padding: 5px 0;
                border-top: 1px solid #dee2e6;
            }
        </style>
        <script>
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            }
            
            function saveAsPDF() {
                window.print();
            }
            
            function downloadPDF() {
                const originalTitle = document.title;
                const filename = <?= $funcionario_id ? "'Relatorio_Funcionario_Individual_'" : "'Relatorio_Funcionarios_Geral_'" ?> + new Date().toISOString().split('T')[0];
                document.title = filename;
                window.print();
                document.title = originalTitle;
            }
        </script>
    </head>
    <body>
        <div class="no-print">
            <button class="btn" onclick="window.print()">🖨️ Imprimir</button>
            <button class="btn btn-success" onclick="downloadPDF()">📄 Salvar PDF</button>
            <button class="btn" onclick="window.close()">❌ Fechar</button>
        </div>
        
        <div class="header">
            <div class="logo-text">GEBERT SEGURANÇA PATRIMONIAL</div>
            <div class="logo-subtitle">SISTEMA DE CONTROLE DE EPI</div>
            <div class="report-title">
                <?= $funcionario_id ? '👤 Relatório Individual de EPIs' : '👥 Relatório Geral por Funcionário' ?>
            </div>
            <div class="report-generated">
                Gerado em: <?= date('d/m/Y H:i:s') ?> | Usuário: <?= $_SESSION['user']['nome'] ?? 'Sistema' ?>
            </div>
        </div>
        
        <?php if ($funcionario_info && $funcionario_id): ?>
            <div class="funcionario-info">
                <div class="funcionario-nome"><?= htmlspecialchars($funcionario_info['funcionario_nome']) ?></div>
                <div class="funcionario-detalhes">
                    <div class="detalhe-item">
                        <div class="detalhe-label">CPF</div>
                        <div class="detalhe-valor"><?= htmlspecialchars($funcionario_info['funcionario_cpf']) ?></div>
                    </div>
                    <div class="detalhe-item">
                        <div class="detalhe-label">Função</div>
                        <div class="detalhe-valor"><?= htmlspecialchars($funcionario_info['funcionario_funcao'] ?? 'Não informado') ?></div>
                    </div>
                    <?php if (!empty($funcionario_info['funcionario_telefone'])): ?>
                        <div class="detalhe-item">
                            <div class="detalhe-label">Telefone</div>
                            <div class="detalhe-valor"><?= htmlspecialchars($funcionario_info['funcionario_telefone']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (empty($dados)): ?>
            <div style="text-align: center; padding: 50px; background: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6;">
                <h4 style="color: #666;">ℹ️ Nenhum registro encontrado</h4>
                <p style="color: #999; font-size: 12px;">
                    <?= $funcionario_id ? 'Nenhuma entrega encontrada para este funcionário.' : 'Nenhum funcionário com entregas encontrado.' ?>
                </p>
            </div>
        <?php else: ?>
            <div class="table-section">
                <div class="table-title">
                    <?= $funcionario_id ? '📋 Histórico de Entregas' : '📊 Resumo por Funcionário' ?>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <?php if ($funcionario_id): ?>
                                    <th style="width: 8%;">ID</th>
                                    <th style="width: 25%;">EPI</th>
                                    <th style="width: 15%;">Data Entrega</th>
                                    <th style="width: 15%;">Prev. Devolução</th>
                                    <th style="width: 12%;">Status</th>
                                    <th style="width: 10%;">Assinatura</th>
                                    <th style="width: 15%;">Data Devolução</th>
                                <?php else: ?>
                                    <th style="width: 30%;">Funcionário</th>
                                    <th style="width: 20%;">Função</th>
                                    <th style="width: 12%;">Total</th>
                                    <th style="width: 12%;">Ativas</th>
                                    <th style="width: 12%;">Devolvidas</th>
                                    <th style="width: 14%;">Última Entrega</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($funcionario_id): ?>
                                <?php foreach ($dados as $entrega): ?>
                                    <tr>
                                        <td><strong><?= str_pad($entrega['id'], 4, '0', STR_PAD_LEFT) ?></strong></td>
                                        <td class="text-left">
                                            <strong><?= htmlspecialchars(substr($entrega['epi_nome'], 0, 25)) ?></strong>
                                            <?php if($entrega['epi_fabricante']): ?>
                                                <br><small style="color: #666;"><?= htmlspecialchars(substr($entrega['epi_fabricante'], 0, 20)) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($entrega['data_entrega'])) ?></td>
                                        <td>
                                            <?= $entrega['data_prevista'] ? date('d/m/Y', strtotime($entrega['data_prevista'])) : '<span style="color: #999;">-</span>' ?>
                                        </td>
                                        <td>
                                            <span class="status-<?= strtolower($entrega['status']) ?>">
                                                <?= htmlspecialchars($entrega['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($entrega['assinatura'] == '1'): ?>
                                                <span style="color: #28a745; font-weight: bold;">✓ Sim</span>
                                            <?php else: ?>
                                                <span style="color: #dc3545; font-weight: bold;">✗ Não</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($entrega['data_devolucao']): ?>
                                                <strong><?= date('d/m/Y', strtotime($entrega['data_devolucao'])) ?></strong>
                                            <?php else: ?>
                                                <span style="color: #999;">Pendente</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php foreach ($dados as $funcionario): ?>
                                    <tr>
                                        <td class="text-left">
                                            <strong><?= htmlspecialchars(substr($funcionario['funcionario_nome'], 0, 30)) ?></strong>
                                            <br><small style="color: #666;">CPF: <?= htmlspecialchars($funcionario['funcionario_cpf']) ?></small>
                                        </td>
                                        <td class="text-left"><?= htmlspecialchars(substr($funcionario['funcionario_funcao'] ?? 'Não informado', 0, 20)) ?></td>
                                        <td><span class="badge badge-primary"><?= $funcionario['total_entregas'] ?></span></td>
                                        <td><span class="badge badge-success"><?= $funcionario['entregas_ativas'] ?></span></td>
                                        <td><span class="badge badge-info"><?= $funcionario['entregas_devolvidas'] ?></span></td>
                                        <td>
                                            <?= $funcionario['ultima_entrega'] ? date('d/m/Y', strtotime($funcionario['ultima_entrega'])) : '<span style="color: #999;">-</span>' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="footer">
            Sistema Gebert - <?= $funcionario_id ? 'Relatório Individual de EPIs' : 'Relatório Geral por Funcionário' ?> - <?= date('d/m/Y H:i') ?> - Total: <?= count($dados) ?> registros
        </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}

// Gerar HTML e enviar
$html = gerarHTMLFuncionarioPDF($dados, $funcionario_info, $funcionario_id);

$filename = $funcionario_id ? 'relatorio_funcionario_individual_' . date('Y-m-d_H-i-s') . '.html' : 'relatorio_funcionarios_geral_' . date('Y-m-d_H-i-s') . '.html';

header('Content-Type: text/html; charset=UTF-8');
header('Content-Disposition: inline; filename="' . $filename . '"');

echo $html;
exit;
?>