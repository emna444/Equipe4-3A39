<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305074351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, lieu VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, likes INT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, background_color VARCHAR(7) NOT NULL, text_color VARCHAR(255) NOT NULL, image_name VARCHAR(255) DEFAULT NULL, cats_id INT DEFAULT NULL, INDEX IDX_B26681E84200A6 (cats_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE partenaire (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, contact VARCHAR(255) NOT NULL, evenement_id INT DEFAULT NULL, INDEX IDX_32FFA373FD02F13 (evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E84200A6 FOREIGN KEY (cats_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE partenaire ADD CONSTRAINT FK_32FFA373FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE dons CHANGE qr_code_path qr_code_path VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E84200A6');
        $this->addSql('ALTER TABLE partenaire DROP FOREIGN KEY FK_32FFA373FD02F13');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE partenaire');
        $this->addSql('ALTER TABLE dons CHANGE qr_code_path qr_code_path VARCHAR(255) DEFAULT NULL');
    }
}
