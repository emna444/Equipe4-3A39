<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240228140210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE medecin (id INT AUTO_INCREMENT NOT NULL, cin INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, telephone INT NOT NULL, spacialite VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE rendezvous ADD date DATETIME NOT NULL, ADD etat INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD description VARCHAR(255) NOT NULL, ADD status INT NOT NULL, ADD patient_id INT DEFAULT NULL, ADD medecin_id INT DEFAULT NULL, ADD rendezvous_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849556B899279 FOREIGN KEY (patient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849554F31A84 FOREIGN KEY (medecin_id) REFERENCES medecin (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849553345E0A3 FOREIGN KEY (rendezvous_id) REFERENCES rendezvous (id)');
        $this->addSql('CREATE INDEX IDX_42C849556B899279 ON reservation (patient_id)');
        $this->addSql('CREATE INDEX IDX_42C849554F31A84 ON reservation (medecin_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42C849553345E0A3 ON reservation (rendezvous_id)');
        $this->addSql('ALTER TABLE suivi ADD ordonnance VARCHAR(255) NOT NULL, ADD user_id INT DEFAULT NULL, ADD medecin_id INT DEFAULT NULL, ADD rendezvous_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE suivi ADD CONSTRAINT FK_2EBCCA8FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE suivi ADD CONSTRAINT FK_2EBCCA8F4F31A84 FOREIGN KEY (medecin_id) REFERENCES medecin (id)');
        $this->addSql('ALTER TABLE suivi ADD CONSTRAINT FK_2EBCCA8F3345E0A3 FOREIGN KEY (rendezvous_id) REFERENCES rendezvous (id)');
        $this->addSql('CREATE INDEX IDX_2EBCCA8FA76ED395 ON suivi (user_id)');
        $this->addSql('CREATE INDEX IDX_2EBCCA8F4F31A84 ON suivi (medecin_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2EBCCA8F3345E0A3 ON suivi (rendezvous_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE medecin');
        $this->addSql('ALTER TABLE rendezvous DROP date, DROP etat');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849556B899279');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849554F31A84');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849553345E0A3');
        $this->addSql('DROP INDEX IDX_42C849556B899279 ON reservation');
        $this->addSql('DROP INDEX IDX_42C849554F31A84 ON reservation');
        $this->addSql('DROP INDEX UNIQ_42C849553345E0A3 ON reservation');
        $this->addSql('ALTER TABLE reservation DROP description, DROP status, DROP patient_id, DROP medecin_id, DROP rendezvous_id');
        $this->addSql('ALTER TABLE suivi DROP FOREIGN KEY FK_2EBCCA8FA76ED395');
        $this->addSql('ALTER TABLE suivi DROP FOREIGN KEY FK_2EBCCA8F4F31A84');
        $this->addSql('ALTER TABLE suivi DROP FOREIGN KEY FK_2EBCCA8F3345E0A3');
        $this->addSql('DROP INDEX IDX_2EBCCA8FA76ED395 ON suivi');
        $this->addSql('DROP INDEX IDX_2EBCCA8F4F31A84 ON suivi');
        $this->addSql('DROP INDEX UNIQ_2EBCCA8F3345E0A3 ON suivi');
        $this->addSql('ALTER TABLE suivi DROP ordonnance, DROP user_id, DROP medecin_id, DROP rendezvous_id');
    }
}
