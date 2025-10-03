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
$data_inicial = $_GET['data_inicial'] ?? date('Y-m-01');
$data_final = $_GET['data_final'] ?? date('Y-m-d');

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
    die('Erro ao buscar dados: ' . $e->getMessage());
} finally {
    mysqli_close($conn);
}

// Função para gerar HTML que será convertido em PDF usando o navegador
function gerarHTMLParaPDF($entregas, $total_entregas, $entregas_ativas, $entregas_devolvidas, $com_assinatura, $data_inicial, $data_final) {
    $data_inicio_br = date('d/m/Y', strtotime($data_inicial));
    $data_fim_br = date('d/m/Y', strtotime($data_final));
    
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Relatório de Entregas - PDF</title>
        <style>
            @page {
                margin: 15mm;
                size: A4 landscape;
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
            
            .report-period {
                font-size: 12px;
                color: #666;
                margin-bottom: 6px;
                font-weight: 500;
            }
            
            .report-generated {
                font-size: 9px;
                color: #999;
            }
            
            .stats-section {
                margin: 20px 0;
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                padding: 18px;
                border-radius: 8px;
                border: 1px solid #dee2e6;
                page-break-inside: avoid;
            }
            
            .stats-title {
                font-size: 15px;
                font-weight: bold;
                margin-bottom: 12px;
                color: #2c3e50;
                border-bottom: 2px solid #007bff;
                padding-bottom: 5px;
                display: inline-block;
            }
            
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 15px;
                margin-top: 15px;
            }
            
            .stat-item {
                text-align: center;
                padding: 12px 8px;
                background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
                border-radius: 6px;
                border: 1px solid #dee2e6;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            .stat-number {
                font-size: 20px;
                font-weight: bold;
                color: #007bff;
                display: block;
                text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
            }
            
            .stat-label {
                font-size: 10px;
                color: #666;
                margin-top: 4px;
                font-weight: 500;
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
                font-size: 9px;
                background-color: white;
            }
            
            th {
                background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
                font-weight: bold;
                padding: 10px 6px;
                border: 1px solid #adb5bd;
                text-align: center;
                font-size: 9px;
                color: #495057;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            td {
                padding: 8px 6px;
                border: 1px solid #dee2e6;
                text-align: center;
                font-size: 9px;
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
            
            .status-entregue {
                background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
                color: #0c5460;
                padding: 3px 6px;
                border-radius: 4px;
                font-size: 8px;
                font-weight: bold;
                display: inline-block;
                min-width: 60px;
            }
            
            .status-devolvido {
                background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
                color: #155724;
                padding: 3px 6px;
                border-radius: 4px;
                font-size: 8px;
                font-weight: bold;
                display: inline-block;
                min-width: 60px;
            }
            
            .status-ativo {
                background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
                color: #856404;
                padding: 3px 6px;
                border-radius: 4px;
                font-size: 8px;
                font-weight: bold;
                display: inline-block;
                min-width: 60px;
            }
            
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
                // Auto print dialog após carregar
                setTimeout(function() {
                    window.print();
                }, 500);
            }
            
            function saveAsPDF() {
                window.print();
            }
            
            function downloadPDF() {
                // Força o download como PDF através do print
                const originalTitle = document.title;
                document.title = 'Relatorio_Entregas_' + new Date().toISOString().split('T')[0];
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
            <div class="report-title">Relatório de Entregas de EPIs</div>
            <div class="report-period">Período: <?= $data_inicio_br ?> até <?= $data_fim_br ?></div>
            <div class="report-generated">
                Gerado em: <?= date('d/m/Y H:i:s') ?> | Usuário: <?= $_SESSION['user']['nome'] ?? 'Sistema' ?>
            </div>
        </div>
        
        <div class="stats-section">
            <div class="stats-title">📊 Resumo Executivo</div>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number"><?= $total_entregas ?></span>
                    <div class="stat-label">Total de Entregas</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?= $entregas_ativas ?></span>
                    <div class="stat-label">Entregas Ativas</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?= $entregas_devolvidas ?></span>
                    <div class="stat-label">Entregas Devolvidas</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?= $com_assinatura ?></span>
                    <div class="stat-label">Com Assinatura</div>
                </div>
            </div>
        </div>
        
        <div class="table-section">
            <div class="table-title">📋 Detalhamento das Entregas</div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 10%;">Data Entrega</th>
                            <th style="width: 25%;">Funcionário</th>
                            <th style="width: 20%;">EPI</th>
                            <th style="width: 12%;">Categoria</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 10%;">Devolução</th>
                            <th style="width: 8%;">Assinatura</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entregas as $entrega): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($entrega['id']) ?></strong></td>
                                <td><?= date('d/m/Y', strtotime($entrega['data_entrega'])) ?></td>
                                <td class="text-left">
                                    <strong><?= htmlspecialchars(substr($entrega['funcionario_nome'], 0, 28)) ?></strong>
                                    <br><small style="color: #666;"><?= htmlspecialchars($entrega['funcionario_funcao']) ?></small>
                                </td>
                                <td class="text-left">
                                    <strong><?= htmlspecialchars(substr($entrega['epi_nome'], 0, 22)) ?></strong>
                                    <?php if($entrega['epi_fabricante']): ?>
                                        <br><small style="color: #666;"><?= htmlspecialchars(substr($entrega['epi_fabricante'], 0, 15)) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-left"><?= htmlspecialchars(substr($entrega['epi_categoria'], 0, 15)) ?></td>
                                <td>
                                    <span class="status-<?= strtolower($entrega['status']) ?>">
                                        <?= htmlspecialchars($entrega['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($entrega['data_devolucao']): ?>
                                        <strong><?= date('d/m/Y', strtotime($entrega['data_devolucao'])) ?></strong>
                                    <?php else: ?>
                                        <span style="color: #999;">Pendente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($entrega['assinatura'] == '1'): ?>
                                        <span style="color: #28a745; font-weight: bold;">✓ Sim</span>
                                    <?php else: ?>
                                        <span style="color: #dc3545; font-weight: bold;">✗ Não</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="footer">
            Sistema Gebert - Relatório de Entregas de EPIs - <?= date('d/m/Y H:i') ?> - Total: <?= $total_entregas ?> registros
        </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}

// Gerar HTML e enviar como PDF (usando CSS para print)
$html = gerarHTMLParaPDF($entregas, $total_entregas, $entregas_ativas, $entregas_devolvidas, $com_assinatura, $data_inicial, $data_final);

// Headers para forçar download como PDF (o navegador pode converter)
$filename = 'relatorio_entregas_' . date('Y-m-d_H-i-s') . '.html';

header('Content-Type: text/html; charset=UTF-8');
header('Content-Disposition: inline; filename="' . $filename . '"');

echo $html;
exit;
?>