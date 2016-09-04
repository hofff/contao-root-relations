<?php

namespace Hofff\Contao\RootRelations;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class RootRelations {

	/**
	 * Updates the direct root references for each given page and their
	 * descendants or, if no pages are given, the update is done for the whole
	 * page tree.
	 *
	 * @param null|integer|array<integer> $pages
	 * @return void
	 */
	public static function updatePageRoots($pages = null) {
		$db = \Database::getInstance();

		// $roots contains a map from page id to their respective root id

		if($pages === null) {
			$pids = [ 0 ];
			$roots = [ 0 => 0 ];

		} else {
			$roots = [];
			foreach((array) $pages as $id) if($page = \PageModel::findWithDetails($id)) {
				$pids[] = $page->id;
				$roots[$page->id] = $page->type == 'root' ? $page->id : intval($page->rootId);
			}
		}

		while($pids) {
			$sql = 'SELECT id, pid, type = \'root\' AS isRoot FROM tl_page WHERE pid IN (' . implode(',', $pids) . ')';
			$result = $db->query($sql);

			$pids = [];
			while($result->next()) if(!isset($roots[$result->id])) {
				$roots[$result->id] = $result->isRoot ? $result->id : $roots[$result->pid];
				$pids[] = $result->id;
			}
		}

		$update = [];

		unset($roots[0]);
		foreach($roots as $id => $root) {
			$update[$root][] = $id;
		}

		foreach($update as $root => $ids) {
			$sql = 'UPDATE tl_page SET hofff_root_page_id = ' . $root . ' WHERE id IN (' . implode(',', $ids) . ')';
			$db->query($sql);
		}
	}

	/**
	 * @return void
	 */
	public function callbackPurgeData() {
		self::updatePageRoots();
	}

}
