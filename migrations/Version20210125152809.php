<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210125152809 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, visible TINYINT(1) NOT NULL, auteur VARCHAR(255) DEFAULT NULL, timestamps DATETIME NOT NULL, picture VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article_categorie_article (article_id INT NOT NULL, categorie_article_id INT NOT NULL, INDEX IDX_94A2D4397294869C (article_id), INDEX IDX_94A2D439EC5D4C30 (categorie_article_id), PRIMARY KEY(article_id, categorie_article_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie_article (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, visible TINYINT(1) NOT NULL, timestamps DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, author_name VARCHAR(255) DEFAULT NULL, content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article_categorie_article ADD CONSTRAINT FK_94A2D4397294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_categorie_article ADD CONSTRAINT FK_94A2D439EC5D4C30 FOREIGN KEY (categorie_article_id) REFERENCES categorie_article (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article_categorie_article DROP FOREIGN KEY FK_94A2D4397294869C');
        $this->addSql('ALTER TABLE article_categorie_article DROP FOREIGN KEY FK_94A2D439EC5D4C30');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE article_categorie_article');
        $this->addSql('DROP TABLE categorie_article');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE user');
    }
}
