#!/bin/sh
set -e

# Esperar o banco de dados estar pronto
if [ "$1" = 'frankenphp' ]; then
    # Executa as migrações automaticamente
    php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
fi

exec docker-php-entrypoint "$@"
