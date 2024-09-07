<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240906075857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE frais_deplacement (id INT AUTO_INCREMENT NOT NULL, generic_document_type VARCHAR(255) DEFAULT NULL, document_location VARCHAR(255) DEFAULT NULL, cost NUMERIC(10, 3) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, status VARCHAR(255) NOT NULL, user_id INT NOT NULL, INDEX IDX_AC76F7BAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE frais_deplacement ADD CONSTRAINT FK_AC76F7BAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE equipe ADD minimal_staff_count_before_alert INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE frais_deplacement DROP FOREIGN KEY FK_AC76F7BAA76ED395');
        $this->addSql('DROP TABLE frais_deplacement');
        $this->addSql('ALTER TABLE equipe DROP minimal_staff_count_before_alert');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
