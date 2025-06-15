# 🌱 SmartGrow – API Routes Documentation

## 🔐 Autenticação de Usuário

- **POST** `/auth/register`  
Cadastrar novo usuário.

- **POST** `/auth/login`  
Realizar login e receber token de autenticação.

- **POST** `/auth/logout`  
Encerrar sessão (opcional, se for usado controle de sessão/token).

---

## 👤 Usuário

- **GET** `/usuarios/:id`  
Obter dados do usuário (perfil).

- **PUT** `/usuarios/:id`  
Atualizar dados do usuário.

- **DELETE** `/usuarios/:id`  
Excluir conta do usuário.

---

## 🌿 Plantas (Base Geral)

- **GET** `/plantas`  
Listar todas as plantas cadastradas na base.

- **GET** `/plantas/:id`  
Obter informações detalhadas de uma planta específica.

- **POST** `/plantas`
Cadastrar planta na base (Admin).

- **PUT** `/plantas/:id`  
Editar planta na base (Admin).

- **DELETE** `/plantas/:id`
Remover planta da base (Admin).

---

## 🪴 Plantas dos Usuários

- **POST** `/minhas-plantas`  
Adicionar uma planta à conta do usuário (informando ID da planta base, apelido e localização).

- **GET** `/minhas-plantas`  
Listar todas as plantas cadastradas pelo usuário.

- **GET** `/minhas-plantas/:id`  
Obter informações detalhadas de uma planta específica do usuário.

- **PUT** `/minhas-plantas/:id`  
Editar apelido ou localização da planta do usuário.

- **DELETE** `/minhas-plantas/:id`  
Remover uma planta da conta do usuário.

---

## 🌡️ Condições Atuais da Planta

- **POST** `/minhas-plantas/:id/condicoes`  
Registrar leitura de umidade atual da planta.

- **GET** `/minhas-plantas/:id/condicoes`  
Listar histórico de leituras de umidade da planta.

---

## 🔍 Análises da Planta

- **POST** `/minhas-plantas/:id/analises`  
Gerar uma nova análise da planta (saúde e umidade).

- **GET** `/minhas-plantas/:id/analises`  
Listar análises feitas para a planta.

- **GET** `/analises/:id`  
Obter detalhes de uma análise específica.

- **DELETE** `/analises/:id`  
Deletar uma análise (opcional).

---

## 📑 Recomendações

- **GET** `/recomendacoes`  
Listar todas as recomendações disponíveis no sistema.

- **GET** `/analises/:id/recomendacoes`  
Obter recomendações geradas a partir de uma análise específica.

---

## 📄 Resumo das Rotas Principais

| Método | Endpoint                                         | Descrição                                         |
|--------|--------------------------------------------------|---------------------------------------------------|
| POST   | /auth/register                                   | Cadastrar usuário                                 |
| POST   | /auth/login                                      | Login                                             |
| GET    | /usuarios/:id                                    | Obter dados do usuário                            |
| PUT    | /usuarios/:id                                    | Atualizar dados do usuário                        |
| DELETE | /usuarios/:id                                    | Deletar conta do usuário                          |
| GET    | /plantas                                         | Listar plantas base                               |
| GET    | /plantas/:id                                     | Detalhes da planta base                           |
| POST   | /plantas                                         | Cadastrar planta na base (Admin)                  |
| PUT    | /plantas/:id                                     | Editar planta na base (Admin)                     |
| DELETE | /plantas/:id                                     | Remover planta da base (Admin)                    |
| POST   | /minhas-plantas                                  | Adicionar planta à conta do usuário               |
| GET    | /minhas-plantas                                  | Listar plantas do usuário                         |
| GET    | /minhas-plantas/:id                              | Detalhes da planta do usuário                     |
| PUT    | /minhas-plantas/:id                              | Editar planta do usuário                          |
| DELETE | /minhas-plantas/:id                              | Remover planta do usuário                         |
| POST   | /minhas-plantas/:id/condicoes                    | Registrar leitura de umidade                      |
| GET    | /minhas-plantas/:id/condicoes                    | Histórico de condições da planta                  |
| POST   | /minhas-plantas/:id/analises                     | Gerar nova análise                                |
| GET    | /minhas-plantas/:id/analises                     | Listar análises da planta                         |
| GET    | /analises/:id                                    | Detalhes da análise                               |
| DELETE | /analises/:id                                    | Deletar análise (opcional)                        |
| GET    | /recomendacoes                                   | Listar recomendações                              |
| GET    | /analises/:id/recomendacoes                      | Recomendações geradas a partir da análise         |

---

## ✅ Observações

- As rotas relacionadas a **"Plantas"** se referem ao banco geral de espécies, não às plantas cadastradas pelos usuários.
- As rotas de **"Minhas Plantas"** referem-se às instâncias das plantas adicionadas por cada usuário, com apelidos, localização e acompanhamento individual.
- As análises podem ser feitas via IA, input manual ou sensores conectados.
- As recomendações são baseadas nos resultados da análise de cada planta.

---

