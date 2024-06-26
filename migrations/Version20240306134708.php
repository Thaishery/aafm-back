<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306134708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adhesion (id INT AUTO_INCREMENT NOT NULL, date DATETIME DEFAULT NULL, statut VARCHAR(50) NOT NULL, is_paid TINYINT(1) NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD id_adhesion_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649EE93F420 FOREIGN KEY (id_adhesion_id) REFERENCES adhesion (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649EE93F420 ON user (id_adhesion_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649EE93F420');
        $this->addSql('DROP TABLE adhesion');
        $this->addSql('DROP INDEX UNIQ_8D93D649EE93F420 ON user');
        $this->addSql('ALTER TABLE user DROP id_adhesion_id');
    }
}
