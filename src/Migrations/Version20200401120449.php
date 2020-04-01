<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200401120449 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_users CHANGE tussenvoegsel tussenvoegsel VARCHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE activiteiten ADD max_deelnemers INT NOT NULL, CHANGE soort_id soort_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activiteiten DROP max_deelnemers, CHANGE soort_id soort_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_users CHANGE tussenvoegsel tussenvoegsel VARCHAR(10) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_unicode_ci`');
    }
}
