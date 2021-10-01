# Kirby 3 Bolt

![Release](https://flat.badgen.net/packagist/v/bnomei/kirby3-bolt?color=ae81ff)
[![Build Status](https://flat.badgen.net/travis/bnomei/kirby3-bolt)](https://travis-ci.com/bnomei/kirby3-bolt)
[![Coverage Status](https://flat.badgen.net/coveralls/c/github/bnomei/kirby3-bolt)](https://coveralls.io/github/bnomei/kirby3-bolt) 
[![Maintainability](https://flat.badgen.net/codeclimate/maintainability/bnomei/kirby3-bolt)](https://codeclimate.com/github/bnomei/kirby3-bolt)
[![Twitter](https://flat.badgen.net/badge/twitter/bnomei?color=66d9ef)](https://twitter.com/bnomei)

Kirby 3 Plugin for a fast Page lookup even in big content trees

## Commercial Usage

> <br>
><b>Support open source!</b><br><br>
> This plugin is free but if you use it in a commercial project please consider to sponsor me or make a donation.<br>
> If my work helped you to make some cash it seems fair to me that I might get a little reward as well, right?<br><br>
> Be kind. Share a little. Thanks.<br><br>
> &dash; Bruno<br>
> &nbsp; 

| M | O | N | E | Y |
|---|----|---|---|---|
| [Github sponsor](https://github.com/sponsors/bnomei) | [Patreon](https://patreon.com/bnomei) | [Buy Me a Coffee](https://buymeacoff.ee/bnomei) | [Paypal dontation](https://www.paypal.me/bnomei/15) | [Buy a Kirby license using this affiliate link](https://a.paddle.com/v2/click/1129/35731?link=1170) |

## Installation

- unzip [master.zip](https://github.com/bnomei/kirby3-bolt/archive/master.zip) as folder `site/plugins/kirby3-bolt` or
- `git submodule add https://github.com/bnomei/kirby3-bolt.git site/plugins/kirby3-bolt` or
- `composer require bnomei/kirby3-bolt`

## Why is Bolt faster and how much?

Because it does not scan each directory and file but skips as many of them as possible. Once you use the Page-Object in your code Kirby will lazily load uninitalized properties.

How much is gained depends on how many have been skipped. You can in average expect it to be n-times faster by the average folder count in your content tree. Example: 1000 pages in 10 folders 3 levels deep: 10\*10\*10. If you need a page from the third level Kirby would have to create a page index of 10+10+10=30 pages but Bolt will create only 3.

## Usage
```php
// lets assume 1000 pages: 10*10*10
$id = 'this-page-has/ten-siblings/in-every-subfolder';
$page = page($id); // kirby core
$page = bolt($id); // ~10x faster lookup

// can lookup beginning at a certain page as well
$page = $somePage->bolt($idInTree);

// it's even faster when you look up based on a directory name
$page = bolt('1_this-page-has/5_ten-siblings/3_in-every-subfolder');
```


## Works great with

- [kirby3-autoid](https://github.com/bnomei/kirby3-autoid) where it speeds up the page lookup

## Alternative

- [kirby3-boost](https://github.com/bnomei/kirby3-boost) which is like Bolt + AutoID but with Caching 

## Related Plugins

- [kirby-resolve](https://github.com/lukaskleinschmidt/kirby-resolve) by Lukas Kleinschmidt

## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/bnomei/kirby3-bolt/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

It is discouraged to use this plugin in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.

## Credits

based on idea in
- [kirby-resolve](https://github.com/lukaskleinschmidt/kirby-resolve)
