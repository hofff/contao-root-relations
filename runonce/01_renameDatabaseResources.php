<?php

$db = Database::getInstance();

if($db->tableExists('tl_page')) {
	if($db->fieldExists('cca_rr_root', 'tl_page')) {
		if($db->query('SHOW INDEX FROM tl_page WHERE Key_name = \'cca_rr_root\'')->numRows) {
			$db->query('ALTER TABLE tl_page DROP INDEX cca_rr_root');
		}
		$db->query('ALTER TABLE tl_page CHANGE cca_rr_root hofff_root_page_id int(10) unsigned NOT NULL default \'0\'');
	}
}
