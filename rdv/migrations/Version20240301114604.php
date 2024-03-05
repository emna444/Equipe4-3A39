<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240301114604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE suivi (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, medecin_id INT DEFAULT NULL, rendezvous_id INT DEFAULT NULL, ordonnance VARCHAR(255) NOT NULL, INDEX IDX_2EBCCA8FA76ED395 (user_id), INDEX IDX_2EBCCA8F4F31A84 (medecin_id), UNIQUE INDEX UNIQ_2EBCCA8F3345E0A3 (rendezvous_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE suivi ADD CONSTRAINT FK_2EBCCA8FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE suivi ADD CONSTRAINT FK_2EBCCA8F4F31A84 FOREIGN KEY (medecin_id) REFERENCES medecin (id)');
        $this->addSql('ALTER TABLE suivi ADD CONSTRAINT FK_2EBCCA8F3345E0A3 FOREIGN KEY (rendezvous_id) REFERENCES rendezvous (id)');
        $this->addSql('ALTER TABLE reservation CHANGE status status INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE suivi DROP FOREIGN KEY FK_2EBCCA8FA76ED395');
        $this->addSql('ALTER TABLE suivi DROP FOREIGN KEY FK_2EBCCA8F4F31A84');
        $this->addSql('ALTER TABLE suivi DROP FOREIGN KEY FK_2EBCCA8F3345E0A3');
        $this->addSql('DROP TABLE suivi');
        $this->addSql('ALTER TABLE reservation CHANGE status status VARCHAR(255) NOT NULL');
    }
}
