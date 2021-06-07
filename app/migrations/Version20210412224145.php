<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210412224145 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE immo.contact_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE immo.favorite_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE immo.picture_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE immo.property_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE immo.contact (id INT NOT NULL, property_id INT NOT NULL, prospect_id INT NOT NULL, desired_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CF5FC35E549213EC ON immo.contact (property_id)');
        $this->addSql('CREATE INDEX IDX_CF5FC35ED182060A ON immo.contact (prospect_id)');
        $this->addSql('CREATE TABLE immo.favorite (id INT NOT NULL, prospect_id INT NOT NULL, property_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CC977791D182060A ON immo.favorite (prospect_id)');
        $this->addSql('CREATE INDEX IDX_CC977791549213EC ON immo.favorite (property_id)');
        $this->addSql('CREATE TABLE immo.picture (id INT NOT NULL, file_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE immo.property (id INT NOT NULL, owner_id INT NOT NULL, type VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, area DOUBLE PRECISION NOT NULL, description TEXT NOT NULL, construction_date DATE NOT NULL, floor INT DEFAULT NULL, floors INT NOT NULL, rooms INT NOT NULL, bedrooms INT NOT NULL, bathrooms INT NOT NULL, toilets INT NOT NULL, is_furnished BOOLEAN NOT NULL, contains_storage BOOLEAN NOT NULL, is_kitchen_separated BOOLEAN NOT NULL, contain_dining_room BOOLEAN NOT NULL, ground VARCHAR(255) NOT NULL, heater VARCHAR(255) NOT NULL, fireplace BOOLEAN NOT NULL, elevator BOOLEAN NOT NULL, external_storage BOOLEAN NOT NULL, area_external_storage DOUBLE PRECISION NOT NULL, guarding BOOLEAN NOT NULL, energy_consumption INT NOT NULL, gas_emissions INT NOT NULL, address VARCHAR(255) NOT NULL, zip_code INT NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, rent_or_sale VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, charges DOUBLE PRECISION NOT NULL, guarentee DOUBLE PRECISION NOT NULL, fees_price DOUBLE PRECISION NOT NULL, inventory_price DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2FA0E5967E3C61F9 ON immo.property (owner_id)');
        $this->addSql('ALTER TABLE immo.contact ADD CONSTRAINT FK_CF5FC35E549213EC FOREIGN KEY (property_id) REFERENCES immo.property (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE immo.contact ADD CONSTRAINT FK_CF5FC35ED182060A FOREIGN KEY (prospect_id) REFERENCES immo.user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE immo.favorite ADD CONSTRAINT FK_CC977791D182060A FOREIGN KEY (prospect_id) REFERENCES immo.user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE immo.favorite ADD CONSTRAINT FK_CC977791549213EC FOREIGN KEY (property_id) REFERENCES immo.property (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE immo.property ADD CONSTRAINT FK_2FA0E5967E3C61F9 FOREIGN KEY (owner_id) REFERENCES immo.user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE immo.user_account ADD forgot_password_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE immo.user_account ADD token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE immo.user_account ADD enabled BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE immo.user_account ADD firstname VARCHAR(80) NOT NULL');
        $this->addSql('ALTER TABLE immo.user_account ADD lastname VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE immo.user_account DROP name');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE immo.contact DROP CONSTRAINT FK_CF5FC35E549213EC');
        $this->addSql('ALTER TABLE immo.favorite DROP CONSTRAINT FK_CC977791549213EC');
        $this->addSql('DROP SEQUENCE immo.contact_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE immo.favorite_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE immo.picture_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE immo.property_id_seq CASCADE');
        $this->addSql('DROP TABLE immo.contact');
        $this->addSql('DROP TABLE immo.favorite');
        $this->addSql('DROP TABLE immo.picture');
        $this->addSql('DROP TABLE immo.property');
        $this->addSql('ALTER TABLE immo.user_account ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE immo.user_account DROP forgot_password_token');
        $this->addSql('ALTER TABLE immo.user_account DROP token');
        $this->addSql('ALTER TABLE immo.user_account DROP enabled');
        $this->addSql('ALTER TABLE immo.user_account DROP firstname');
        $this->addSql('ALTER TABLE immo.user_account DROP lastname');
    }
}
