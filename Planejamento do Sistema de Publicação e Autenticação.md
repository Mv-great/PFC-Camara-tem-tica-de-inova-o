# Planejamento do Sistema de Publicação e Autenticação

Este documento detalha o planejamento para a adição de um sistema de gerenciamento de conteúdo (CMS) com autenticação de login ao projeto "Câmara Temática de Inovação". O objetivo é permitir que usuários autorizados publiquem e gerenciem notícias, projetos e eventos de forma segura.

## 1. Design do Banco de Dados

Serão necessárias modificações e adições ao esquema do banco de dados existente para suportar a autenticação de usuários e o gerenciamento de conteúdo.

### 1.1. Tabela `usuarios`

Esta tabela armazenará as informações dos usuários que terão permissão para acessar o painel administrativo e publicar conteúdo.

| Campo        | Tipo de Dados     | Restrições           | Descrição                               |
| :----------- | :---------------- | :------------------- | :-------------------------------------- |
| `id`         | `INT`             | `PRIMARY KEY`, `AUTO_INCREMENT` | Identificador único do usuário          |
| `username`   | `VARCHAR(50)`     | `NOT NULL`, `UNIQUE` | Nome de usuário para login              |
| `password`   | `VARCHAR(255)`    | `NOT NULL`           | Senha do usuário (armazenada como hash) |
| `email`      | `VARCHAR(100)`    | `UNIQUE`             | Endereço de e-mail do usuário           |
| `role`       | `VARCHAR(20)`     | `DEFAULT 'editor'`   | Nível de permissão (ex: 'admin', 'editor') |
| `created_at` | `TIMESTAMP`       | `DEFAULT CURRENT_TIMESTAMP` | Data e hora de criação do registro      |

### 1.2. Tabela `artigos` (Modificações)

A tabela `artigos` existente será aprimorada para incluir informações sobre o autor e o status de publicação.

| Campo        | Tipo de Dados     | Restrições           | Descrição                               |
| :----------- | :---------------- | :------------------- | :-------------------------------------- |
| `id`         | `INT`             | `PRIMARY KEY`, `AUTO_INCREMENT` | Identificador único do artigo           |
| `titulo`     | `VARCHAR(255)`    | `NOT NULL`           | Título da notícia, projeto ou evento    |
| `conteudo`   | `TEXT`            | `NOT NULL`           | Conteúdo detalhado                      |
| `categoria_id` | `INT`             | `FOREIGN KEY`        | Referência à tabela `categorias`        |
| `data_evento` | `DATE`            | `NULLABLE`           | Data do evento (se for um evento)       |
| `autor_id`   | `INT`             | `FOREIGN KEY`        | ID do usuário que publicou o artigo     |
| `status`     | `VARCHAR(20)`     | `DEFAULT 'rascunho'` | Status de publicação (ex: 'publicado', 'rascunho') |
| `created_at` | `TIMESTAMP`       | `DEFAULT CURRENT_TIMESTAMP` | Data e hora de criação do artigo        |
| `updated_at` | `TIMESTAMP`       | `DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` | Última atualização do artigo            |

### 1.3. Tabela `categorias` (Existente)

Será utilizada a tabela `categorias` existente para classificar o conteúdo (Notícias, Eventos, Projetos).

## 2. Fluxo de Autenticação

O sistema de autenticação seguirá um fluxo padrão para garantir a segurança e a usabilidade.

### 2.1. Registro de Usuários (Opcional/Manual)

Inicialmente, o registro de usuários pode ser feito manualmente por um administrador existente ou através de um script de configuração inicial para criar o primeiro usuário `admin`.

### 2.2. Página de Login

*   **URL:** `/admin/login.php`
*   **Campos:** `username` e `password`.
*   **Validação:** Verificação das credenciais no banco de dados. A senha fornecida será comparada com o hash armazenado usando `password_verify()`.
*   **Sessão:** Após login bem-sucedido, uma sessão PHP será iniciada e o `user_id` e `role` serão armazenados em `$_SESSION`.
*   **Redirecionamento:** Usuários autenticados serão redirecionados para o painel administrativo (`/admin/dashboard.php`).

### 2.3. Proteção de Rotas

Todas as páginas do painel administrativo (`/admin/*`) verificarão a existência e validade da sessão do usuário. Se a sessão não for válida, o usuário será redirecionado para a página de login.

### 2.4. Logout

*   **URL:** `/admin/logout.php`
*   **Funcionalidade:** Destrói a sessão PHP e redireciona o usuário para a página de login ou para a página inicial do site.

## 3. Painel Administrativo (CMS)

Um painel administrativo será criado para gerenciar o conteúdo.

### 3.1. Dashboard (`/admin/dashboard.php`)

*   Visão geral do sistema.
*   Links para gerenciar Notícias, Eventos e Projetos.

### 3.2. Gerenciamento de Notícias (`/admin/noticias.php`)

*   Listagem de todas as notícias.
*   Opções para Adicionar, Editar e Excluir notícias.
*   Formulário para criação/edição com campos: `titulo`, `conteudo`, `status`.

### 3.3. Gerenciamento de Eventos (`/admin/eventos.php`)

*   Listagem de todos os eventos.
*   Opções para Adicionar, Editar e Excluir eventos.
*   Formulário para criação/edição com campos: `titulo`, `conteudo`, `data_evento`, `status`.

### 3.4. Gerenciamento de Projetos (`/admin/projetos.php`)

*   Listagem de todos os projetos.
*   Opções para Adicionar, Editar e Excluir projetos.
*   Formulário para criação/edição com campos: `titulo`, `conteudo`, `status`.

## 4. Tecnologias e Ferramentas

*   **Backend:** PHP (com MySQLi ou PDO para interação com o banco de dados).
*   **Frontend (Admin):** HTML, CSS (reutilizando `styles.css` ou criando um novo para o admin), JavaScript.
*   **Segurança:** `password_hash()` e `password_verify()` para senhas, sessões PHP para autenticação.

## 5. Próximos Passos

1.  Criar os scripts SQL para as novas tabelas e modificações.
2.  Implementar a página de login e o sistema de autenticação.
3.  Desenvolver o painel administrativo e as interfaces de gerenciamento de conteúdo.
4.  Integrar as novas funcionalidades com o frontend existente.
5.  Realizar testes completos.

Este planejamento servirá como guia para as próximas fases de implementação.
