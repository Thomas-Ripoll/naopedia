<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180124195942 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, image_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_23A0E66F675F31B (author_id), INDEX IDX_23A0E663DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bird (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, latin_name VARCHAR(255) NOT NULL, ordre VARCHAR(255) NOT NULL, famille VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bird_image (bird_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_A9133CBFE813F9 (bird_id), INDEX IDX_A9133CBF3DA5256D (image_id), PRIMARY KEY(bird_id, image_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, alt VARCHAR(200) NOT NULL, author VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE observation (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, bird_id INT DEFAULT NULL, image_id INT DEFAULT NULL, geoloc LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', date DATETIME NOT NULL, valid TINYINT(1) NOT NULL, INDEX IDX_C576DBE0A76ED395 (user_id), INDEX IDX_C576DBE0E813F9 (bird_id), INDEX IDX_C576DBE03DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE observation_user (observation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_EFC668FC1409DD88 (observation_id), INDEX IDX_EFC668FCA76ED395 (user_id), PRIMARY KEY(observation_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(25) NOT NULL, password VARCHAR(255) NOT NULL, avatar VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, salt VARCHAR(255) NOT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_image (user_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_27FFFF07A76ED395 (user_id), INDEX IDX_27FFFF073DA5256D (image_id), PRIMARY KEY(user_id, image_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E663DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE bird_image ADD CONSTRAINT FK_A9133CBFE813F9 FOREIGN KEY (bird_id) REFERENCES bird (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bird_image ADD CONSTRAINT FK_A9133CBF3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE observation ADD CONSTRAINT FK_C576DBE0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE observation ADD CONSTRAINT FK_C576DBE0E813F9 FOREIGN KEY (bird_id) REFERENCES bird (id)');
        $this->addSql('ALTER TABLE observation ADD CONSTRAINT FK_C576DBE03DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE observation_user ADD CONSTRAINT FK_EFC668FC1409DD88 FOREIGN KEY (observation_id) REFERENCES observation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE observation_user ADD CONSTRAINT FK_EFC668FCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_image ADD CONSTRAINT FK_27FFFF07A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_image ADD CONSTRAINT FK_27FFFF073DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bird_image DROP FOREIGN KEY FK_A9133CBFE813F9');
        $this->addSql('ALTER TABLE observation DROP FOREIGN KEY FK_C576DBE0E813F9');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E663DA5256D');
        $this->addSql('ALTER TABLE bird_image DROP FOREIGN KEY FK_A9133CBF3DA5256D');
        $this->addSql('ALTER TABLE observation DROP FOREIGN KEY FK_C576DBE03DA5256D');
        $this->addSql('ALTER TABLE user_image DROP FOREIGN KEY FK_27FFFF073DA5256D');
        $this->addSql('ALTER TABLE observation_user DROP FOREIGN KEY FK_EFC668FC1409DD88');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66F675F31B');
        $this->addSql('ALTER TABLE observation DROP FOREIGN KEY FK_C576DBE0A76ED395');
        $this->addSql('ALTER TABLE observation_user DROP FOREIGN KEY FK_EFC668FCA76ED395');
        $this->addSql('ALTER TABLE user_image DROP FOREIGN KEY FK_27FFFF07A76ED395');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE bird');
        $this->addSql('DROP TABLE bird_image');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE observation');
        $this->addSql('DROP TABLE observation_user');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_image');
    }
}
