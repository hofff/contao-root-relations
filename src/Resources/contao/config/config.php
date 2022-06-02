<?php

declare(strict_types=1);

$GLOBALS['TL_PURGE']['custom']['hofff_root_relations']['callback'] = [
    Hofff\Contao\RootRelations\RootRelations::class,
    'callbackPurgeData',
];
