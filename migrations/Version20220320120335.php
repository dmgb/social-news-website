<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220320120335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, story_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, parent_id INT UNSIGNED DEFAULT NULL, body VARCHAR(255) NOT NULL, short_id VARCHAR(10) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, score INT DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_9474526CF8496E51 (short_id), INDEX IDX_9474526CAA5D4036 (story_id), INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526C727ACA70 (parent_id), INDEX short_id_idx (short_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment_vote (id INT UNSIGNED AUTO_INCREMENT NOT NULL, comment_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_7C262788F8697D13 (comment_id), INDEX IDX_7C262788A76ED395 (user_id), UNIQUE INDEX comment_vote_unique (comment_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invitation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, is_used TINYINT(1) NOT NULL, email VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE story (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, is_approved TINYINT(1) DEFAULT 0 NOT NULL, disapproved_reason VARCHAR(255) DEFAULT NULL, is_deleted TINYINT(1) DEFAULT 0 NOT NULL, score INT DEFAULT 0 NOT NULL, short_id VARCHAR(10) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_EB5604382B36786B (title), UNIQUE INDEX UNIQ_EB560438F47645AE (url), UNIQUE INDEX UNIQ_EB560438F8496E51 (short_id), INDEX IDX_EB560438A76ED395 (user_id), INDEX short_id_idx (short_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE story_tag (story_id INT UNSIGNED NOT NULL, tag_id INT UNSIGNED NOT NULL, INDEX IDX_A74D17C9AA5D4036 (story_id), INDEX IDX_A74D17C9BAD26311 (tag_id), PRIMARY KEY(story_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE story_vote (id INT UNSIGNED AUTO_INCREMENT NOT NULL, story_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_CCBDE94AAA5D4036 (story_id), INDEX IDX_CCBDE94AA76ED395 (user_id), UNIQUE INDEX story_vote_unique (story_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_389B7835E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT UNSIGNED AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', karma INT DEFAULT 0 NOT NULL, is_banned TINYINT(1) DEFAULT 0 NOT NULL, banned_reason VARCHAR(255) DEFAULT NULL, force_relogin_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CAA5D4036 FOREIGN KEY (story_id) REFERENCES story (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C727ACA70 FOREIGN KEY (parent_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE comment_vote ADD CONSTRAINT FK_7C262788F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE comment_vote ADD CONSTRAINT FK_7C262788A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE story ADD CONSTRAINT FK_EB560438A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE story_tag ADD CONSTRAINT FK_A74D17C9AA5D4036 FOREIGN KEY (story_id) REFERENCES story (id)');
        $this->addSql('ALTER TABLE story_tag ADD CONSTRAINT FK_A74D17C9BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)');
        $this->addSql('ALTER TABLE story_vote ADD CONSTRAINT FK_CCBDE94AAA5D4036 FOREIGN KEY (story_id) REFERENCES story (id)');
        $this->addSql('ALTER TABLE story_vote ADD CONSTRAINT FK_CCBDE94AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C727ACA70');
        $this->addSql('ALTER TABLE comment_vote DROP FOREIGN KEY FK_7C262788F8697D13');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CAA5D4036');
        $this->addSql('ALTER TABLE story_tag DROP FOREIGN KEY FK_A74D17C9AA5D4036');
        $this->addSql('ALTER TABLE story_vote DROP FOREIGN KEY FK_CCBDE94AAA5D4036');
        $this->addSql('ALTER TABLE story_tag DROP FOREIGN KEY FK_A74D17C9BAD26311');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment_vote DROP FOREIGN KEY FK_7C262788A76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE story DROP FOREIGN KEY FK_EB560438A76ED395');
        $this->addSql('ALTER TABLE story_vote DROP FOREIGN KEY FK_CCBDE94AA76ED395');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE comment_vote');
        $this->addSql('DROP TABLE invitation');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE story');
        $this->addSql('DROP TABLE story_tag');
        $this->addSql('DROP TABLE story_vote');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE user');
    }
}
