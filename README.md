pop-css
=======

[![Build Status](https://travis-ci.org/popphp/pop-css.svg?branch=master)](https://travis-ci.org/popphp/pop-css)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=pop-css)](http://cc.popphp.org/pop-css/)

OVERVIEW
--------
`pop-css` provides the ability to create new CSS files as well as parse existing ones.
There is support for media queries and comments as well.

`pop-css` is a component of the [Pop PHP Framework](http://www.popphp.org/).

INSTALL
-------

Install `pop-css` using Composer.

    composer require popphp/pop-css

BASIC USAGE
-----------

### Creating CSS

```php
use Pop\Css\Css;
use Pop\Css\Selector;

$css = new Css();

$html = new Selector('html');
$html->setProperties([
    'margin'  => 0,
    'padding' => 0,
    'background-color' => '#fff',
    'font-family' => 'Arial, sans-serif'
]);

$login = new Selector('#login');
$login->setProperty('margin', 0);
$login->setProperty('padding', 0);

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

### Parsing a CSS file

```php
use Pop\Css\Css;
$css = Css::parseFile('styles.css');
$login = $css->getSelector('#login');
echo $login['margin'];
```
