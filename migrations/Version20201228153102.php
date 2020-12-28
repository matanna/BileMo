<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201228153102 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo_phone (photo_id INT NOT NULL, phone_id INT NOT NULL, INDEX IDX_FE1E906A7E9E4C8C (photo_id), INDEX IDX_FE1E906A3B7323CB (phone_id), PRIMARY KEY(photo_id, phone_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE photo_phone ADD CONSTRAINT FK_FE1E906A7E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE photo_phone ADD CONSTRAINT FK_FE1E906A3B7323CB FOREIGN KEY (phone_id) REFERENCES phone (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE photo_phone DROP FOREIGN KEY FK_FE1E906A7E9E4C8C');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE photo_phone');
    }
}
