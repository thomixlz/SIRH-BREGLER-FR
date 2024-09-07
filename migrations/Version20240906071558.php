<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240906071558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE absence (id INT AUTO_INCREMENT NOT NULL, type_absence VARCHAR(255) NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, reason VARCHAR(255) DEFAULT NULL, document VARCHAR(255) DEFAULT NULL, employee_id INT NOT NULL, approved_by_id INT DEFAULT NULL, INDEX IDX_765AE0C98C03F15C (employee_id), INDEX IDX_765AE0C92D234F6A (approved_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C98C03F15C FOREIGN KEY (employee_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C92D234F6A FOREIGN KEY (approved_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C98C03F15C');
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C92D234F6A');
        $this->addSql('DROP TABLE absence');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
