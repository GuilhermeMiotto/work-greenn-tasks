Guia de Utilização do Projeto: TaskForGreen

1. Configuração do Backend:

O servidor backend está configurado para rodar na porta 8000. comando = php -S localhost:8000
Lembrando de dar o CD antes para a parte back-end do projeto. 
O nome do banco de dados deve ser = taskforgreenn.
O nome da tabela no banco de dados deve ser = tarefas
1. Configuração do Banco de Dados:

Copy code
CREATE TABLE `tarefas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descricao` longtext,
  `data_vencimento` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `completed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

3. Configuração do Frontend:

O servidor frontend está configurado para rodar na porta 8080.

4. Acesso à Aplicação:

Abra o navegador e acesse http://localhost:8080/ para usar a aplicação.