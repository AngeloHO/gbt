-- Criação da tabela para armazenar ASO (Atestado de Saúde Ocupacional) dos funcionários
CREATE TABLE IF NOT EXISTS FUN_ASO (
    ASO_ID INT PRIMARY KEY AUTO_INCREMENT,
    ASO_FUNCIONARIO_ID INT NOT NULL,
    ASO_TIPO_EXAME VARCHAR(100) NOT NULL COMMENT 'Tipo do exame: Admissional, Periódico, Retorno ao trabalho, Mudança de função, Demissional',
    ASO_DATA_EXAME DATE NOT NULL COMMENT 'Data em que o exame foi realizado',
    ASO_DATA_VALIDADE DATE NOT NULL COMMENT 'Data de validade do ASO',
    ASO_RESULTADO ENUM('APTO', 'INAPTO', 'APTO_COM_RESTRICOES') NOT NULL DEFAULT 'APTO',
    ASO_MEDICO_RESPONSAVEL VARCHAR(150) NOT NULL COMMENT 'Nome do médico responsável pelo exame',
    ASO_CRM_MEDICO VARCHAR(20) COMMENT 'CRM do médico responsável',
    ASO_CLINICA_EXAME VARCHAR(200) COMMENT 'Nome da clínica onde foi realizado o exame',
    ASO_OBSERVACOES TEXT COMMENT 'Observações sobre o exame ou restrições',
    ASO_RESTRICOES TEXT COMMENT 'Descrição detalhada das restrições se houver',
    ASO_EXAMES_REALIZADOS TEXT COMMENT 'Lista dos exames complementares realizados',
    ASO_NUMERO_DOCUMENTO VARCHAR(50) COMMENT 'Número do documento ASO',
    ASO_ARQUIVO_PATH VARCHAR(500) COMMENT 'Caminho para o arquivo PDF/imagem do ASO',
    ASO_STATUS ENUM('ATIVO', 'VENCIDO', 'CANCELADO') NOT NULL DEFAULT 'ATIVO',
    
    -- Campos de Auditoria
    ASO_CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ASO_UPDATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ASO_CREATED_BY INT,
    ASO_UPDATED_BY INT,
    
    -- Chaves estrangeiras
    FOREIGN KEY (ASO_FUNCIONARIO_ID) REFERENCES FUN_FUNCIONARIO(FUN_ID) ON DELETE CASCADE,
    FOREIGN KEY (ASO_CREATED_BY) REFERENCES usu_usuario(USU_ID),
    FOREIGN KEY (ASO_UPDATED_BY) REFERENCES usu_usuario(USU_ID)
);

-- Criar índices para melhor performance
CREATE INDEX idx_funcionario_aso ON FUN_ASO(ASO_FUNCIONARIO_ID);
CREATE INDEX idx_data_validade_aso ON FUN_ASO(ASO_DATA_VALIDADE);
CREATE INDEX idx_status_aso ON FUN_ASO(ASO_STATUS);
CREATE INDEX idx_tipo_exame ON FUN_ASO(ASO_TIPO_EXAME);
CREATE INDEX idx_resultado_aso ON FUN_ASO(ASO_RESULTADO);

-- Comentários da tabela
ALTER TABLE FUN_ASO COMMENT = 'Tabela para armazenar ASO (Atestado de Saúde Ocupacional) dos funcionários';

-- Inserir tipos de exame padrão (opcional)
INSERT IGNORE INTO tipo_exame_aso (nome) VALUES 
('Admissional'),
('Periódico'),
('Retorno ao trabalho'),
('Mudança de função'),
('Demissional');

-- View para facilitar consultas com dados do funcionário
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
    -- Status de vencimento
    CASE 
        WHEN a.ASO_DATA_VALIDADE < CURDATE() THEN 'VENCIDO'
        WHEN DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) <= 30 THEN 'VENCE_30_DIAS'
        WHEN DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) <= 60 THEN 'VENCE_60_DIAS'
        ELSE 'VIGENTE'
    END as STATUS_VENCIMENTO
FROM FUN_ASO a
INNER JOIN FUN_FUNCIONARIO f ON a.ASO_FUNCIONARIO_ID = f.FUN_ID
WHERE f.FUN_STATUS = 'ativo';