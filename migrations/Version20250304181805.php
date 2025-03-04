<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250304181805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_completed_formations (user_id INT NOT NULL, formations_id INT NOT NULL, INDEX IDX_553F7FFA76ED395 (user_id), INDEX IDX_553F7FF3BF5B0C2 (formations_id), PRIMARY KEY(user_id, formations_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_completed_formations ADD CONSTRAINT FK_553F7FFA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_completed_formations ADD CONSTRAINT FK_553F7FF3BF5B0C2 FOREIGN KEY (formations_id) REFERENCES formations (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_completed_formations DROP FOREIGN KEY FK_553F7FFA76ED395');
        $this->addSql('ALTER TABLE user_completed_formations DROP FOREIGN KEY FK_553F7FF3BF5B0C2');
        $this->addSql('DROP TABLE user_completed_formations');
    }
}
