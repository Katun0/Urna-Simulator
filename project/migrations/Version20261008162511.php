<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261008162511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario ADD roles JSON NOT NULL');
        $this->addSql('ALTER TABLE usuario ADD password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE usuario ALTER titulo_eleitor TYPE VARCHAR(20)');
        $this->addSql('ALTER TABLE usuario ALTER documento DROP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_TITULO_ELEITOR ON usuario (titulo_eleitor)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_TITULO_ELEITOR');
        $this->addSql('ALTER TABLE usuario DROP roles');
        $this->addSql('ALTER TABLE usuario DROP password');
        $this->addSql('ALTER TABLE usuario ALTER titulo_eleitor TYPE TEXT');
        $this->addSql('ALTER TABLE usuario ALTER documento SET NOT NULL');
    }
}
