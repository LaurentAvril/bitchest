<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190223163102 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE avatar (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1677722FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cryptomonney (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, actual_currency DOUBLE PRECISION NOT NULL, variation_of_day DOUBLE PRECISION NOT NULL, history LONGTEXT NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, funds DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wallet (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, quantity DOUBLE PRECISION NOT NULL, INDEX IDX_7C68921FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wallet_cryptomonney (wallet_id INT NOT NULL, cryptomonney_id INT NOT NULL, INDEX IDX_650D8E92712520F3 (wallet_id), INDEX IDX_650D8E926BD854FE (cryptomonney_id), PRIMARY KEY(wallet_id, cryptomonney_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avatar ADD CONSTRAINT FK_1677722FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE wallet_cryptomonney ADD CONSTRAINT FK_650D8E92712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wallet_cryptomonney ADD CONSTRAINT FK_650D8E926BD854FE FOREIGN KEY (cryptomonney_id) REFERENCES cryptomonney (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE wallet_cryptomonney DROP FOREIGN KEY FK_650D8E926BD854FE');
        $this->addSql('ALTER TABLE avatar DROP FOREIGN KEY FK_1677722FA76ED395');
        $this->addSql('ALTER TABLE wallet DROP FOREIGN KEY FK_7C68921FA76ED395');
        $this->addSql('ALTER TABLE wallet_cryptomonney DROP FOREIGN KEY FK_650D8E92712520F3');
        $this->addSql('DROP TABLE avatar');
        $this->addSql('DROP TABLE cryptomonney');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP TABLE wallet_cryptomonney');
    }
}
