<?php

declare(strict_types=1);

namespace Bnomei;

use Kirby\Cms\Page;
use Kirby\Cms\Dir;

final class Bolt
{
    /**
     * @var string
     */
    private $root;

    /**
     * @var string
     */
    private $extension;

    /**
     * @var array<string>
     */
    private $modelFiles;

    public function __construct(?string $root = null)
    {
        $kirby = kirby();
        $this->root = $root ?? $kirby->root('content');

        $this->extension = $kirby->contentExtension();
        if ($kirby->multilang()) {
            $this->extension = $kirby->defaultLanguage()->code() . '.' . $this->extension;
        }

        $extension = $this->extension;
        $this->modelFiles = array_map(static function ($value) use ($extension) {
            return $value . '.' . $extension;
        }, array_keys(Page::$models));
    }

    public function findByID(string $id): ?Page
    {
        $draft = false;
        $parent = null;
        $page = null;
        $parts = explode('/', $id);

        foreach ($parts as $part) {
            if ($part === '_drafts') {
                $draft = true;
                $this->root .= '/_drafts';
                continue;
            }
            $params = [
                'root' => null,
                'parent' => $parent,
                'slug' => $part,
                'num' => null,
                'model' => null,
            ];
            $directory = opendir($this->root);
            while ($file = readdir($directory)) {
                if ($file === "." || $file === "..") {
                    continue;
                }
                if ($file === $part) {
                    $params['root'] = $this->root . '/' . $file;
                } elseif (strpos($file, Dir::$numSeparator . $part) !== false) {
                    $params['root'] = $this->root . '/' . $file;
                    if (preg_match('/^([0-9]+)_(.*)$/', $part, $match)) {
                        $params['num'] = intval($match[1]);
                        $params['slug'] = $match[2];
                    }
                }
                if ($params['root']) {
                    foreach ($this->modelFiles as $modelFile) {
                        if (file_exists($params['root'] . '/' . $modelFile)) {
                            $params['model'] = str_replace('.' . $this->extension, '', $modelFile);
                            break;
                        }
                    }
                    break;
                }
            }
            closedir($directory);

            if (!$params['root']) {
                return null; // not found
            }
            if ($draft === true) {
                $params['isDraft'] = $draft;
                // Only direct subpages are marked as drafts
                $draft = false;
            }
            $page = Page::factory($params);
            $parent = $page;
            kirby()->extend([
                'pages' => [$id => $page]
            ]);
            $this->root = $params['root']; // loop
        }
        return $page;
    }

    public static function page(string $id, ?string $root = null): ?\Kirby\Cms\Page
    {
        return (new self($root))->findByID($id);
    }
}
