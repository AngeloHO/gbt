<?php
require_once '../config/conexao.php';
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

$conn = connect_local_mysqli('gebert');

// Verificar se a conexão foi estabelecida
if (!$conn) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro de conexão com o banco de dados'
    ]);
    exit;
}

// Função para verificar se uma tabela existe
function tabelaExiste($conn, $tabela) {
    $query = "SHOW TABLES LIKE '$tabela'";
    $result = mysqli_query($conn, $query);
    return $result && mysqli_num_rows($result) > 0;
}

// Função para buscar estatísticas do sistema
function obterEstatisticas($conn) {
    $stats = [];
    
    // Total de funcionários ativos
    if (tabelaExiste($conn, 'fun_funcionario')) {
        $query = "SELECT COUNT(*) as total FROM fun_funcionario WHERE fun_status = 'ativo'";
        $result = mysqli_query($conn, $query);
        if ($result) {
            $stats['funcionarios_ativos'] = mysqli_fetch_assoc($result)['total'];
        } else {
            $stats['funcionarios_ativos'] = 0;
        }
        
        // Total de funcionários
        $query = "SELECT COUNT(*) as total FROM fun_funcionario";
        $result = mysqli_query($conn, $query);
        if ($result) {
            $stats['total_funcionarios'] = mysqli_fetch_assoc($result)['total'];
        } else {
            $stats['total_funcionarios'] = 0;
        }
    } else {
        $stats['funcionarios_ativos'] = 0;
        $stats['total_funcionarios'] = 0;
    }
    
    // ASOs vencendo nos próximos 30 dias
    if (tabelaExiste($conn, 'fun_aso')) {
        $query = "SELECT COUNT(*) as total FROM fun_aso WHERE ASO_DATA_VALIDADE BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND ASO_STATUS = 'ATIVO'";
        $result = mysqli_query($conn, $query);
        $stats['asos_vencendo'] = $result ? mysqli_fetch_assoc($result)['total'] : 0;
    } else {
        $stats['asos_vencendo'] = 0;
    }
    
    // EPIs entregues (não devolvidos)
    if (tabelaExiste($conn, 'epi_entregas')) {
        $query = "SELECT COUNT(*) as total FROM epi_entregas WHERE entrega_status = 'entregue'";
        $result = mysqli_query($conn, $query);
        $stats['epis_entregues'] = $result ? mysqli_fetch_assoc($result)['total'] : 0;
    } else {
        $stats['epis_entregues'] = 0;
    }
    
    // Total de equipamentos EPI cadastrados
    if (tabelaExiste($conn, 'epi_equipamentos')) {
        $query = "SELECT COUNT(*) as total FROM epi_equipamentos WHERE epi_status = 'ativo'";
        $result = mysqli_query($conn, $query);
        $stats['total_epis'] = $result ? mysqli_fetch_assoc($result)['total'] : 0;
    } else {
        $stats['total_epis'] = 0;
    }
    
    // Desligamentos este mês
    if (tabelaExiste($conn, 'des_desligamento')) {
        $query = "SELECT COUNT(*) as total FROM des_desligamento WHERE MONTH(des_data_desligamento) = MONTH(CURDATE()) AND YEAR(des_data_desligamento) = YEAR(CURDATE())";
        $result = mysqli_query($conn, $query);
        $stats['desligamentos_mes'] = $result ? mysqli_fetch_assoc($result)['total'] : 0;
    } else {
        $stats['desligamentos_mes'] = 0;
    }
    
    return $stats;
}

try {
    $estatisticas = obterEstatisticas($conn);
    
    // Fechar conexão
    if ($conn) {
        mysqli_close($conn);
    }
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'data' => $estatisticas
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Fechar conexão em caso de erro
    if ($conn) {
        mysqli_close($conn);
    }
    
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao carregar estatísticas: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} catch (Error $e) {
    // Capturar erros fatais
    if ($conn) {
        mysqli_close($conn);
    }
    
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'error' => 'Erro fatal: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>