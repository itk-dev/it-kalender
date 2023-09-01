<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230827203316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE calendar_person (id INT AUTO_INCREMENT NOT NULL, calendar_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', person_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_9EBAC92AA40A2C8 (calendar_id), INDEX IDX_9EBAC92A217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE calendar_person ADD CONSTRAINT FK_9EBAC92AA40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id)');
        $this->addSql('ALTER TABLE calendar_person ADD CONSTRAINT FK_9EBAC92A217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar_person DROP FOREIGN KEY FK_9EBAC92AA40A2C8');
        $this->addSql('ALTER TABLE calendar_person DROP FOREIGN KEY FK_9EBAC92A217BBB47');
        $this->addSql('DROP TABLE calendar_person');
    }
}
