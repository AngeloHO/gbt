-- Script para adicionar colunas de auditoria na tabela FUN_FUNCIONARIO
-- Execute este script no phpMyAdmin ou MySQL

-- Usar o banco de dados gebert
USE gebert;

-- Adicionar coluna para usuário que fez a última atualização
ALTER TABLE FUN_FUNCIONARIO 
ADD COLUMN fun_usuario_atualizacao INT DEFAULT NULL COMMENT 'ID do usuário que fez a última atualização';

-- Adicionar coluna para data da última atualização
ALTER TABLE FUN_FUNCIONARIO 
ADD COLUMN fun_data_atualizacao DATETIME DEFAULT NULL COMMENT 'Data da última atualização';

-- Verificar se as colunas foram adicionadas corretamente
DESCRIBE FUN_FUNCIONARIO;

-- Exemplo: Atualizar um funcionário específico com dados de auditoria
-- (substitua o ID 2 pelo ID do funcionário que você quer testar)
UPDATE FUN_FUNCIONARIO 
SET fun_usuario_atualizacao = 2, 
    fun_data_atualizacao = '2025-10-01 10:30:00' 
WHERE fun_id = 2;