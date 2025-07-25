<?php
namespace Services;

use Omeka\Module\AbstractModule;
use Laminas\EventManager\Event;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Renderer\PhpRenderer;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include sprintf('%s/config/module.config.php', __DIR__);
    }

    public function install(ServiceLocatorInterface $services)
    {
        $sql = <<<'SQL'
CREATE TABLE services_transcription_project (id INT UNSIGNED AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, prepare_job_id INT DEFAULT NULL, transcribe_job_id INT DEFAULT NULL, fetch_job_id INT DEFAULT NULL, `label` VARCHAR(255) NOT NULL, model_id VARCHAR(255) NOT NULL, access_token VARCHAR(255) NOT NULL, query LONGTEXT DEFAULT NULL, created DATETIME NOT NULL, modified DATETIME DEFAULT NULL, INDEX IDX_346F4EE97E3C61F9 (owner_id), INDEX IDX_346F4EE9B86B1630 (prepare_job_id), INDEX IDX_346F4EE96C79D14A (transcribe_job_id), INDEX IDX_346F4EE96BC46024 (fetch_job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
CREATE TABLE services_transcription_page (id INT UNSIGNED AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, media_id INT NOT NULL, storage_id VARCHAR(190) DEFAULT NULL, position INT DEFAULT NULL, created DATETIME NOT NULL, modified DATETIME DEFAULT NULL, INDEX IDX_17DC2F8A126F525E (item_id), INDEX IDX_17DC2F8AEA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
ALTER TABLE services_transcription_project ADD CONSTRAINT FK_346F4EE97E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE SET NULL;
ALTER TABLE services_transcription_project ADD CONSTRAINT FK_346F4EE9B86B1630 FOREIGN KEY (prepare_job_id) REFERENCES job (id) ON DELETE SET NULL;
ALTER TABLE services_transcription_project ADD CONSTRAINT FK_346F4EE96C79D14A FOREIGN KEY (transcribe_job_id) REFERENCES job (id) ON DELETE SET NULL;
ALTER TABLE services_transcription_project ADD CONSTRAINT FK_346F4EE96BC46024 FOREIGN KEY (fetch_job_id) REFERENCES job (id) ON DELETE SET NULL;
ALTER TABLE services_transcription_page ADD CONSTRAINT FK_17DC2F8A126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE;
ALTER TABLE services_transcription_page ADD CONSTRAINT FK_17DC2F8AEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE;
SQL;
        $conn = $services->get('Omeka\Connection');
        $conn->exec('SET FOREIGN_KEY_CHECKS=0;');
        $conn->exec($sql);
        $conn->exec('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function uninstall(ServiceLocatorInterface $services)
    {
        $conn = $services->get('Omeka\Connection');
        $conn->exec('SET FOREIGN_KEY_CHECKS=0;');
        $conn->exec('DROP TABLE IF EXISTS services_transcription_project;');
        $conn->exec('DROP TABLE IF EXISTS services_transcription_page;');
        $conn->exec('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function getConfigForm(PhpRenderer $view)
    {
    }

    public function handleConfigForm(AbstractController $controller)
    {
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
    }
}
