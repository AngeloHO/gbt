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

// Função para verificar se uma tabela existe
function tabelaExiste($conn, $tabela) {
    $query = "SHOW TABLES LIKE '$tabela'";
    $result = mysqli_query($conn, $query);
    return $result && mysqli_num_rows($result) > 0;
}

// Função para obter atividades recentes
function obterAtividadesRecentes($conn) {
    $atividades = [];
    
    // Últimos funcionários cadastrados
    if (tabelaExiste($conn, 'fun_funcionario')) {
        $query = "SELECT fun_nome_completo, fun_data_cadastro FROM fun_funcionario ORDER BY fun_data_cadastro DESC LIMIT 5";
        $result = mysqli_query($conn, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $atividades[] = [
                    'tipo' => 'funcionario',
                    'descricao' => 'Funcionário cadastrado: ' . $row['fun_nome_completo'],
                    'data' => $row['fun_data_cadastro'],
                    'icone' => 'bi-person-plus'
                ];
            }
        }
    }
    
    // Últimas entregas de EPI
    if (tabelaExiste($conn, 'epi_entregas') && tabelaExiste($conn, 'fun_funcionario') && tabelaExiste($conn, 'epi_equipamentos')) {
        $query = "SELECT e.entrega_data_entrega, f.fun_nome_completo, ep.epi_nome 
                  FROM epi_entregas e 
                  JOIN fun_funcionario f ON e.entrega_funcionario_id = f.fun_id 
                  JOIN epi_equipamentos ep ON e.entrega_epi_id = ep.epi_id 
                  ORDER BY e.entrega_data_entrega DESC LIMIT 5";
        $result = mysqli_query($conn, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $atividades[] = [
                    'tipo' => 'epi',
                    'descricao' => 'EPI entregue: ' . $row['epi_nome'] . ' para ' . $row['fun_nome_completo'],
                    'data' => $row['entrega_data_entrega'],
                    'icone' => 'bi-shield-check'
                ];
            }
        }
    }
    
    // Se não houver atividades reais, adicionar algumas de exemplo
    if (empty($atividades)) {
        $atividades = [
            [
                'tipo' => 'sistema',
                'descricao' => 'Sistema iniciado com sucesso',
                'data' => date('Y-m-d H:i:s'),
                'icone' => 'bi-check-circle'
            ],
            [
                'tipo' => 'sistema',
                'descricao' => 'Dashboard carregado',
                'data' => date('Y-m-d H:i:s', strtotime('-1 minute')),
                'icone' => 'bi-speedometer2'
            ]
        ];
    }
    
    // Ordenar por data
    usort($atividades, function($a, $b) {
        return strtotime($b['data']) - strtotime($a['data']);
    });
    
    return array_slice($atividades, 0, 10);
}

try {
    $atividades = obterAtividadesRecentes($conn);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $atividades
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao carregar atividades: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
?>