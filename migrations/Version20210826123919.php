<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210826123919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pet (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, age INT NOT NULL, species INT NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, gender INT NOT NULL, is_missing TINYINT(1) NOT NULL, INDEX IDX_E4529B857E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pet_tags (pet_id INT NOT NULL, tags_id INT NOT NULL, INDEX IDX_74F4714C966F7FB6 (pet_id), INDEX IDX_74F4714C8D7B4FB4 (tags_id), PRIMARY KEY(pet_id, tags_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, pet_id INT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_16DB4F89966F7FB6 (pet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, category INT NOT NULL, duration INT DEFAULT NULL, duration_type INT DEFAULT NULL, pet_sitting_start DATETIME DEFAULT NULL, last_seen DATETIME DEFAULT NULL, missing_pet INT DEFAULT NULL, species INT DEFAULT NULL, pets_to_be_watched LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', lat DOUBLE PRECISION DEFAULT NULL, lng DOUBLE PRECISION DEFAULT NULL, town VARCHAR(255) DEFAULT NULL, INDEX IDX_5A8A6C8DF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_tags (post_id INT NOT NULL, tags_id INT NOT NULL, INDEX IDX_A6E9F32D4B89032C (post_id), INDEX IDX_A6E9F32D8D7B4FB4 (tags_id), PRIMARY KEY(post_id, tags_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_picture (post_id INT NOT NULL, picture_id INT NOT NULL, INDEX IDX_2B33F8BA4B89032C (post_id), INDEX IDX_2B33F8BAEE45BDBF (picture_id), PRIMARY KEY(post_id, picture_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, category INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, phone INT DEFAULT NULL, birthdate DATETIME NOT NULL, is_petsitter TINYINT(1) NOT NULL, lat DOUBLE PRECISION DEFAULT NULL, lng DOUBLE PRECISION DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pet ADD CONSTRAINT FK_E4529B857E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pet_tags ADD CONSTRAINT FK_74F4714C966F7FB6 FOREIGN KEY (pet_id) REFERENCES pet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pet_tags ADD CONSTRAINT FK_74F4714C8D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89966F7FB6 FOREIGN KEY (pet_id) REFERENCES pet (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post_tags ADD CONSTRAINT FK_A6E9F32D4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_tags ADD CONSTRAINT FK_A6E9F32D8D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_picture ADD CONSTRAINT FK_2B33F8BA4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_picture ADD CONSTRAINT FK_2B33F8BAEE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pet_tags DROP FOREIGN KEY FK_74F4714C966F7FB6');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89966F7FB6');
        $this->addSql('ALTER TABLE post_picture DROP FOREIGN KEY FK_2B33F8BAEE45BDBF');
        $this->addSql('ALTER TABLE post_tags DROP FOREIGN KEY FK_A6E9F32D4B89032C');
        $this->addSql('ALTER TABLE post_picture DROP FOREIGN KEY FK_2B33F8BA4B89032C');
        $this->addSql('ALTER TABLE pet_tags DROP FOREIGN KEY FK_74F4714C8D7B4FB4');
        $this->addSql('ALTER TABLE post_tags DROP FOREIGN KEY FK_A6E9F32D8D7B4FB4');
        $this->addSql('ALTER TABLE pet DROP FOREIGN KEY FK_E4529B857E3C61F9');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF675F31B');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE pet');
        $this->addSql('DROP TABLE pet_tags');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_tags');
        $this->addSql('DROP TABLE post_picture');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE user');
    }
}
