<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306135307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adhesion ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE adhesion ADD CONSTRAINT FK_C50CA65AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C50CA65AA76ED395 ON adhesion (user_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649EE93F420');
        $this->addSql('DROP INDEX UNIQ_8D93D649EE93F420 ON user');
        $this->addSql('ALTER TABLE user DROP id_adhesion_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adhesion DROP FOREIGN KEY FK_C50CA65AA76ED395');
        $this->addSql('DROP INDEX UNIQ_C50CA65AA76ED395 ON adhesion');
        $this->addSql('ALTER TABLE adhesion DROP user_id');
        $this->addSql('ALTER TABLE user ADD id_adhesion_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649EE93F420 FOREIGN KEY (id_adhesion_id) REFERENCES adhesion (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649EE93F420 ON user (id_adhesion_id)');
    }
}
