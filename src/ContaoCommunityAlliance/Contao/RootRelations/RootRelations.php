<?php

namespace ContaoCommunityAlliance\Contao\RootRelations;

class RootRelations {

	/**
	 * Updates the direct root references for each given page and their
	 * descendants or, if no pages are given, the update is done for the whole
	 * page tree.
	 *
	 * @param null|integer|array<integer> $pages
	 */
	public static function updatePageRoots($pages = null) {
		$db = \Database::getInstance();

		// $roots contains a map from page id to their respective root id

		if($pages === null) {
			$pids = array(0);
			$roots = array(0 => 0);

		} else {
			$roots = array();
			foreach((array) $pages as $id) if($page = ControllerProxy::getPageDetails($id)) {
				$pids[] = $page->id;
				$roots[$page->id] = $page->type == 'root' ? $page->id : intval($page->rootId);
			}
		}

		while($pids) {
			$sql = 'SELECT id, pid, type = \'root\' AS isRoot FROM tl_page WHERE pid IN (' . implode(',', $pids) . ')';
			$result = $db->query($sql);

			$pids = array();
			while($result->next()) if(!isset($roots[$result->id])) {
				$roots[$result->id] = $result->isRoot ? $result->id : $roots[$result->pid];
				$pids[] = $result->id;
			}
		}

		unset($roots[0]);
		foreach($roots as $id => $root) {
			$update[$root][] = $id;
		}

		if($update) foreach($update as $root => $ids) {
			$sql = 'UPDATE tl_page SET cca_rr_root = ' . $root . ' WHERE id IN (' . implode(',', $ids) . ')';
			$db->query($sql);
		}
	}

}
