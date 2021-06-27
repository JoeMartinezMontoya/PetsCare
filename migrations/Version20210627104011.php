<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210627104011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pet_tags (pet_id INT NOT NULL, tags_id INT NOT NULL, INDEX IDX_74F4714C966F7FB6 (pet_id), INDEX IDX_74F4714C8D7B4FB4 (tags_id), PRIMARY KEY(pet_id, tags_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pet_tags ADD CONSTRAINT FK_74F4714C966F7FB6 FOREIGN KEY (pet_id) REFERENCES pet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pet_tags ADD CONSTRAINT FK_74F4714C8D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE tags_pet');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tags_pet (tags_id INT NOT NULL, pet_id INT NOT NULL, INDEX IDX_4307FFCC8D7B4FB4 (tags_id), INDEX IDX_4307FFCC966F7FB6 (pet_id), PRIMARY KEY(tags_id, pet_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tags_pet ADD CONSTRAINT FK_4307FFCC8D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tags_pet ADD CONSTRAINT FK_4307FFCC966F7FB6 FOREIGN KEY (pet_id) REFERENCES pet (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('DROP TABLE pet_tags');
    }
}
