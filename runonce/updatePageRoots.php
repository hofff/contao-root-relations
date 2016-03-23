<?php

if(!Database::getInstance()->fieldExists('hofff_root_page_id', 'tl_page')) {
	Database::getInstance()->query('ALTER TABLE tl_page ADD hofff_root_page_id int(10) unsigned NOT NULL default \'0\'');
}

Hofff\Contao\RootRelations\RootRelations::updatePageRoots();
