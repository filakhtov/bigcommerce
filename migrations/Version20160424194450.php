<?php

namespace Bigcommerce\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160424194450 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_history (user_id INT NOT NULL, history_id INT NOT NULL, INDEX IDX_D7BA0481A76ED395 (user_id), INDEX IDX_D7BA04811E058452 (history_id), PRIMARY KEY(user_id, history_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE history (id INT AUTO_INCREMENT NOT NULL, query VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_27BA704B24BDB5EB (query), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_history ADD CONSTRAINT FK_D7BA0481A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_history ADD CONSTRAINT FK_D7BA04811E058452 FOREIGN KEY (history_id) REFERENCES history (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users_history DROP FOREIGN KEY FK_D7BA04811E058452');
        $this->addSql('DROP TABLE users_history');
        $this->addSql('DROP TABLE history');
    }
}
