<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250710132535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, intitule VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE challenge (id INT AUTO_INCREMENT NOT NULL, educational_content_id INT DEFAULT NULL, mission_id INT DEFAULT NULL, pere_id INT DEFAULT NULL, cours_id INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, visibility VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D7098951248D84AC (educational_content_id), UNIQUE INDEX UNIQ_D7098951BE6CAE90 (mission_id), INDEX IDX_D70989513FD73900 (pere_id), INDEX IDX_D70989517ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coach (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_FDCA8C9C12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kids (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kids_category (kids_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_6043E6C8B71E5B2E (kids_id), INDEX IDX_6043E6C812469DE2 (category_id), PRIMARY KEY(kids_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publication (id INT AUTO_INCREMENT NOT NULL, auteur_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content VARCHAR(255) DEFAULT NULL, date_publication DATETIME NOT NULL, visibility VARCHAR(255) NOT NULL, nb_vues INT DEFAULT NULL, INDEX IDX_AF3C677960BB6FE6 (auteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D7098951248D84AC FOREIGN KEY (educational_content_id) REFERENCES publication (id)');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D7098951BE6CAE90 FOREIGN KEY (mission_id) REFERENCES publication (id)');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D70989513FD73900 FOREIGN KEY (pere_id) REFERENCES challenge (id)');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D70989517ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE coach ADD CONSTRAINT FK_3F596DCCBF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE kids ADD CONSTRAINT FK_42F8D194BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE kids_category ADD CONSTRAINT FK_6043E6C8B71E5B2E FOREIGN KEY (kids_id) REFERENCES kids (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE kids_category ADD CONSTRAINT FK_6043E6C812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C677960BB6FE6 FOREIGN KEY (auteur_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY FK_D7098951248D84AC');
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY FK_D7098951BE6CAE90');
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY FK_D70989513FD73900');
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY FK_D70989517ECF78B0');
        $this->addSql('ALTER TABLE coach DROP FOREIGN KEY FK_3F596DCCBF396750');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C12469DE2');
        $this->addSql('ALTER TABLE kids DROP FOREIGN KEY FK_42F8D194BF396750');
        $this->addSql('ALTER TABLE kids_category DROP FOREIGN KEY FK_6043E6C8B71E5B2E');
        $this->addSql('ALTER TABLE kids_category DROP FOREIGN KEY FK_6043E6C812469DE2');
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C677960BB6FE6');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE challenge');
        $this->addSql('DROP TABLE coach');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE kids');
        $this->addSql('DROP TABLE kids_category');
        $this->addSql('DROP TABLE publication');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
