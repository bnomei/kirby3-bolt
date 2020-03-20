# Kirby 3 Bolt

![Release](https://flat.badgen.net/packagist/v/bnomei/kirby3-bolt?color=ae81ff)
![Stars](https://flat.badgen.net/packagist/ghs/bnomei/kirby3-bolt?color=272822)
[![Build Status](https://flat.badgen.net/travis/bnomei/kirby3-bolt)](https://travis-ci.com/bnomei/kirby3-bolt)
[![Coverage Status](https://flat.badgen.net/coveralls/c/github/bnomei/kirby3-bolt)](https://coveralls.io/github/bnomei/kirby3-bolt) 
[![Maintainability](https://flat.badgen.net/codeclimate/maintainability/bnomei/kirby3-bolt)](https://codeclimate.com/github/bnomei/kirby3-bolt)  
[![Twitter](https://flat.badgen.net/badge/twitter/bnomei?color=66d9ef)](https://twitter.com/bnomei)

Kirby 3 Plugin for a fast Page lookup even in big content trees

## Commercial Usage

This plugin is free but if you use it in a commercial project please consider to
- [make a donation 🍻](https://www.paypal.me/bnomei/2) or
- [buy me ☕](https://buymeacoff.ee/bnomei) or
- [buy a Kirby license using this affiliate link](https://a.paddle.com/v2/click/1129/35731?link=1170)

## Installation

- unzip [master.zip](https://github.com/bnomei/kirby3-bolt/archive/master.zip) as folder `site/plugins/kirby3-bolt` or
- `git submodule add https://github.com/bnomei/kirby3-bolt.git site/plugins/kirby3-bolt` or
- `composer require bnomei/kirby3-bolt`

## Why is Bolt faster and how much?

Because it does not scan each directory and file but skips as many of them as possible. How much is gained depends on how many have been skipped.

## Usage
```php
$id = 'this-page-has/1000_siblings/in-every-subfolder';
$page = page($id); // kirby core
$page = bolt($id); // faster lookup

// can lookup beginning at a certain page as well
$page = $somePage->bolt($idInTree);
```

## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/bnomei/kirby3-bolt/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

It is discouraged to use this plugin in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.

## Credits

based on idea in
- https://github.com/lukaskleinschmidt/kirby-resolve
