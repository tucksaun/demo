<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Synchronizer\SingleDatabaseSynchronizer;
use Doctrine\DBAL\Types\Type;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20181203085004 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $synchronizer = new SingleDatabaseSynchronizer($this->connection);
        $this->addSql($synchronizer->getUpdateSchema($this->getSchema()));
    }

    public function down(Schema $schema): void
    {
        $synchronizer = new SingleDatabaseSynchronizer($this->connection);
        $this->addSql($synchronizer->getDropSchema($this->getSchema()));
    }

    protected function getSchema(): Schema
    {
        $em = $this->container->get('doctrine')->getManager();
        $metadatas = $em->getMetadataFactory()->getAllMetadata();

        $st = new SchemaTool($em);

        $schema = $st->getSchemaFromMetadata($metadatas);
        $table = $schema->createTable('migration_versions');
        $table->addColumn('version', Type::STRING, ['length' => 255]);
        $table->setPrimaryKey(['version']);

        return $schema;
    }
}
