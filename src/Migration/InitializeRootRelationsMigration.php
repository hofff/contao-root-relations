<?php

declare(strict_types=1);

namespace Hofff\Contao\RootRelations\Migration;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Hofff\Contao\RootRelations\RootRelations;

final class InitializeRootRelationsMigration extends AbstractMigration
{
    private Connection $connection;

    private ContaoFramework $framework;

    public function __construct(Connection $connection, ContaoFramework $framework)
    {
        $this->connection = $connection;
        $this->framework  = $framework;
    }

    public function shouldRun(): bool
    {
        $columns = $this->connection->getSchemaManager()->listTableColumns('tl_page');

        return ! isset($columns['hofff_root_page_id']);
    }

    public function run(): MigrationResult
    {
        $this->framework->initialize();

        $this->connection->executeStatement(
            'ALTER TABLE tl_page ADD hofff_root_page_id int(10) unsigned NOT NULL default \'0\''
        );

        RootRelations::updatePageRoots();

        return $this->createResult(true);
    }
}
