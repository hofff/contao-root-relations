<?php

declare(strict_types=1);

namespace Hofff\Contao\RootRelations;

use Contao\Database;
use Contao\DataContainer;
use Contao\PageModel;

class PageDCA
{
    /** @var bool[] */
    private array $typeChanged;

    public function __construct()
    {
        $this->typeChanged = [];
    }

    /**
     * @param numeric-string|int $pageId
     * @param mixed[]            $set
     */
    public function oncreatePage(string $table, $pageId, array $set): void
    {
        if ($table !== 'tl_page' || ! isset($set['type'])) {
            return;
        }

        // TODO better?
        // RootRelations::updatePageRoots($id);

        if ($set['type'] === 'root') {
            $root = $pageId;
        } elseif ($set['pid']) {
            $parent = PageModel::findWithDetails($set['pid']);
            if ($parent === null) {
                return;
            }

            $root = $parent->type === 'root' ? $parent->id : $parent->rootId;
        } else {
            return;
        }

        $sql = 'UPDATE tl_page SET hofff_root_page_id = ? WHERE id = ?';
        Database::getInstance()->prepare($sql)->execute((int) $root, $pageId);
    }

    public function onsubmitPage(DataContainer $dataContainer): void
    {
        if (! isset($this->typeChanged[$dataContainer->id])) {
            return;
        }

        RootRelations::updatePageRoots((int) $dataContainer->id);
    }

    public function saveType(string $value, DataContainer $dataContainer): string
    {
        if ($value === 'root' xor ($dataContainer->activeRecord && $dataContainer->activeRecord->type === 'root')) {
            $this->typeChanged[$dataContainer->id] = true;
        }

        return $value;
    }

    /** @param numeric-string|int $pageId */
    public function oncopyPage($pageId): void
    {
        RootRelations::updatePageRoots((int) $pageId);
    }

    public function oncutPage(DataContainer $dataContainer): void
    {
        RootRelations::updatePageRoots((int) $dataContainer->id);
    }

    /** @param numeric-string|int $pageId */
    public function onrestorePage($pageId): void
    {
        RootRelations::updatePageRoots((int) $pageId);
    }
}
