<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250303181742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_formations (user_id INT NOT NULL, formations_id INT NOT NULL, INDEX IDX_E7F7E7DBA76ED395 (user_id), INDEX IDX_E7F7E7DB3BF5B0C2 (formations_id), PRIMARY KEY(user_id, formations_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_lessons (user_id INT NOT NULL, lessons_id INT NOT NULL, INDEX IDX_674F06D3A76ED395 (user_id), INDEX IDX_674F06D3FED07355 (lessons_id), PRIMARY KEY(user_id, lessons_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_formations ADD CONSTRAINT FK_E7F7E7DBA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_formations ADD CONSTRAINT FK_E7F7E7DB3BF5B0C2 FOREIGN KEY (formations_id) REFERENCES formations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_lessons ADD CONSTRAINT FK_674F06D3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_lessons ADD CONSTRAINT FK_674F06D3FED07355 FOREIGN KEY (lessons_id) REFERENCES lessons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purchases DROP FOREIGN KEY FK_AA6431FE5200282E');
        $this->addSql('ALTER TABLE purchases DROP FOREIGN KEY FK_AA6431FECDF80196');
        $this->addSql('ALTER TABLE purchases ADD status VARCHAR(20) NOT NULL, ADD created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE purchases ADD CONSTRAINT FK_AA6431FE5200282E FOREIGN KEY (formation_id) REFERENCES formations (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE purchases ADD CONSTRAINT FK_AA6431FECDF80196 FOREIGN KEY (lesson_id) REFERENCES lessons (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_formations DROP FOREIGN KEY FK_E7F7E7DBA76ED395');
        $this->addSql('ALTER TABLE user_formations DROP FOREIGN KEY FK_E7F7E7DB3BF5B0C2');
        $this->addSql('ALTER TABLE user_lessons DROP FOREIGN KEY FK_674F06D3A76ED395');
        $this->addSql('ALTER TABLE user_lessons DROP FOREIGN KEY FK_674F06D3FED07355');
        $this->addSql('DROP TABLE user_formations');
        $this->addSql('DROP TABLE user_lessons');
        $this->addSql('ALTER TABLE purchases DROP FOREIGN KEY FK_AA6431FECDF80196');
        $this->addSql('ALTER TABLE purchases DROP FOREIGN KEY FK_AA6431FE5200282E');
        $this->addSql('ALTER TABLE purchases DROP status, DROP created_at');
        $this->addSql('ALTER TABLE purchases ADD CONSTRAINT FK_AA6431FECDF80196 FOREIGN KEY (lesson_id) REFERENCES lessons (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE purchases ADD CONSTRAINT FK_AA6431FE5200282E FOREIGN KEY (formation_id) REFERENCES formations (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
