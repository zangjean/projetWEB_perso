<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250329100417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE l3_paniers (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, id_utilisateur INTEGER NOT NULL, id_produit INTEGER NOT NULL --libellé du produit
            , CONSTRAINT FK_661EDEBD50EAE44 FOREIGN KEY (id_utilisateur) REFERENCES l3_utilisateurs (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_661EDEBDF7384557 FOREIGN KEY (id_produit) REFERENCES l3_produits (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_661EDEBD50EAE44 ON l3_paniers (id_utilisateur)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_661EDEBDF7384557 ON l3_paniers (id_produit)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_ID_UTILISATEUR_ID_PRODUIT ON l3_paniers (id_utilisateur, id_produit)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE l3_paniers
        SQL);
    }
}
