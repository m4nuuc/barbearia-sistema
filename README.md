# ✂️ La Famiglia - Sistema de Agendamento Online

Desafio técnico desenvolvido em **Laravel 11** e **PHP 8.3** para o processo seletivo da **Skala Code**. O sistema foi projetado para substituir o fluxo de marcações manuais via WhatsApp por uma solução digital robusta, performática e segura.

---

## 🚀 Como Rodar o Projeto do Zero

Siga as instruções abaixo para configurar o ambiente localmente no Windows (otimizado para Laravel Herd):

1. **Clonar o Repositório:**
   ```bash
   git clone https://github.com/m4nuuc?tab=repositories
   cd barbearia-sistema
   ```

2. **Instalar as Dependências do Composer:**
   ```bash
   composer install
   ```

3. **Configurar as Variáveis de Ambiente:**
   ```bash
   cp .env.example .env
   ```

4. **Gerar a Chave Única da Aplicação:**
   ```bash
   php artisan key:generate
   ```

5. **Preparar o Banco de Dados (SQLite):**
   ```bash
   touch database/database.sqlite
   ```

6. **Executar Migrations e Seeders:**
   Alimenta a tabela com os serviços e preços reais da região.
   ```bash
   php artisan migrate --seed
   ```

---

## 📦 O Que Foi Feito (Funcionalidades Entregues)

*   **Painel Administrativo (`$isAdmin` via Session):** Restrição de segurança para que apenas o barbeiro autenticado gerencie e visualize dados sensíveis de clientes.
*   **Agenda Diária Ordenada:** Listagem cronológica dos atendimentos com busca dinâmica por data (`?date=YYYY-MM-DD`).
*   **Controle de Horário Comercial Extensivo:** Validação integrada através da classe auxiliar `Availability::withinBusinessHours`, garantindo o atendimento estrito das 09h às 18h [source: 1, cite: 3].
*   **Bloqueio de Colisões Intercaladas:** Algoritmo preventivo baseado em janelas de tempo (`start_time` e `end_time`) com suporte a exclusão do ID atual (`$ignoreId`) para operações estáveis de reajuste.
*   **Recursos Opcionais Concluídos:**
    *   *Cancelamento Definitivo:* Exclusão física do registro via método HTTP `DELETE`.
    *   *Remarcação Segura:* Atualização reativa de slots de horários com checagem dinâmica de conflitos.
    *   *Métricas Financeiras do Dia:* Consolidação em tempo real do faturamento diário, volume total de cortes e cálculo do ticket médio.
    *   *Notificação via WhatsApp:* Injeção de variáveis em tempo de execução para disparo de lembretes rápidos via API externa (`wa.me`).

---

## 🧠 Decisões e Suposições Técnica

*   **Arquitetura Monolítica Eficiente:** Escolha do Laravel Blade clássico em conjunto com o Tailwind CSS (via CDN) para blindar o tempo de entrega de 48 horas contra o overhead de builds complexas (Vite/Node).
*   **Database Isolation com Transactions:** Toda alocação de horário é empacotada em uma `DB::transaction` para evitar condições de corrida (Race Conditions) caso múltiplos clientes cliquem no mesmo milissegundo.
*   **Abstração de Regras com Carbon:** Uso intensivo da biblioteca Carbon para manipulação cirúrgica de fusos horários e prevenção de agendamentos retroativos no passado.

---

## 📈 Planejado x Entregue

*   **100% do Escopo Concluído:** Todas as metas obrigatórias e os recursos opcionais (cancelamento, remarcação, lembrete e faturamento) sugeridos no enunciado foram desenvolvidos e integrados com sucesso. 

---

## 🤖 Uso de IA (Ferramentas e Prompts)

Ferramentas utilizadas: **ChatGPT** e **Claude AI**.

### Prompts Principais Utilizados (Formatados e Limpos):
1. *"Como estruturar uma validação elegante de concorrência de horários no Laravel usando Eloquent e Carbon, garantindo que o próprio ID seja ignorado em caso de update?"*
2. *"Como isolar o recurso de cadastro de serviços em uma view Blade para que apenas a sessão autenticada do administrador tenha privilégios de escrita, protegendo a experiência do cliente final?"*
3. *"Crie um layout responsivo dark/gold inspirado no design da La Mafia Barbearia utilizando utilitários puros do Tailwind CSS."*
4. *"Como implementar um efeito parallax/fade-out em uma imagem hero de fundo utilizando CSS nativo acoplado ao scroll do Blade?"*

---
