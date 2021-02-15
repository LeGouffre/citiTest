<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200615114803 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE old_token (id INT AUTO_INCREMENT NOT NULL, number INT NOT NULL, update_at DATETIME NOT NULL, archived_at DATETIME NOT NULL, location VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE token_number (id INT AUTO_INCREMENT NOT NULL, tested_by_id INT DEFAULT NULL, number INT NOT NULL, result VARCHAR(10) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, update_at DATETIME DEFAULT NULL, has_see TINYINT(1) NOT NULL, active TINYINT(1) NOT NULL, INDEX IDX_97E8B056BBC441CE (tested_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, location VARCHAR(255) DEFAULT NULL, user_name VARCHAR(75) NOT NULL, token_registration VARCHAR(255) DEFAULT NULL, activate TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE token_number ADD CONSTRAINT FK_97E8B056BBC441CE FOREIGN KEY (tested_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE token_number DROP FOREIGN KEY FK_97E8B056BBC441CE');
        $this->addSql('DROP TABLE old_token');
        $this->addSql('DROP TABLE token_number');
        $this->addSql('DROP TABLE user');
    }
}
