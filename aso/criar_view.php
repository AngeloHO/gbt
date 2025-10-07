<?php
require_once '../config/conexao.php';
session_start();

// Conecta ao banco de dados
$conn = connect_local_mysqli('gebert');

echo "<h2>Criação da View VW_ASO_FUNCIONARIO</h2>";

// SQL para criar a view
$sql_create_view = "
CREATE OR REPLACE VIEW VW_ASO_FUNCIONARIO AS
SELECT 
    a.ASO_ID,
    a.ASO_FUNCIONARIO_ID,
    f.FUN_NOME_COMPLETO as FUNCIONARIO_NOME,
    f.FUN_CPF as FUNCIONARIO_CPF,
    f.FUN_FUNCAO as FUNCIONARIO_FUNCAO,
    a.ASO_TIPO_EXAME,
    a.ASO_DATA_EXAME,
    a.ASO_DATA_VALIDADE,
    a.ASO_RESULTADO,
    a.ASO_MEDICO_RESPONSAVEL,
    a.ASO_CRM_MEDICO,
    a.ASO_CLINICA_EXAME,
    a.ASO_OBSERVACOES,
    a.ASO_RESTRICOES,
    a.ASO_EXAMES_REALIZADOS,
    a.ASO_NUMERO_DOCUMENTO,
    a.ASO_ARQUIVO_PATH,
    a.ASO_STATUS,
    a.ASO_CREATED_AT,
    a.ASO_UPDATED_AT,
    -- Calcular dias para vencimento
    DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) as DIAS_PARA_VENCIMENTO,
    -- Status de vencimento corrigido
    CASE 
        WHEN a.ASO_DATA_VALIDADE < CURDATE() THEN 'VENCIDO'
        WHEN DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) <= 30 THEN 'VENCE_30_DIAS'
        WHEN DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) <= 60 THEN 'VENCE_60_DIAS'
        ELSE 'VIGENTE'
    END as STATUS_VENCIMENTO
FROM FUN_ASO a
INNER JOIN FUN_FUNCIONARIO f ON a.ASO_FUNCIONARIO_ID = f.FUN_ID
WHERE f.FUN_STATUS = 'ativo' AND a.ASO_STATUS = 'ATIVO'";

echo "<h3>Executando criação da view...</h3>";

if (mysqli_query($conn, $sql_create_view)) {
    echo "<p style='color: green;'>✓ View VW_ASO_FUNCIONARIO criada com sucesso!</p>";
    
    // Testar a view
    echo "<h3>Testando a view:</h3>";
    $sql_test = "SELECT COUNT(*) as total FROM VW_ASO_FUNCIONARIO";
    $result_test = mysqli_query($conn, $sql_test);
    
    if ($result_test) {
        $total = mysqli_fetch_assoc($result_test)['total'];
        echo "<p>Total de registros na view: <strong>$total</strong></p>";
        
        if ($total > 0) {
            echo "<p style='color: green;'>✓ View funcionando corretamente!</p>";
            
            // Mostrar alguns dados
            $sql_sample = "SELECT FUNCIONARIO_NOME, ASO_TIPO_EXAME, ASO_DATA_VALIDADE, STATUS_VENCIMENTO FROM VW_ASO_FUNCIONARIO LIMIT 5";
            $result_sample = mysqli_query($conn, $sql_sample);
            
            echo "<h4>Dados de exemplo:</h4>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>Funcionário</th><th>Tipo Exame</th><th>Validade</th><th>Status</th></tr>";
            
            while ($row = mysqli_fetch_assoc($result_sample)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['FUNCIONARIO_NOME']) . "</td>";
                echo "<td>" . htmlspecialchars($row['ASO_TIPO_EXAME']) . "</td>";
                echo "<td>" . htmlspecialchars($row['ASO_DATA_VALIDADE']) . "</td>";
                echo "<td>" . htmlspecialchars($row['STATUS_VENCIMENTO']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>⚠ View criada mas não há dados. Verifique se existem ASO cadastrados.</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Erro ao testar view: " . mysqli_error($conn) . "</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Erro ao criar view: " . mysqli_error($conn) . "</p>";
    echo "<p>Detalhes do erro: " . mysqli_error($conn) . "</p>";
}

// Verificar se as tabelas base existem
echo "<h3>Verificando tabelas base:</h3>";

$tabelas = ['FUN_ASO', 'FUN_FUNCIONARIO'];
foreach ($tabelas as $tabela) {
    $sql_check = "SHOW TABLES LIKE '$tabela'";
    $result_check = mysqli_query($conn, $sql_check);
    
    if (mysqli_num_rows($result_check) > 0) {
        echo "<p style='color: green;'>✓ Tabela $tabela existe</p>";
        
        // Contar registros
        $sql_count = "SELECT COUNT(*) as total FROM $tabela";
        $result_count = mysqli_query($conn, $sql_count);
        $total = mysqli_fetch_assoc($result_count)['total'];
        echo "<p>&nbsp;&nbsp;&nbsp;Total de registros: $total</p>";
    } else {
        echo "<p style='color: red;'>✗ Tabela $tabela NÃO existe</p>";
    }
}

echo "<hr>";
echo "<p><a href='aso.php'>← Voltar para o sistema ASO</a></p>";
echo "<p><a href='debug_listagem.php'>Executar debug da listagem</a></p>";

mysqli_close($conn);
?>