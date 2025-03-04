<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250304174459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE progress ADD formation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE progress ADD CONSTRAINT FK_2201F2465200282E FOREIGN KEY (formation_id) REFERENCES formations (id)');
        $this->addSql('CREATE INDEX IDX_2201F2465200282E ON progress (formation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE progress DROP FOREIGN KEY FK_2201F2465200282E');
        $this->addSql('DROP INDEX IDX_2201F2465200282E ON progress');
        $this->addSql('ALTER TABLE progress DROP formation_id');
    }
}
