# Workflow - Gerenciador de Tarefas 🚀

Um sistema completo de gerenciamento de tarefas desenvolvido com PHP puro (Vanilla) e MySQL. Este projeto foi construído para solidificar conceitos fundamentais de desenvolvimento web, segurança, manipulação de banco de dados relacional e boas práticas de arquitetura de software.

## 🎯 Funcionalidades Principais

* **Autenticação Segura:** Sistema de login e cadastro de usuários utilizando `password_hash` e `password_verify` nativos do PHP.
* **CRUD Completo:** Criação, leitura, edição e exclusão de tarefas.
* **Soft Delete (Engenharia de Dados):** As exclusões de tarefas não apagam os registros fisicamente do banco de dados, mas os marcam com uma data de exclusão (`deletado_em`), uma prática padrão de mercado para auditoria e recuperação de dados.
* **Filtros e Busca Dinâmica:** Capacidade de buscar tarefas por título e filtrar por status (Pendente, Em Andamento, Concluído).
* **Segurança e Isolamento:**
  * Proteção de rotas (acesso permitido apenas para usuários autenticados via `session`).
  * Isolamento de dados: Um usuário só tem acesso para ver, editar e excluir as suas próprias tarefas.
  * Prevenção contra SQL Injection utilizando **PDO (PHP Data Objects)** e *Prepared Statements*.
* **UX/Feedback Visual:** Sistema de mensagens temporárias (Flash Messages) via sessão para confirmar ações do usuário (ex: "Tarefa movida para a lixeira!").

## 🛠️ Tecnologias Utilizadas

* **Backend:** PHP 8+
* **Banco de Dados:** MySQL
* **Frontend:** HTML5, CSS3 (Estilização pura, sem frameworks)
* **Conexão de Dados:** PDO (PHP Data Objects)

## ⚙️ Como executar o projeto localmente

### 1. Pré-requisitos
* PHP instalado em sua máquina.
* Servidor MySQL rodando localmente (pode ser instalado via MySQL Installer, XAMPP, Laragon, etc.).

### 2. Configuração do Banco de Dados
Abra a interface do seu banco de dados (ex: MySQL Workbench) e execute o script abaixo para criar o banco e as tabelas necessárias:

```sql
CREATE DATABASE IF NOT EXISTS workflow_db;
USE workflow_db;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tarefas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    status ENUM('pendente', 'andamento', 'concluido') DEFAULT 'pendente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deletado_em TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

### 3. Configuração da Conexão
Abra o arquivo `conexao.php` e insira as credenciais do seu banco de dados local:

```php
$host = 'localhost';
$dbname = 'workflow_db';
$user = 'root'; // Seu usuário do MySQL
$password = 'SUA_SENHA_AQUI'; // Sua senha do MySQL

### 4. Rodando o servidor
Abra o terminal na pasta raiz do projeto e inicie o servidor embutido do PHP:

```bash
php -S localhost:8000