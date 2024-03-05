<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305170517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activitees (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, places INT NOT NULL, is_open TINYINT(1) NOT NULL, lieu LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activitees_user (activitees_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5F91B1FB5D8F7519 (activitees_id), INDEX IDX_5F91B1FBA76ED395 (user_id), PRIMARY KEY(activitees_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activitees_user ADD CONSTRAINT FK_5F91B1FB5D8F7519 FOREIGN KEY (activitees_id) REFERENCES activitees (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activitees_user ADD CONSTRAINT FK_5F91B1FBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activitees_user DROP FOREIGN KEY FK_5F91B1FB5D8F7519');
        $this->addSql('ALTER TABLE activitees_user DROP FOREIGN KEY FK_5F91B1FBA76ED395');
        $this->addSql('DROP TABLE activitees');
        $this->addSql('DROP TABLE activitees_user');
    }
}
