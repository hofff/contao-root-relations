<?php

namespace Hofff\Contao\RootRelations;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class PageDCA {

	/**
	 * @var array
	 */
	private $typeChanged;

	/**
	 */
	public function __construct() {
		$this->typeChanged = [];
	}

	/**
	 * @param string $table
	 * @param integer $id
	 * @param array $set
	 * @param \DataContainer $dc
	 */
	public function oncreatePage($table, $id, $set, $dc) {
		if($table != 'tl_page') {
			return;
		}

		// TODO better?
// 		RootRelations::updatePageRoots($id);

		if($set['type'] == 'root') {
			$root = $id;

		} elseif($set['pid']) {
			$parent = \PageModel::findWithDetails($set['pid']);
			$root = $parent->type == 'root' ? $parent->id : $parent->rootId;
		}

		$sql = 'UPDATE tl_page SET hofff_root_page_id = ? WHERE id = ?';
		\Database::getInstance()->prepare($sql)->executeUncached(intval($root), $id);
	}

	/**
	 * @param \DataContainer $dc
	 * @return void
	 */
	public function onsubmitPage($dc) {
		if(isset($this->typeChanged[$dc->id])) {
			RootRelations::updatePageRoots($dc->id);
		}
	}

	/**
	 * @param string $value
	 * @param \DataContainer $dc
	 * @return string
	 */
	public function saveType($value, $dc) {
		if($value == 'root' xor $dc->activeRecord->type == 'root') {
			$this->typeChanged[$dc->id] = true;
		}
		return $value;
	}

	/**
	 * @param integer $id
	 * @return void
	 */
	public function oncopyPage($id) {
		RootRelations::updatePageRoots($id);
	}

	/**
	 * @param integer $id
	 * @return void
	 */
	public function oncutPage($dc) {
		RootRelations::updatePageRoots($dc->id);
	}

	/**
	 * @param integer $id
	 * @return void
	 */
	public function onrestorePage($id) {
		RootRelations::updatePageRoots($id);
	}

}
