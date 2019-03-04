<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190223175240 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE wallet_cryptomonney');
        $this->addSql('ALTER TABLE cryptomonney ADD wallets_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cryptomonney ADD CONSTRAINT FK_2AA7913CC3B43BA3 FOREIGN KEY (wallets_id) REFERENCES wallet (id)');
        $this->addSql('CREATE INDEX IDX_2AA7913CC3B43BA3 ON cryptomonney (wallets_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE wallet_cryptomonney (wallet_id INT NOT NULL, cryptomonney_id INT NOT NULL, INDEX IDX_650D8E926BD854FE (cryptomonney_id), INDEX IDX_650D8E92712520F3 (wallet_id), PRIMARY KEY(wallet_id, cryptomonney_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE wallet_cryptomonney ADD CONSTRAINT FK_650D8E926BD854FE FOREIGN KEY (cryptomonney_id) REFERENCES cryptomonney (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wallet_cryptomonney ADD CONSTRAINT FK_650D8E92712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cryptomonney DROP FOREIGN KEY FK_2AA7913CC3B43BA3');
        $this->addSql('DROP INDEX IDX_2AA7913CC3B43BA3 ON cryptomonney');
        $this->addSql('ALTER TABLE cryptomonney DROP wallets_id');
    }
}
