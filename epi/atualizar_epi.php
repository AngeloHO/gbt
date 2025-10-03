    <?php
require_once '../config/conexao.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado']);
    exit();
}

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
    exit();
}

// Conecta ao banco de dados
$conn = connect_local_mysqli('gebert');

try {
    // Validar campos obrigatórios
    $campos_obrigatorios = ['id', 'nome', 'categoria_id', 'fabricante', 'status'];
    foreach ($campos_obrigatorios as $campo) {
        if (empty($_POST[$campo])) {
            throw new Exception("Campo '$campo' é obrigatório");
        }
    }
    
    // Escapar dados de entrada
    $id = intval($_POST['id']);
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $categoria_id = intval($_POST['categoria_id']);
    $fabricante = mysqli_real_escape_string($conn, $_POST['fabricante']);
    $tamanho = mysqli_real_escape_string($conn, $_POST['tamanho'] ?? '');
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $descricao = mysqli_real_escape_string($conn, $_POST['descricao'] ?? '');
    
    // Verificar se o EPI existe
    $sql_check = "SELECT epi_id FROM EPI_EQUIPAMENTOS WHERE epi_id = $id";
    $result_check = mysqli_query($conn, $sql_check);
    
    if (mysqli_num_rows($result_check) === 0) {
        throw new Exception('EPI não encontrado');
    }
    
    // Atualizar EPI
    $sql = "UPDATE EPI_EQUIPAMENTOS SET 
                epi_nome = '$nome',
                epi_categoria = $categoria_id,
                epi_fabricante = '$fabricante',
                epi_tamanho = '$tamanho',
                epi_status = '$status',
                epi_descricao = '$descricao'
            WHERE epi_id = $id";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception('Erro ao atualizar EPI: ' . mysqli_error($conn));
    }
    
    $response = [
        'status' => 'success',
        'message' => 'EPI atualizado com sucesso!'
    ];
    
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
} finally {
    mysqli_close($conn);
}

header('Content-Type: application/json');
echo json_encode($response);
?>