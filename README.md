# La Famiglia - Sistema de Agendamento Online
---

## Como Rodar o Projeto do Zero

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

## O Que Foi Feito (Funcionalidades Entregues)

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

## Planejado x Entregue

*   **Visual e Interface**
   • Planejado: Uma interface simples, estritamente funcional apenas para cumprir os requisitos básicos do teste.
   • Entregue: Design moderno escuro com detalhes em dourado inspirado na La Mafia (Novo Hamburgo), utilizando Tailwind CSS (CDN), com rodapé institucional rico em informações (acessibilidade, espaço kids, pet friendly e       wi-fi).
*   **Alteração de Horários**
   • Planejado: Um sistema complexo de remarcação direta de horários na grade.
   • Entregue: Fluxo otimizado e seguro de Cancelamento Imediato (via método HTTP DELETE), liberando o slot no banco SQLite instantaneamente para evitar erros de concorrência e garantir a integridade dos dados.
*   **Lembretes e Comunicação**
   • Planejado: Envio de notificações totalmente automatizadas em background.
   • Entregue: Solução inteligente com um botão de Lembrete via WhatsApp (wa.me), que injeta dinamicamente as variáveis do Laravel Blade para que o barbeiro envie a mensagem personalizada com apenas um clique.
*   **Gestão e Relatórios**
   • Planejado: Apenas uma listagem simples dos agendamentos do dia em ordem cronológica.
   • Entregue: Um Painel Administrativo Executivo completo que utiliza o Eloquent ORM para calcular e exibir em tempo real o faturamento total do dia, o volume de cortes e o ticket médio dos clientes.

---

## Uso de IA (Ferramentas e Prompts)

Ferramentas utilizadas: **ChatGPT** e **Claude AI**.

### Prompts Principais Utilizados:
1. **Prompt de Segurança e Regras de Negócio (Aba de Serviços):** *"Estou desenvolvendo o back-end de um sistema de agendamento em Laravel 11. No momento, o formulário de cadastro de novos serviços está visível na mesma view principal que o cliente usa para agendar horários. Percebi um problema de segurança: se eu compartilhar o link com outas pessoas para coletar feedbacks, qualquer um pode cadastrar um serviço avulso com dados aleatórios, poluindo o banco. Como posso utilizar uma checagem simples de sessão nativa (session('is_admin')) no meu AppointmentController e condicionar com diretivas @if no Blade para que a seção de criação de serviços fique trancada e acessível estritamente para o host/dono do estabelecimento, mantendo o fluxo limpo e protegido para o cliente final?*
2. **Prompt de Identidade Visual e Arquitetura Front-End (Estética La Mafia):** *"Preciso reconstruir a interface do arquivo welcome.blade.php de um projeto de barbearia para o ecossistema do Windows usando Laravel Herd. O visual padrão atual está muito simples. Quero usar como referência estética o site oficial da unidade de Novo Hamburgo da La Mafia Barbearia. Como posso estruturar um layout responsivo, limpo e imponente utilizando Tailwind CSS puro via CDN, aplicando uma paleta de cores premium com fundo escuro (grafite/preto), tipografia contrastante em cinza claro e destaques pontuais em dourado metálico (#d4af37)? O design precisa organizar de forma escaneável uma tabela lateral com os serviços e preços reais da região (Corte R$ 53, Barba R$ 40, Corte+Barba R$ 83), um formulário centralizado de marcação de horários e uma barra de navegação superior fixa (sticky navbar).*
3. **Prompt de Efeito Visual (Fade-Out de Imagem com Scroll):** *"Quero adicionar um efeito moderno de transição visual na página inicial da minha barbearia via Blade. Gostaria de incluir uma imagem de alta resolução do estabelecimento ocupando o fundo da seção principal (Hero). Preciso de uma lógica em CSS nativo ou Tailwind que faça com que essa imagem de fundo esmaeça progressivamente (efeito de fade-out/parallax) à medida que o cliente rola a tela para baixo com o scroll do mouse, desaparecendo suavemente até dar lugar à cor cinza sólida padrão do restante do site. Como posso codificar isso garantindo um efeito fluido no navegador sem quebrar o posicionamento do formulário de agendamento que fica sobreposto à imagem?*
4. **Prompt do Algoritmo Anti-Colisão e Atualizações Seguro (Carbon + Eloquent):** *"Estou escrevendo o método update no meu AppointmentController para permitir a remarcação de horários no Laravel 11. Minha regra de negócio exige que a barbearia atenda estritamente das 09h às 18h e proíbe colisões de horários entre clientes diferentes, calculando dinamicamente o tempo de duração de cada serviço usando Carbon. Como posso estruturar uma query eficiente com Eloquent para verificar a disponibilidade de slots de tempo de forma que, no momento em que o usuário altere apenas a data ou a hora, o sistema valide o conflito contra todos os registros do SQLite, mas ignore o ID do próprio agendamento atual para evitar falsos positivos de colisão com ele mesmo?*

---
