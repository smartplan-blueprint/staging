<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250904131252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE api_session (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, token VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_3D7AA1B3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE api_session ADD CONSTRAINT FK_3D7AA1B3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('DROP INDEX client_id ON api_key');
        $this->addSql('ALTER TABLE api_key CHANGE is_active is_active TINYINT(1) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE api_key ADD CONSTRAINT FK_C912ED9DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_C912ED9DA76ED395 ON api_key (user_id)');
        $this->addSql('ALTER TABLE users DROP is_verified, CHANGE roles roles JSON NOT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE api_key api_key VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users RENAME INDEX email TO UNIQ_1483A5E9E7927C74');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE api_session DROP FOREIGN KEY FK_3D7AA1B3A76ED395');
        $this->addSql('DROP TABLE api_session');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE api_key DROP FOREIGN KEY FK_C912ED9DA76ED395');
        $this->addSql('DROP INDEX IDX_C912ED9DA76ED395 ON api_key');
        $this->addSql('ALTER TABLE api_key CHANGE is_active is_active TINYINT(1) DEFAULT 1, CHANGE created_at created_at DATETIME DEFAULT \'current_timestamp()\' NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX client_id ON api_key (client_id)');
        $this->addSql('ALTER TABLE users ADD is_verified TINYINT(1) DEFAULT 0, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE api_key api_key VARCHAR(255) NOT NULL, CHANGE created_at created_at DATETIME DEFAULT \'current_timestamp()\', CHANGE updated_at updated_at DATETIME DEFAULT \'current_timestamp()\'');
        $this->addSql('ALTER TABLE users RENAME INDEX uniq_1483a5e9e7927c74 TO email');
    }
}
