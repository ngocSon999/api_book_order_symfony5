<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240204123234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_author DROP FOREIGN KEY book_author_ibfk_1');
        $this->addSql('ALTER TABLE book_author DROP FOREIGN KEY book_author_ibfk_2');
        $this->addSql('DROP INDEX author_id ON book_author');
        $this->addSql('DROP INDEX book_id ON book_author');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `book_author` ADD CONSTRAINT book_author_ibfk_1 FOREIGN KEY (book_id) REFERENCES book (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE `book_author` ADD CONSTRAINT book_author_ibfk_2 FOREIGN KEY (author_id) REFERENCES author (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX author_id ON `book_author` (author_id)');
        $this->addSql('CREATE INDEX book_id ON `book_author` (book_id)');
    }
}
