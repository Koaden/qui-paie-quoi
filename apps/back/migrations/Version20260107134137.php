<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260107134137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create group_membership table, migrate existing member-group relations with roles and add creator (Member) to Expense entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE group_membership (id INT AUTO_INCREMENT NOT NULL, member_id INT NOT NULL, group_id INT NOT NULL, role VARCHAR(10) NOT NULL, INDEX IDX_5132B3377597D3FE (member_id), INDEX IDX_5132B337FE54D947 (group_id), UNIQUE INDEX UNIQ_5132B3377597D3FEFE54D947 (member_id, group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE group_membership ADD CONSTRAINT FK_5132B3377597D3FE FOREIGN KEY (member_id) REFERENCES `member` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_membership ADD CONSTRAINT FK_5132B337FE54D947 FOREIGN KEY (group_id) REFERENCES expense_group (id) ON DELETE CASCADE');

        // Every member linked to a participant in a group gets a membership with role VIEWER
        $this->addSql('
            INSERT INTO group_membership (member_id, group_id, role)
            SELECT DISTINCT
                m.id AS member_id,
                gp.group_id AS group_id,
                "VIEWER" AS role
            FROM group_participant gp
            INNER JOIN participant p ON p.id = gp.participant_id
            INNER JOIN member m ON m.participant_id = p.id
        ');

        // Promote group owners to OWNER
        $this->addSql('
            UPDATE group_membership gm
            INNER JOIN expense_group g ON g.id = gm.group_id
            SET gm.role = "OWNER"
            WHERE g.owner_id = gm.member_id
        ');

        $this->addSql('ALTER TABLE expense ADD creator_id INT DEFAULT NULL');

        // Set creator value with the group owner
        $this->addSql('
            UPDATE expense e
            INNER JOIN expense_group g ON g.id = e.group_id
            SET e.creator_id = g.owner_id
        ');

        $this->addSql('ALTER TABLE expense MODIFY creator_id INT NOT NULL');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA661220EA6 FOREIGN KEY (creator_id) REFERENCES member (id)');
        $this->addSql('CREATE INDEX IDX_2D3A8DA661220EA6 ON expense (creator_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA661220EA6');
        $this->addSql('DROP INDEX IDX_2D3A8DA661220EA6 ON expense');
        $this->addSql('ALTER TABLE expense DROP creator_id');

        $this->addSql('ALTER TABLE group_membership DROP FOREIGN KEY FK_5132B3377597D3FE');
        $this->addSql('ALTER TABLE group_membership DROP FOREIGN KEY FK_5132B337FE54D947');
        $this->addSql('DROP TABLE group_membership');
    }
}
