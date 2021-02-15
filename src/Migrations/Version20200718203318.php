<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200718203318 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE token_number ADD activate_by_id INT DEFAULT NULL, ADD tested_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE token_number ADD CONSTRAINT FK_97E8B056AF434215 FOREIGN KEY (activate_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_97E8B056AF434215 ON token_number (activate_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE token_number DROP FOREIGN KEY FK_97E8B056AF434215');
        $this->addSql('DROP INDEX IDX_97E8B056AF434215 ON token_number');
        $this->addSql('ALTER TABLE token_number DROP activate_by_id, DROP tested_at');
    }
}
