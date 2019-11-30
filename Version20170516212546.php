<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170516212546 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE location ADD home_type INT DEFAULT NULL, ADD levels INT DEFAULT NULL, ADD floor INT DEFAULT NULL, ADD balcony_access TINYINT(1) DEFAULT NULL, ADD bedrooms INT DEFAULT NULL, ADD basement TINYINT(1) DEFAULT NULL, ADD solar_panels TINYINT(1) DEFAULT NULL, ADD fireplace TINYINT(1) DEFAULT NULL, ADD hazardous_materials LONGTEXT DEFAULT NULL, ADD gated_community TINYINT(1) DEFAULT NULL, ADD gate_code VARCHAR(255) DEFAULT NULL, ADD medical_concerns LONGTEXT DEFAULT NULL, ADD pets TINYINT(1) DEFAULT NULL, ADD pets_details LONGTEXT DEFAULT NULL, ADD square_feet INT DEFAULT NULL, ADD improvement_type VARCHAR(255) DEFAULT NULL, ADD foundation VARCHAR(255) DEFAULT NULL, ADD year_built VARCHAR(4) DEFAULT NULL, ADD quality VARCHAR(255) DEFAULT NULL, ADD interior VARCHAR(255) DEFAULT NULL, ADD exterior VARCHAR(255) DEFAULT NULL, ADD roof_type VARCHAR(255) DEFAULT NULL, ADD roof_cover VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE location DROP home_type, DROP levels, DROP floor, DROP balcony_access, DROP bedrooms, DROP basement, DROP solar_panels, DROP fireplace, DROP hazardous_materials, DROP gated_community, DROP gate_code, DROP medical_concerns, DROP pets, DROP pets_details, DROP square_feet, DROP improvement_type, DROP foundation, DROP year_built, DROP quality, DROP interior, DROP exterior, DROP roof_type, DROP roof_cover');
    }
}
