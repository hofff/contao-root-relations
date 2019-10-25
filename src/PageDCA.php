<?php

declare(strict_types=1);

namespace Hofff\Contao\RootRelations;

use Contao\Database;
use Contao\DataContainer;
use Contao\PageModel;
use function intval;

class PageDCA
{
    /** @var bool[] */
    private $typeChanged;

    public function __construct()
    {
        $this->typeChanged = [];
    }

    /**
     * @param mixed[] $set
     */
    public function oncreatePage(string $table, int $id, array $set, DataContainer $dc) : void
    {
        if ($table !== 'tl_page') {
            return;
        }

        // TODO better?
//      RootRelations::updatePageRoots($id);

        if ($set['type'] === 'root') {
            $root = $id;
        } elseif ($set['pid']) {
            $parent = PageModel::findWithDetails($set['pid']);
            $root   = $parent->type === 'root' ? $parent->id : $parent->rootId;
        }

        $sql = 'UPDATE tl_page SET hofff_root_page_id = ? WHERE id = ?';
        Database::getInstance()->prepare($sql)->execute(intval($root), $id);
    }

    public function onsubmitPage(DataContainer $dc) : void
    {
        if (! isset($this->typeChanged[$dc->id])) {
            return;
        }

        RootRelations::updatePageRoots($dc->id);
    }

    public function saveType(string $value, DataContainer $dc) : string
    {
        if ($value === 'root' xor $dc->activeRecord->type === 'root') {
            $this->typeChanged[$dc->id] = true;
        }
        return $value;
    }

    public function oncopyPage(int $id) : void
    {
        RootRelations::updatePageRoots($id);
    }

    public function oncutPage(DataContainer $dc) : void
    {
        RootRelations::updatePageRoots($dc->id);
    }

    public function onrestorePage(int $id) : void
    {
        RootRelations::updatePageRoots($id);
    }
}
