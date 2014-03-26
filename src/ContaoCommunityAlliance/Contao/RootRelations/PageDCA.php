<?php

namespace ContaoCommunityAlliance\Contao\RootRelations;

class PageDCA {

	public function oncreatePage($table, $id, $set, $dc) {
		if($table != 'tl_page') {
			return;
		}

		if($set['type'] == 'root') {
			$root = $id;

		} elseif($set['pid']) {
			$parent = ControllerProxy::getPageDetails($set['pid']);
			$root = $parent->type == 'root' ? $parent->id : $parent->rootId;
		}

		$sql = 'UPDATE tl_page SET cca_rr_root = ? WHERE id = ?';
		\Database::getInstance()->prepare($sql)->executeUncached(intval($root), $id);
	}

	public function oncopyPage($id) {
		RootRelations::updatePageRoots($id);
	}

	public function oncutPage($dc) {
		RootRelations::updatePageRoots($dc->id);
	}

	public function onrestorePage($id) {
		RootRelations::updatePageRoots($id);
	}

}
