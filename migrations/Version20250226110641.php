<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250226110641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certifications CHANGE lessons_id lesson_id INT NOT NULL');
        $this->addSql('ALTER TABLE certifications ADD CONSTRAINT FK_3B0D76D5CDF80196 FOREIGN KEY (lesson_id) REFERENCES lessons (id)');
        $this->addSql('ALTER TABLE certifications ADD CONSTRAINT FK_3B0D76D5A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_3B0D76D5CDF80196 ON certifications (lesson_id)');
        $this->addSql('CREATE INDEX IDX_3B0D76D5A76ED395 ON certifications (user_id)');
        $this->addSql('ALTER TABLE formations DROP price, DROP created_at, DROP updated_at, CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE lessons ADD formation_id INT NOT NULL');
        $this->addSql('ALTER TABLE lessons ADD CONSTRAINT FK_3F4218D95200282E FOREIGN KEY (formation_id) REFERENCES formations (id)');
        $this->addSql('CREATE INDEX IDX_3F4218D95200282E ON lessons (formation_id)');
        $this->addSql('ALTER TABLE progress CHANGE lessons_id lesson_id INT NOT NULL');
        $this->addSql('ALTER TABLE progress ADD CONSTRAINT FK_2201F246CDF80196 FOREIGN KEY (lesson_id) REFERENCES lessons (id)');
        $this->addSql('ALTER TABLE progress ADD CONSTRAINT FK_2201F246A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_2201F246CDF80196 ON progress (lesson_id)');
        $this->addSql('CREATE INDEX IDX_2201F246A76ED395 ON progress (user_id)');
        $this->addSql('ALTER TABLE purchases ADD lesson_id INT DEFAULT NULL, ADD formation_id INT DEFAULT NULL, DROP lessons_id, DROP formations_id');
        $this->addSql('ALTER TABLE purchases ADD CONSTRAINT FK_AA6431FEA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE purchases ADD CONSTRAINT FK_AA6431FECDF80196 FOREIGN KEY (lesson_id) REFERENCES lessons (id)');
        $this->addSql('ALTER TABLE purchases ADD CONSTRAINT FK_AA6431FE5200282E FOREIGN KEY (formation_id) REFERENCES formations (id)');
        $this->addSql('CREATE INDEX IDX_AA6431FEA76ED395 ON purchases (user_id)');
        $this->addSql('CREATE INDEX IDX_AA6431FECDF80196 ON purchases (lesson_id)');
        $this->addSql('CREATE INDEX IDX_AA6431FE5200282E ON purchases (formation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certifications DROP FOREIGN KEY FK_3B0D76D5CDF80196');
        $this->addSql('ALTER TABLE certifications DROP FOREIGN KEY FK_3B0D76D5A76ED395');
        $this->addSql('DROP INDEX IDX_3B0D76D5CDF80196 ON certifications');
        $this->addSql('DROP INDEX IDX_3B0D76D5A76ED395 ON certifications');
        $this->addSql('ALTER TABLE certifications CHANGE lesson_id lessons_id INT NOT NULL');
        $this->addSql('ALTER TABLE lessons DROP FOREIGN KEY FK_3F4218D95200282E');
        $this->addSql('DROP INDEX IDX_3F4218D95200282E ON lessons');
        $this->addSql('ALTER TABLE lessons DROP formation_id');
        $this->addSql('ALTER TABLE progress DROP FOREIGN KEY FK_2201F246CDF80196');
        $this->addSql('ALTER TABLE progress DROP FOREIGN KEY FK_2201F246A76ED395');
        $this->addSql('DROP INDEX IDX_2201F246CDF80196 ON progress');
        $this->addSql('DROP INDEX IDX_2201F246A76ED395 ON progress');
        $this->addSql('ALTER TABLE progress CHANGE lesson_id lessons_id INT NOT NULL');
        $this->addSql('ALTER TABLE purchases DROP FOREIGN KEY FK_AA6431FEA76ED395');
        $this->addSql('ALTER TABLE purchases DROP FOREIGN KEY FK_AA6431FECDF80196');
        $this->addSql('ALTER TABLE purchases DROP FOREIGN KEY FK_AA6431FE5200282E');
        $this->addSql('DROP INDEX IDX_AA6431FEA76ED395 ON purchases');
        $this->addSql('DROP INDEX IDX_AA6431FECDF80196 ON purchases');
        $this->addSql('DROP INDEX IDX_AA6431FE5200282E ON purchases');
        $this->addSql('ALTER TABLE purchases ADD lessons_id INT DEFAULT NULL, ADD formations_id INT DEFAULT NULL, DROP lesson_id, DROP formation_id');
        $this->addSql('ALTER TABLE formations ADD price DOUBLE PRECISION NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE description description VARCHAR(255) NOT NULL');
    }
}
