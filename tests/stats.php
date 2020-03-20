<?php
// 1) download https://github.com/getkirby/plainkit
// 2) download and install Bolt and/or Resolve plugin
// 2.1) https://github.com/bnomei/kirby3-bolt
// 2.2) https://github.com/lukaskleinschmidt/kirby-resolve
// 3) save this file as /stats.php
// 4) run in terminal `php stats.php`. repeat until pages exist. once again to generate json, again to measure (loop).

require __DIR__ . '/kirby/bootstrap.php';
$kirby = kirby();

function createPage($parent, int $idx, int $depth = 3, int $amount): Page
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
        for ($i = 0; $i < $amount; $i++) {
            createPage($page, $i, $depth, $amount);
        }
    }

    return $page;
}

$dataFile = __DIR__ . '/stats.json';
$randomIds = F::exists($dataFile) ? Data::read($dataFile) : null;
if (! $randomIds) {
    // create n folders of n childs or until CLI crashes with out of memory
    if (site()->index()->count() < 10000) {
        $n = 100;
        for ($i = 0; $i < 2; $i++) {
            createPage(site(), $i, 2, $n);
        }
        die; // repeat until amount reached
    }

    // select random 1% of pages
    // bolt and page will use id but resolve needs diruri
    $randomIds = site()->index()->shuffle()->limit(10)->toArray(static function ($page) {
        return (string) $page->diruri();
    });
    Data::write($dataFile, $randomIds);
    die; // site()->index() would mess up measurement
}

var_dump($randomIds);

if (function_exists('bolt')) {
    $kirby->site()->prune();
    $start = microtime(true);
    foreach ($randomIds as $id => $diruri) {
        bolt($id);
    }
    $end = microtime(true);
    dump(($end - $start) * 1000); // 5
}

if (function_exists('resolveDir')) {
    $kirby->site()->prune();
    $start = microtime(true);
    foreach ($randomIds as $id => $diruri) {
        resolveDir($diruri);
    }
    $end = microtime(true);
    dump(($end - $start) * 1000); // 1
}

if (true) {
    $kirby->site()->prune();
    $start = microtime(true);
    foreach ($randomIds as $id => $diruri) {
        page($id);
    }
    $end = microtime(true);
    dump(($end - $start) * 1000); // 50
}
