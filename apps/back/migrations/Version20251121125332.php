<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251121125332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'This migration creates the Member entity and updates Group to reference Member as owner instead of Participant.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `member` (id INT AUTO_INCREMENT NOT NULL, participant_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_70E4FA78E7927C74 (email), UNIQUE INDEX UNIQ_70E4FA789D1C3019 (participant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `member` ADD CONSTRAINT FK_70E4FA789D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id)');
        $this->addSql('ALTER TABLE expense_group DROP FOREIGN KEY FK_CEC8E3FF7E3C61F9');
        $this->addSql('ALTER TABLE expense_group ADD CONSTRAINT FK_CEC8E3FF7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `member` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE expense_group DROP FOREIGN KEY FK_CEC8E3FF7E3C61F9');
        $this->addSql('ALTER TABLE `member` DROP FOREIGN KEY FK_70E4FA789D1C3019');
        $this->addSql('DROP TABLE `member`');
        $this->addSql('ALTER TABLE expense_group DROP FOREIGN KEY FK_CEC8E3FF7E3C61F9');
        $this->addSql('ALTER TABLE expense_group ADD CONSTRAINT FK_CEC8E3FF7E3C61F9 FOREIGN KEY (owner_id) REFERENCES participant (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
