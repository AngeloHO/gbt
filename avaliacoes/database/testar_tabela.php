<?php
require_once '../../config/conexao.php';
connect_local_mysqli('gebert');

echo "<h2>Teste de Criação da Tabela FUN_AVALIACOES_FEEDBACK</h2>";

global $mysqli;

// Verificar se a tabela existe
$result = $mysqli->query("SHOW TABLES LIKE 'FUN_AVALIACOES_FEEDBACK'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✅ Tabela FUN_AVALIACOES_FEEDBACK existe!</p>";
    
    // Mostrar estrutura da tabela
    echo "<h3>Estrutura da Tabela:</h3>";
    $result = $mysqli->query("DESCRIBE FUN_AVALIACOES_FEEDBACK");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "<td>{$row['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Tabela FUN_AVALIACOES_FEEDBACK não encontrada!</p>";
    echo "<p>Execute o SQL manualmente:</p>";
    echo "<pre>" . file_get_contents('criar_tabela_avaliacoes.sql') . "</pre>";
}
?>