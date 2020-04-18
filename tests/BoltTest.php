<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Bnomei\Bolt;
use Kirby\Cms\Page;
use PHPUnit\Framework\TestCase;

class BoltTest extends TestCase
{
    /**
     * @var int
     */
    private $depth;

    public function setUp(): void
    {
        $this->setUpPages();
    }

    public function setUpPages(): void
    {
        $this->depth = 2;

        if (site()->pages()->children()->notTemplate('home')->count() === 0) {
            for ($i = 0; $i < $this->depth; $i++) {
                $this->createPage(site(), $i, $this->depth);
            }
        }
    }

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

    public function randomPage(): ?Page
    {
        return site()->pages()->index()->notTemplate('home')->shuffle()->first();
    }

    public function tearDownPages(): void
    {
        kirby()->impersonate('kirby');
        /* @var $page Page */
        foreach (site()->pages()->index()->notTemplate('home') as $page) {
            $page->delete(true);
        }
    }

    public function testConstruct()
    {
        $bolt = new Bnomei\Bolt();
        $this->assertInstanceOf(Bnomei\Bolt::class, $bolt);
    }

    public function testFind()
    {
        $randomPage = $this->randomPage();
        site()->prune();

        $page = \bolt($randomPage->id());

        // bolt page is lazy loaded
        $this->assertNotEquals($randomPage, $page);

        // test kirbys lazy loading
        $this->assertEquals($randomPage->id(), $page->id());
        $this->assertEquals($randomPage->num(), $page->num());
        $this->assertEquals($randomPage->url(), $page->url());
        $this->assertEquals($randomPage->title()->value(), $page->title()->value());
        $this->assertEquals($randomPage->diruri(), $page->diruri());
        if ($randomPage->parent()) {
            $this->assertEquals($randomPage->parent()->root(), $page->parent()->root());
            $this->assertEquals($randomPage->siblings()->count(), $page->siblings()->count());
            $this->assertEquals($randomPage->siblings()->first()->id(), $page->siblings()->first()->id());
        }
    }

    public function testShortcutRepeatedLookup()
    {
        $randomPage = $this->randomPage();
        site()->prune();

        $page = \bolt($randomPage->id());
        $page2 = \bolt($randomPage->id());
        $this->assertEquals($page, $page2);
    }

    public function testShortcutSilblingsLookup()
    {
        $randomPage = $this->randomPage();
        $randomSibl = $randomPage->siblings()->shuffle()->first();
        site()->prune();

        $page = \bolt($randomPage->id());
        $page2 = \bolt($randomSibl->id());
        $this->assertEquals($page->parent()->id(), $page2->parent()->id());
    }

    public function testPageMethod()
    {
        $randomPage = null;
        $parent = null;
        while (!$parent) {
            $randomPage = $this->randomPage();
            $parent = $randomPage->parent();
        }
        site()->prune();

        $page = $parent->bolt($randomPage->slug());

        $this->assertEquals($randomPage->id(), $page->id());
    }

    public function testFindByDiruri()
    {
        $randomPage = $this->randomPage();
        $randomPageDiruri = $randomPage->diruri();
        site()->prune();

        $page = \bolt($randomPageDiruri);

        $this->assertEquals($randomPageDiruri, $page->diruri());
        $this->assertEquals($randomPage->id(), $page->id());
    }
}
