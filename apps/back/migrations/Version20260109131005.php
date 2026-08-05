<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260109131005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add invitation entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE invitation (id INT AUTO_INCREMENT NOT NULL, participant_id INT NOT NULL, group_id INT NOT NULL, code VARCHAR(14) NOT NULL, UNIQUE INDEX UNIQ_F11D61A277153098 (code), INDEX IDX_F11D61A29D1C3019 (participant_id), INDEX IDX_F11D61A2FE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A29D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id)');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2FE54D947 FOREIGN KEY (group_id) REFERENCES expense_group (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE invitation DROP FOREIGN KEY FK_F11D61A29D1C3019');
        $this->addSql('ALTER TABLE invitation DROP FOREIGN KEY FK_F11D61A2FE54D947');
        $this->addSql('DROP TABLE invitation');
    }
}
