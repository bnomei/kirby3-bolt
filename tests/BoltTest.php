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
        $this->depth = 5;

        if (site()->pages()->index()->notTemplate('home')->count() === 0) {
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

        var_dump($randomPage->diruri());
        $page = \bolt($randomPage->id());

        // bolt page is lazy loaded
        $this->assertNotEquals($randomPage, $page);

        $this->assertEquals($randomPage->id(), $page->id());
        $this->assertEquals($randomPage->num(), $page->num());
        $this->assertEquals($randomPage->url(), $page->url());
    }
}
