<?php
// Testar a API de estatísticas
session_start();

// Simular uma sessão válida para teste
$_SESSION['user_id'] = 1;
$_SESSION['user'] = ['nome' => 'Teste'];

echo "<h2>Teste da API de Estatísticas</h2>";

// Testar conexão direta
echo "<h3>1. Testando conexão direta:</h3>";
try {
    require_once 'config/conexao.php';
    $conn = connect_local_mysqli('gebert');
    if ($conn) {
        echo "✅ Conexão estabelecida com sucesso<br>";
        
        // Testar se as tabelas existem
        $tabelas = ['fun_funcionario', 'fun_aso', 'epi_entregas', 'epi_equipamentos', 'des_desligamento'];
        foreach ($tabelas as $tabela) {
            $query = "SHOW TABLES LIKE '$tabela'";
            $result = mysqli_query($conn, $query);
            if ($result && mysqli_num_rows($result) > 0) {
                echo "✅ Tabela '$tabela' existe<br>";
            } else {
                echo "❌ Tabela '$tabela' NÃO existe<br>";
            }
        }
        
        mysqli_close($conn);
    } else {
        echo "❌ Falha na conexão<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

echo "<h3>2. Testando API via HTTP:</h3>";

// Fazer requisição para a API
$url = 'http://localhost/gbt/api/estatisticas.php';
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Cookie: " . $_SERVER['HTTP_COOKIE'] ?? ''
    ]
]);

$response = file_get_contents($url, false, $context);
if ($response !== false) {
    echo "✅ Resposta da API recebida:<br>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    $data = json_decode($response, true);
    if ($data) {
        if ($data['success']) {
            echo "✅ API retornou sucesso!<br>";
            echo "Dados: <pre>" . print_r($data['data'], true) . "</pre>";
        } else {
            echo "❌ API retornou erro: " . $data['error'] . "<br>";
        }
    } else {
        echo "❌ Erro ao decodificar JSON<br>";
    }
} else {
    echo "❌ Falha ao chamar a API<br>";
    echo "Cabeçalhos de resposta: <pre>" . print_r($http_response_header ?? ['Nenhum'], true) . "</pre>";
}
?>