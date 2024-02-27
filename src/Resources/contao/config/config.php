<?php

declare(strict_types=1);

use Hofff\Contao\RootRelations\RootRelations;

$GLOBALS['TL_PURGE']['custom']['hofff_root_relations']['callback'] = [RootRelations::class, 'callbackPurgeData'];
