<?php
require_once '../config/conexao.php';

echo "<h2>Teste de Conexão</h2>";

try {
    $mysqli = connect_local_mysqli('gebert');
    
    if ($mysqli) {
        echo "<p style='color: green;'>✅ Conexão estabelecida com sucesso!</p>";
        echo "<p>Servidor: " . $mysqli->get_server_info() . "</p>";
        echo "<p>Protocolo: " . $mysqli->protocol_version . "</p>";
        
        // Testar uma consulta simples
        $result = $mysqli->query("SELECT 1 as teste");
        if ($result) {
            echo "<p style='color: green;'>✅ Consulta de teste executada com sucesso!</p>";
        } else {
            echo "<p style='color: red;'>❌ Erro na consulta: " . $mysqli->error . "</p>";
        }
        
        // Verificar se as tabelas existem
        $result = $mysqli->query("SHOW TABLES LIKE 'FUN_FUNCIONARIO'");
        if ($result && $result->num_rows > 0) {
            echo "<p style='color: green;'>✅ Tabela FUN_FUNCIONARIO encontrada!</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Tabela FUN_FUNCIONARIO não encontrada</p>";
        }
        
        $result = $mysqli->query("SHOW TABLES LIKE 'FUN_AVALIACOES_FEEDBACK'");
        if ($result && $result->num_rows > 0) {
            echo "<p style='color: green;'>✅ Tabela FUN_AVALIACOES_FEEDBACK encontrada!</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Tabela FUN_AVALIACOES_FEEDBACK não encontrada</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Falha na conexão!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}
?>