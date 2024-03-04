<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240304141855 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD316812469DE2');
        $this->addSql('DROP INDEX IDX_BFDD316812469DE2 ON articles');
        $this->addSql('ALTER TABLE articles ADD id_categorie_id INT NOT NULL, ADD auteur_id INT NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD edited_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD is_publish TINYINT(1) NOT NULL, DROP category_id, CHANGE title title LONGTEXT NOT NULL, CHANGE contenu contenu LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD31689F34925F FOREIGN KEY (id_categorie_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD316860BB6FE6 FOREIGN KEY (auteur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BFDD31689F34925F ON articles (id_categorie_id)');
        $this->addSql('CREATE INDEX IDX_BFDD316860BB6FE6 ON articles (auteur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD31689F34925F');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD316860BB6FE6');
        $this->addSql('DROP INDEX IDX_BFDD31689F34925F ON articles');
        $this->addSql('DROP INDEX IDX_BFDD316860BB6FE6 ON articles');
        $this->addSql('ALTER TABLE articles ADD category_id INT DEFAULT NULL, DROP id_categorie_id, DROP auteur_id, DROP created_at, DROP edited_at, DROP is_publish, CHANGE title title VARCHAR(255) NOT NULL, CHANGE contenu contenu JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD316812469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX IDX_BFDD316812469DE2 ON articles (category_id)');
    }
}
