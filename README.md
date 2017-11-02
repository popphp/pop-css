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

You can create CSS from scratch like this:

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

### Using Media Queries

You can also add media queries to your CSS as well:

```php
use Pop\Css\Css;
use Pop\Css\Media;
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
$login->setProperty('width', '50%');

$css->addSelectors([$html, $login]);

$media = new Media('screen');
$media->setFeature('max-width', '480px');
$media['#login'] = new Selector();
$media['#login']->setProperty('width', '75%');

$css->addMedia($media);

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
    width: 50%;
}

@media screen and (max-width: 480px) {
    #login {
        width: 75%;
    }
    
}
```

### Adding Comments

You can add comments to the css as well:

```php
use Pop\Css\Css;
use Pop\Css\Media;
use Pop\Css\Selector;
use Pop\Css\Comment;

$css = new Css();
$css->addComment(new Comment('This is a top level comment'));

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
$login->setProperty('width', '50%');
$login->addComment(new Comment('This is a comment for the #login selector'));

$css->addSelectors([$html, $login]);

$media = new Media('screen');
$media->setFeature('max-width', '480px');
$media['#login'] = new Selector();
$media['#login']->setProperty('width', '75%');
$media['#login']->addComment(
    new Comment('And this is a comment for the #login selector within the media query.')
);

$css->addMedia($media);

echo $css;
```

The above code will produce:

```css
/**
 * This is a top level comment
 */

html {
    margin: 0;
    padding: 0;
    background-color: #fff;
    font-family: Arial, sans-serif;
}

/**
 * This is a comment for the #login selector
 */

#login {
    margin: 0;
    padding: 0;
    width: 50%;
}

@media screen and (max-width: 480px) {
    /**
     * And this is a comment for the #login selector within the media query.
     */
    
    #login {
        width: 75%;
    }
    
}
```

### Minifying the CSS

Minify the CSS like this:

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
$login->setProperty('width', '50%');

$css->addSelectors([$html, $login]);
$css->minify();
echo $css;
```

Which produces:

```css
html{margin:0;padding:0;background-color:#fff;font-family:Arial, sans-serif;}
#login{margin:0;padding:0;width:50%;}
```

### Parsing a CSS file

```php
use Pop\Css\Css;
$css = Css::parseFile('styles.css');
$login = $css->getSelector('#login');
echo $login['margin'];
```
