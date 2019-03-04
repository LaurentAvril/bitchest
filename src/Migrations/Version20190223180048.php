<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190223180048 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cryptomonney DROP FOREIGN KEY FK_2AA7913CC3B43BA3');
        $this->addSql('DROP INDEX IDX_2AA7913CC3B43BA3 ON cryptomonney');
        $this->addSql('ALTER TABLE cryptomonney DROP wallets_id');
        $this->addSql('ALTER TABLE wallet ADD cryptomonney_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921F6BD854FE FOREIGN KEY (cryptomonney_id) REFERENCES cryptomonney (id)');
        $this->addSql('CREATE INDEX IDX_7C68921F6BD854FE ON wallet (cryptomonney_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cryptomonney ADD wallets_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cryptomonney ADD CONSTRAINT FK_2AA7913CC3B43BA3 FOREIGN KEY (wallets_id) REFERENCES wallet (id)');
        $this->addSql('CREATE INDEX IDX_2AA7913CC3B43BA3 ON cryptomonney (wallets_id)');
        $this->addSql('ALTER TABLE wallet DROP FOREIGN KEY FK_7C68921F6BD854FE');
        $this->addSql('DROP INDEX IDX_7C68921F6BD854FE ON wallet');
        $this->addSql('ALTER TABLE wallet DROP cryptomonney_id');
    }
}
