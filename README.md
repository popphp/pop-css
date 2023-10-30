pop-css
=======

[![Build Status](https://github.com/popphp/pop-css/workflows/phpunit/badge.svg)](https://github.com/popphp/pop-css/actions)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=pop-css)](http://cc.popphp.org/pop-css/)

[![Join the chat at https://popphp.slack.com](https://media.popphp.org/img/slack.svg)](https://popphp.slack.com)
[![Join the chat at https://discord.gg/D9JBxPa5](https://media.popphp.org/img/discord.svg)](https://discord.gg/D9JBxPa5)

* [Overview](#overview)
* [Install](#install)
* [Quickstart](#quickstart)
* [Selectors](#selectors)
* [Media Queries](#media-queries)
* [Comments](#comments)

Overview
--------
`pop-css` provides the ability to create new CSS files as well as parse existing ones.
There is support for a number of CSS-based features such as media queries, comments
and even minification.

`pop-css` is a component of the [Pop PHP Framework](http://www.popphp.org/).

[Top](#pop-css)

Install
-------

Install `pop-css` using Composer.

    composer require popphp/pop-css

Or, require it in your composer.json file

    "require": {
        "popphp/pop-css" : "^2.0.0"
    }

[Top](#pop-css)

Quickstart
----------

In creating CSS, you will use the main CSS object and create and add selector, media and comment objects
to it to build your CSS.

**Adding some selectors**

```php
use Pop\Css\Css;
use Pop\Css\Selector;

$css = new Css();

$html = new Selector('html');
$html->setProperties([
    'margin'           => 0,
    'padding'          => 0,
    'background-color' => '#fff',
    'font-family'      => 'Arial, sans-serif'
]);

$login = new Selector('#login');
$login->setProperty('margin', 0)
    ->setProperty('padding', 0);

$css->addSelectors([$html, $login]);

echo $css;
```

The above code will produce:

```css
html {
    margin: 0;
    padding: 0;
    background-color: #fff;
    font-family: Arial, sans-serif;
}

#login {
    margin: 0;
    padding: 0;
}
```

[Top](#pop-css)

Selectors
---------

[Top](#pop-css)

Media Queries
-------------

[Top](#pop-css)

Comments
--------

[Top](#pop-css)
