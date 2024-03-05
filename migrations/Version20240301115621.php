<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240301115621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bonus (id INT AUTO_INCREMENT NOT NULL, montant DOUBLE PRECISION NOT NULL, patient_id INT DEFAULT NULL, INDEX IDX_9F987F7A6B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, user_id INT DEFAULT NULL, don_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, INDEX IDX_5F9E962AA76ED395 (user_id), INDEX IDX_5F9E962A7B3C9061 (don_id), INDEX IDX_5F9E962A727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE dons (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, user_id INT NOT NULL, qr_code_path VARCHAR(255) NOT NULL, bonus_id INT DEFAULT NULL, user_p_id INT DEFAULT NULL, INDEX IDX_E4F955FA69545666 (bonus_id), INDEX IDX_E4F955FAA9FA2F6B (user_p_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE bonus ADD CONSTRAINT FK_9F987F7A6B899279 FOREIGN KEY (patient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A7B3C9061 FOREIGN KEY (don_id) REFERENCES dons (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A727ACA70 FOREIGN KEY (parent_id) REFERENCES comments (id)');
        $this->addSql('ALTER TABLE dons ADD CONSTRAINT FK_E4F955FA69545666 FOREIGN KEY (bonus_id) REFERENCES bonus (id)');
        $this->addSql('ALTER TABLE dons ADD CONSTRAINT FK_E4F955FAA9FA2F6B FOREIGN KEY (user_p_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bonus DROP FOREIGN KEY FK_9F987F7A6B899279');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AA76ED395');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A7B3C9061');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A727ACA70');
        $this->addSql('ALTER TABLE dons DROP FOREIGN KEY FK_E4F955FA69545666');
        $this->addSql('ALTER TABLE dons DROP FOREIGN KEY FK_E4F955FAA9FA2F6B');
        $this->addSql('DROP TABLE bonus');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE dons');
    }
}
