<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Bnomei\Bolt;
use Kirby\Cms\Page;
use PHPUnit\Framework\TestCase;

class BoltTest extends TestCase
{
    public function randomPage(): ?Page
    {
        return site()->pages()->index()->notTemplate('home')->shuffle()->first();
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
