<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230828164040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar_person MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE calendar_person DROP FOREIGN KEY FK_9EBAC92A217BBB47');
        $this->addSql('ALTER TABLE calendar_person DROP FOREIGN KEY FK_9EBAC92AA40A2C8');
        $this->addSql('DROP INDEX `primary` ON calendar_person');
        $this->addSql('ALTER TABLE calendar_person DROP id, DROP position');
        $this->addSql('ALTER TABLE calendar_person ADD CONSTRAINT FK_9EBAC92A217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE calendar_person ADD CONSTRAINT FK_9EBAC92AA40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE calendar_person ADD PRIMARY KEY (calendar_id, person_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar_person DROP FOREIGN KEY FK_9EBAC92AA40A2C8');
        $this->addSql('ALTER TABLE calendar_person DROP FOREIGN KEY FK_9EBAC92A217BBB47');
        $this->addSql('ALTER TABLE calendar_person ADD id INT AUTO_INCREMENT NOT NULL, ADD position INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE calendar_person ADD CONSTRAINT FK_9EBAC92AA40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id)');
        $this->addSql('ALTER TABLE calendar_person ADD CONSTRAINT FK_9EBAC92A217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
    }
}
