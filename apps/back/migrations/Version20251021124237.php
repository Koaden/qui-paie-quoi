<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251021124237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'This migration creates the initial tables for Expense, Participant, and Group entities along with their relationships.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE expense (id INT AUTO_INCREMENT NOT NULL, payer_id INT NOT NULL, group_id INT NOT NULL, title VARCHAR(100) NOT NULL, amount INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_2D3A8DA6C17AD9A9 (payer_id), INDEX IDX_2D3A8DA6FE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expense_participant (expense_id INT NOT NULL, participant_id INT NOT NULL, INDEX IDX_AEF3C78AF395DB7B (expense_id), INDEX IDX_AEF3C78A9D1C3019 (participant_id), PRIMARY KEY(expense_id, participant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expense_group (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_CEC8E3FF7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_participant (group_id INT NOT NULL, participant_id INT NOT NULL, INDEX IDX_F22774D0FE54D947 (group_id), INDEX IDX_F22774D09D1C3019 (participant_id), PRIMARY KEY(group_id, participant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6C17AD9A9 FOREIGN KEY (payer_id) REFERENCES participant (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6FE54D947 FOREIGN KEY (group_id) REFERENCES expense_group (id)');
        $this->addSql('ALTER TABLE expense_participant ADD CONSTRAINT FK_AEF3C78AF395DB7B FOREIGN KEY (expense_id) REFERENCES expense (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE expense_participant ADD CONSTRAINT FK_AEF3C78A9D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE expense_group ADD CONSTRAINT FK_CEC8E3FF7E3C61F9 FOREIGN KEY (owner_id) REFERENCES participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_participant ADD CONSTRAINT FK_F22774D0FE54D947 FOREIGN KEY (group_id) REFERENCES expense_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_participant ADD CONSTRAINT FK_F22774D09D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6C17AD9A9');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6FE54D947');
        $this->addSql('ALTER TABLE expense_participant DROP FOREIGN KEY FK_AEF3C78AF395DB7B');
        $this->addSql('ALTER TABLE expense_participant DROP FOREIGN KEY FK_AEF3C78A9D1C3019');
        $this->addSql('ALTER TABLE expense_group DROP FOREIGN KEY FK_CEC8E3FF7E3C61F9');
        $this->addSql('ALTER TABLE group_participant DROP FOREIGN KEY FK_F22774D0FE54D947');
        $this->addSql('ALTER TABLE group_participant DROP FOREIGN KEY FK_F22774D09D1C3019');
        $this->addSql('DROP TABLE expense');
        $this->addSql('DROP TABLE expense_participant');
        $this->addSql('DROP TABLE expense_group');
        $this->addSql('DROP TABLE group_participant');
        $this->addSql('DROP TABLE participant');
    }
}
