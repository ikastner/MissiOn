<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241031131829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD gestionnaire_id INT DEFAULT NULL, ADD personel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496885AC1B FOREIGN KEY (gestionnaire_id) REFERENCES gestionnaire (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A8C3AF89 FOREIGN KEY (personel_id) REFERENCES personel (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6496885AC1B ON user (gestionnaire_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649A8C3AF89 ON user (personel_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6496885AC1B');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649A8C3AF89');
        $this->addSql('DROP INDEX UNIQ_8D93D6496885AC1B ON `user`');
        $this->addSql('DROP INDEX UNIQ_8D93D649A8C3AF89 ON `user`');
        $this->addSql('ALTER TABLE `user` DROP gestionnaire_id, DROP personel_id');
    }
}
