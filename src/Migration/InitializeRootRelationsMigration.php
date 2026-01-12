<?php

declare(strict_types=1);

namespace Hofff\Contao\RootRelations\Migration;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Hofff\Contao\RootRelations\RootRelations;
use Override;

final class InitializeRootRelationsMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $connection, private readonly ContaoFramework $framework)
    {
    }

    #[Override]
    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->getSchemaManager();

        if (! $schemaManager->tablesExist('tl_page')) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_page');

        return ! isset($columns['hofff_root_page_id']);
    }

    #[Override]
    public function run(): MigrationResult
    {
        $this->framework->initialize();

        $this->connection->executeStatement(
            'ALTER TABLE tl_page ADD hofff_root_page_id int(10) unsigned NOT NULL default \'0\'',
        );

        RootRelations::updatePageRoots();

        return $this->createResult(true);
    }
}
