<?php

declare(strict_types=1);

namespace Hofff\Contao\RootRelations;

use Contao\PageModel;
use Contao\System;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use function implode;
use function intval;

class RootRelations
{
    /**
     * Updates the direct root references for each given page and their
     * descendants or, if no pages are given, the update is done for the whole
     * page tree.
     *
     * @param int|array<integer>|null $pages
     */
    public static function updatePageRoots($pages = null) : void
    {
        /** @var Connection $db */
        $db = System::getContainer()->get('database_connection');

        // $roots contains a map from page id to their respective root id
        if ($pages === null) {
            $pids  = [ 0 ];
            $roots = [ 0 => 0 ];
        } else {
            $roots = [];
            foreach ((array) $pages as $id) {
                $page = PageModel::findWithDetails($id);
                if (! $page) {
                    continue;
                }

                $pids[]           = $page->id;
                $roots[$page->id] = $page->type === 'root' ? $page->id : intval($page->rootId);
            }
        }

        while ($pids) {
            $result = $db->query('SELECT id, pid, type = \'root\' AS isRoot FROM tl_page WHERE pid IN (' .
                implode(',', $pids) . ')');
            $result->execute();
            $pids = [];
            while ($row = $result->fetch(FetchMode::STANDARD_OBJECT)) {
                if (isset($roots[$row->id])) {
                    continue;
                }

                $roots[$row->id] = $row->isRoot ? $row->id : $roots[$row->pid];
                $pids[]          = $row->id;
            }
        }

        $update = [];

        unset($roots[0]);
        foreach ($roots as $id => $root) {
            $update[$root][] = $id;
        }

        foreach ($update as $root => $ids) {
            $db->query('UPDATE tl_page SET hofff_root_page_id = ' . $root . ' WHERE id IN (' .
                implode(',', $ids) . ')');
        }
    }

    public function callbackPurgeData() : void
    {
        self::updatePageRoots();
    }
}
