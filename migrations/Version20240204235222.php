<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240204235222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE exchange_rate_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE exchange_rate_history_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE exchange_rate (id INT NOT NULL, status VARCHAR(10) NOT NULL, buy INT NOT NULL, sale INT NOT NULL, currency VARCHAR(5) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE exchange_rate_history (id INT NOT NULL, exchange_rate_id INT NOT NULL, bank VARCHAR(15) NOT NULL, buy_threshold INT NOT NULL, sale_threshold INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_51C18A99FB53491E ON exchange_rate_history (exchange_rate_id)');
        $this->addSql('ALTER TABLE exchange_rate_history ADD CONSTRAINT FK_51C18A99FB53491E FOREIGN KEY (exchange_rate_id) REFERENCES exchange_rate (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE exchange_rate_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE exchange_rate_history_id_seq CASCADE');
        $this->addSql('ALTER TABLE exchange_rate_history DROP CONSTRAINT FK_51C18A99FB53491E');
        $this->addSql('DROP TABLE exchange_rate');
        $this->addSql('DROP TABLE exchange_rate_history');
    }
}
