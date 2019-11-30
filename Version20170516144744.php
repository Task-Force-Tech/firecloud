<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170516144744 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE INDEX first_name ON location (first_name)');
        $this->addSql('CREATE INDEX last_name ON location (last_name)');
        $this->addSql('CREATE INDEX created_date ON location (created_date)');
        $this->addSql('CREATE INDEX modified_date ON location (modified_date)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX first_name ON location');
        $this->addSql('DROP INDEX last_name ON location');
        $this->addSql('DROP INDEX created_date ON location');
        $this->addSql('DROP INDEX modified_date ON location');
    }
}
