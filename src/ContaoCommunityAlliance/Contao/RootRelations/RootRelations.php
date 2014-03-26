<?php

namespace ContaoCommunityAlliance\Contao\RootRelations;

class RootRelations {

	public static function updatePageRoots($pages = null, $orphans = true) {
		$db = \Database::getInstance();

		if($pages === null) {
			$sql = "SELECT id FROM tl_page WHERE type = 'root'";
			$roots = $db->query($sql)->fetchEach('id');
			$roots = array_combine($roots, $roots);

		} else {
			$pages = array_unique(array_map('intval', array_filter((array) $pages, 'is_numeric')));
			$roots = array();
			foreach($pages as $page) if($page = ControllerProxy::getPageDetails($page)) {
				$root = $page->type == 'root' ? $page->id : intval($page->rootId);
				$roots[$root][] = $page->id;
			}
		}

		foreach($roots as $root => $pages) {
			$descendants = ControllerProxy::getChildRecords($pages, 'tl_page');
			$descendants = array_merge($descendants, $pages);
			$descendants = implode(',', $descendants);
			$sql = "UPDATE tl_page SET cca_rr_root = ? WHERE id IN ($descendants)";
			$db->prepare($sql)->execute($root);
		}

		if(!$orphans) {
			return;
		}

		// retrieve all pages not within a root page
		$ids = array();
		$pids = array(0);
		do {
			$pids = implode(',', $pids);
			$sql = "SELECT id FROM tl_page WHERE pid IN ($pids) AND type != 'root'";
			$pids = $db->query($sql)->fetchEach('id');
			$ids[] = $pids;
		} while($pids);

		$ids = call_user_func_array('array_merge', $ids);

		if($ids) {
			$ids = implode(',', $ids);
			$sql = "UPDATE tl_page SET cca_rr_root = 0 WHERE id IN ($ids)";
			$db->query($sql);
		}
	}

}
