<?php

declare(strict_types=1);

use Contao\Database;
use Hofff\Contao\RootRelations\RootRelations;

$db = Database::getInstance();
if (! $db->fieldExists('hofff_root_page_id', 'tl_page')) {
    $db->query('ALTER TABLE tl_page ADD hofff_root_page_id int(10) unsigned NOT NULL default \'0\'');
}

RootRelations::updatePageRoots();
