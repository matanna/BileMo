<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201228150945 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phone (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, model VARCHAR(255) NOT NULL, catch_phrase LONGTEXT DEFAULT NULL, description LONGTEXT NOT NULL, price DOUBLE PRECISION NOT NULL, color VARCHAR(255) DEFAULT NULL, size VARCHAR(255) DEFAULT NULL, battery_power VARCHAR(255) DEFAULT NULL, os_name VARCHAR(255) DEFAULT NULL, weight VARCHAR(255) DEFAULT NULL, memory INT DEFAULT NULL, availability TINYINT(1) DEFAULT NULL, INDEX IDX_444F97DD44F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phone_brand (id INT AUTO_INCREMENT NOT NULL, brand VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE phone ADD CONSTRAINT FK_444F97DD44F5D008 FOREIGN KEY (brand_id) REFERENCES phone_brand (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE phone DROP FOREIGN KEY FK_444F97DD44F5D008');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE phone');
        $this->addSql('DROP TABLE phone_brand');
        $this->addSql('DROP TABLE `user`');
    }
}
