<?php

if(!Database::getInstance()->fieldExists('cca_rr_root', 'tl_page')) {
	Database::getInstance()->query("ALTER TABLE tl_page ADD cca_rr_root int(10) unsigned NOT NULL default '0'");
}

ContaoCommunityAlliance\Contao\RootRelations\RootRelations::updatePageRoots();
