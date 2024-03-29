<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Kirby\Cms\Page;
use PHPUnit\Framework\TestCase;

class CreatePagesForNextRunTest extends TestCase
{
    /**
     * @var int
     */
    private $depth;

    public function createPage($parent, int $idx, int $depth = 3): Page
    {
        $id = 'Test ' . abs(crc32(microtime() . $idx . $depth));
        /* @var $page Page */
        kirby()->impersonate('kirby');
        $page = $parent->createChild([
            'slug' => Str::slug($id),
            'template' => 'default',
            'content' => [
                'title' => $id,
            ],
        ]);

        $page = $page->changeStatus($idx + $depth % 2 > 0 ? 'listed' : 'unlisted');
        if ($depth > 0) {
            $depth--;
            for ($i = 0; $i < $this->depth; $i++) {
                $this->createPage($page, $i, $depth);
            }
        }

        return $page;
    }

    public function tearDownPages(): void
    {
        kirby()->impersonate('kirby');
        /* @var $page Page */
        foreach (site()->pages()->index()->notTemplate('home') as $page) {
            $page->delete(true);
        }
    }

    /**
     * CreatePagesForNextRun
     * 
     * @group CreatePagesForNextRun
     */
    public function testSetUpPages(): void
    {
        $this->depth = 2;

        if (site()->pages()->children()->notTemplate('home')->count() === 0) {
            for ($i = 0; $i < $this->depth; $i++) {
                $this->createPage(site(), $i, $this->depth);
            }
        }
        $this->assertTrue(site()->pages()->children()->notTemplate('home')->count() > 0);
    }
}
