# Urna Simulator

Aplicação de simulação de votação construída com Symfony 8.1, PHP 8.4+, Twig e PostgreSQL 16. Inclui cadastro de eleitores, votação, gerenciamento administrativo e apuração.

> Este projeto é uma simulação. As medidas descritas aqui não equivalem a certificação nem tornam a aplicação apropriada para uma eleição oficial.

## Requisitos

- PHP 8.4 ou superior, com as extensões `ctype`, `iconv`, `intl`, `pdo_pgsql` e `zip` habilitadas;
- Composer;
- PostgreSQL 16 local ou acessível pela aplicação;
- Symfony CLI, recomendado para iniciar o servidor de desenvolvimento.

## Configuração local

Abra um terminal nesta pasta (`Urna-Simulator/project`) e instale as dependências:

```powershell
composer install
```

O Composer executa os scripts do Symfony, incluindo a instalação dos assets do ImportMap. Se o asset do Stimulus ou outro pacote JavaScript estiver ausente, execute:

```powershell
php bin/console importmap:install
```

Configure os valores locais em `.env.dev.local`, que é ignorado pelo Git e prevalece sobre `.env.dev`. Não coloque credenciais em `.env` nem as envie ao repositório.

```dotenv
APP_SECRET=<segredo aleatório para o Symfony>
APP_VOTE_PEPPER=<segredo aleatório próprio para os comprovantes>
DATABASE_URL="postgresql://usuario:senha@127.0.0.1:5432/urna?serverVersion=16&charset=utf8"
```

Gere valores aleatórios diferentes para `APP_SECRET` e `APP_VOTE_PEPPER`. No PowerShell, este comando gera 32 bytes em hexadecimal; execute-o separadamente para cada segredo:

```powershell
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

`APP_VOTE_PEPPER` precisa ter pelo menos 32 caracteres e deve permanecer secreto e estável entre instalações que precisam validar os mesmos comprovantes. Mantenha uma cópia segura. Se ele for perdido ou trocado sem um plano de rotação, os comprovantes existentes não poderão ser verificados.

## Banco de dados e migrações

Confira se a URL do banco aponta para o ambiente correto antes de executar migrações. Faça um backup antes de migrar um banco que já contenha votos.

```powershell
php bin/console doctrine:migrations:status
php bin/console doctrine:migrations:migrate --no-interaction
```

A migração `Version20261026190000` separa o registro de participação das cédulas, preserva os votos existentes, cria uma restrição para uma participação por eleitor e converte os comprovantes existentes para hashes com sal e pepper. Ela remove o vínculo direto entre eleitor e voto e é intencionalmente irreversível; o backup deve ser feito antes da aplicação. O `APP_VOTE_PEPPER` precisa estar configurado antes de migrar.

**Estado atual:** a migração está no código, mas ainda não foi aplicada ao banco Neon configurado anteriormente no ambiente de desenvolvimento. Até aplicá-la ao banco correto, o código novo de votação espera o esquema atualizado e a gravação de votos não funcionará no esquema antigo.

## Iniciar a aplicação

Com Symfony CLI:

```powershell
symfony server:start
```

Abra <http://127.0.0.1:8000>. Para iniciar em segundo plano, use `symfony server:start -d`; para pará-lo, use `symfony server:stop`.

Sem Symfony CLI, é possível usar o servidor integrado do PHP:

```powershell
php -S 127.0.0.1:8000 -t public public/index.php
```

O cadastro de novos usuários fica em `/register`. O cadastro público concede perfil de votante; não existe criação pública de administradores. O primeiro administrador deve ser provisionado por um procedimento controlado.

## Proteções implementadas

- **Autenticação e senhas:** o Symfony usa o hasher configurado para o ambiente; cadastro e atualização administrativa de senha exigem pelo menos 12 caracteres. O login limita tentativas malsucedidas a 5 por período de 15 minutos.
- **Autorização:** regras de acesso restringem as áreas administrativas a `ROLE_ADMIN` e a votação a `ROLE_VOTANTE`; o endpoint de voto também exige eleitor autenticado.
- **CSRF:** proteção habilitada no login e nos formulários Symfony; os endpoints de votação e encerramento da eleição validam tokens CSRF. Os formulários de exclusão também usam tokens.
- **Sessões e cabeçalhos:** cookies de sessão `HttpOnly`, `SameSite=Lax` e `Secure` automático em HTTPS. Respostas incluem proteção contra MIME sniffing, enquadramento indevido, política de referenciador e permissões de recursos; HSTS é enviado em produção sobre HTTPS.
- **Validação de dados:** validação de e-mail, campos obrigatórios, limites de tamanho, idade, título de eleitor único e formato do voto. Fotos aceitam somente JPEG, PNG ou WebP, com limite de 2 MB.
- **Comprovantes:** cada voto novo recebe uma chave aleatória. O banco guarda HMAC-SHA-256 com sal por voto e pepper privado; a chave em texto puro é exibida ao eleitor uma vez e não é persistida.
- **Separação entre participação e cédula:** implementada no modelo e na migração pendente. Após migrar, a tabela de participação informa se o eleitor votou, sem guardar uma referência para a cédula escolhida.
- **Formulários:** tema Bootstrap 5 do Symfony, apresentação de erros de validação e campos com estados de foco e erro.

## Cuidados de operação

- Nunca publique `.env.local`, `.env.dev.local`, senhas, `APP_SECRET`, `APP_VOTE_PEPPER` ou comprovantes em logs ou no Git.
- Confirme o destino em `DATABASE_URL` e faça backup antes de qualquer migração; a migração que remove o vínculo entre eleitor e cédula não pode ser revertida.
- Restrinja o acesso ao PostgreSQL e às cópias de segurança. Use TLS ao conectar a bancos remotos.
- A chave do comprovante é mostrada uma única vez. Oriente o eleitor a guardá-la; o sistema não consegue recuperar a chave original a partir do hash.

