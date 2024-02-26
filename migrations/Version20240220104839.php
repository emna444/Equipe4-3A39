<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240220104839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bonus (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, montant DOUBLE PRECISION NOT NULL, INDEX IDX_9F987F7A6B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dons (id INT AUTO_INCREMENT NOT NULL, bonus_id INT DEFAULT NULL, user_p_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, don_count INT NOT NULL, INDEX IDX_E4F955FA69545666 (bonus_id), INDEX IDX_E4F955FAA9FA2F6B (user_p_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, cin INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, telephone INT NOT NULL, ville VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bonus ADD CONSTRAINT FK_9F987F7A6B899279 FOREIGN KEY (patient_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE dons ADD CONSTRAINT FK_E4F955FA69545666 FOREIGN KEY (bonus_id) REFERENCES bonus (id)');
        $this->addSql('ALTER TABLE dons ADD CONSTRAINT FK_E4F955FAA9FA2F6B FOREIGN KEY (user_p_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bonus DROP FOREIGN KEY FK_9F987F7A6B899279');
        $this->addSql('ALTER TABLE dons DROP FOREIGN KEY FK_E4F955FA69545666');
        $this->addSql('ALTER TABLE dons DROP FOREIGN KEY FK_E4F955FAA9FA2F6B');
        $this->addSql('DROP TABLE bonus');
        $this->addSql('DROP TABLE dons');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
