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
* [Parse CSS](#parse-css)
* [Minify](#minify)

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

Rendering the CSS can happen a number of ways:

**Call the render method**

```php
$cssString = $css->render();
```

**Call a string function**

```php
echo $css;
```

**Write to file**

```php
$css->writeToFile(__DIR__ . '/styles.css');
```

The main CSS object's constructor is also flexible and other CSS-related objects can be injected into it:

```php
use Pop\Css\Css;
use Pop\Css\Selector;

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

$css = new Css($html, $login);
```

[Top](#pop-css)

Selectors
---------

The selector object is the main object in which to define the various selectors and add them to the
main CSS object. The selector constructor accepts any valid CSS selector.

```php
use Pop\Css\Css;
use Pop\Css\Selector;

$css = new Css();

// Element selector
$html = new Selector('p');
$html->setProperties([
    'margin'      => 0,
    'padding'     => '3px',
    'color'       => '#555',
    'font-family' => 'Arial, sans-serif'
]);

// ID selector
$login = new Selector('#login');
$login->setProperty('margin', 0)
    ->setProperty('padding', 0);

// Class selector
$bold = new Selector('.bold');
$bold->setProperty('font-weight', 'bold');

$css->addSelectors([$html, $login, $bold]);

echo $css;
```

The above code will produce:

```css
p {
    margin: 0;
    padding: 3px;
    color: #555;
    font-family: Arial, sans-serif;
}

#login {
    margin: 0;
    padding: 0;
}

.bold {
    font-weight: bold;
}

```

[Top](#pop-css)

Media Queries
-------------

Media queries can be created as separate objects that contain their own selector objects.
They are then added to the main CSS object.

```php
use Pop\Css\Css;
use Pop\Css\Selector;
use Pop\Css\Media;

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

$p = new Selector('p');
$p->setProperty('margin', 0);
$p->setProperty('padding', 0);
$p->setProperty('width', '50%');

$media = new Media('screen');
$media->setFeature('max-width', '480px');
$media['#login'] = new Selector();
$media['#login']->setProperty('width', '75%');
$media['p'] = new Selector();
$media['p']->setProperty('width', '75%');

$css->addSelectors([$html, $login, $p])
    ->addMedia($media);

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

p {
    margin: 0;
    padding: 0;
    width: 50%;
}

#login {
    margin: 0;
    padding: 0;
    width: 50%;
}

@media screen and (max-width: 480px) {
    p {
        width: 75%;
    }
    
    #login {
        width: 75%;
    }
}
```

[Top](#pop-css)

Comments
--------

Comments can be added in a number of places. They can be added to the top of the main CSS content,
to the top of a selector or to the top of a media query.

```php
use Pop\Css\Css;
use Pop\Css\Selector;
use Pop\Css\Media;
use Pop\Css\Comment;

$css = new Css();
$css->addComment('This is a global comment');

$html = new Selector('html');
$html->setProperties([
    'margin'  => 0,
    'padding' => 0,
    'background-color' => '#fff',
    'font-family' => 'Arial, sans-serif'
]);

$p = new Selector('p');
$p->setProperty('margin', 0);
$p->setProperty('padding', 0);
$p->setProperty('width', '50%');
$p->addComment('This is a comment for the P selector');

$media = new Media('screen');
$media->setFeature('max-width', '480px');
$media['html'] = new Selector();
$media['html']->setProperty('padding', '1%');
$media['html']->addComment('This is a comment for the HTML selector in the media query');
$media->addComment('This is a comment for the media query');

$css->addSelectors([$html, $p])
    ->addMedia($media);

echo $css;
```

The above code will produce:

```css
/*
 * This is a global comment
 */
html {
    margin: 0;
    padding: 0;
    background-color: #fff;
    font-family: Arial, sans-serif;
}

/*
 * This is a comment for the P selector
 */
p {
    margin: 0;
    padding: 0;
    width: 50%;
}

/*
 * This is a comment for the media query
 */
@media screen and (max-width: 480px) {
    /*
     * This is a comment for the HTML selector in the media query
     */
    html {
        padding: 1%;
    }
}
```

Comments can be tailored the `$wrap` and `$trailingNewLine` properties that can be passed into
the comment constructor. If the `$wrap` is set to `0`, it will force a single-line comment.

```php
use Pop\Css\Css;
use Pop\Css\Selector;

$p = new Selector('p');
$p->setProperty('margin', 0);
$p->setProperty('padding', 0);
$p->setProperty('width', '50%');
$p->addComment('This is a comment for the P selector', 0, false);

$css = new Css($p);
echo $css;
```


The above code will produce:

```css
/* This is a comment for the P selector */
p {
    margin: 0;
    padding: 0;
    width: 50%;
}
```

[Top](#pop-css)

Parse CSS
---------

You can parse CSS with the following methods:

```php
use Pop\Css\Css;

$css = Css::parseFile('path/to/styles.css');

$css = Css::parseString($cssString);

$css = Css::parseUri('http://www.domain.com/css/styles.css');
```

In each case, it will return as CSS object populated with the related CSS objects from the content
of the CSS source.

[Top](#pop-css)

Minify
------

Setting the minify property on the main CSS object will perform a basic minification of the CSS content.

```php
use Pop\Css\Css;
use Pop\Css\Selector;
use Pop\Css\Media;

$css = new Css();
$css->addComment('This is a global comment');

$html = new Selector('html');
$html->setProperties([
    'margin'  => 0,
    'padding' => 0,
    'background-color' => '#fff',
    'font-family' => 'Arial, sans-serif'
]);

$p = new Selector('p');
$p->setProperty('margin', 0);
$p->setProperty('padding', 0);
$p->setProperty('width', '50%');

$media = new Media('screen');
$media->setFeature('max-width', '480px');
$media['html'] = new Selector();
$media['html']->setProperty('padding', '1%');

$css->addSelectors([$html, $p])
    ->addMedia($media);

$css->minify(true);

echo $css;
```

The above code will produce:

```css
html{margin:0;padding:0;background-color:#fff;font-family:Arial, sans-serif;}p{margin:0;padding:0;width:50%;} @media screen and (max-width: 480px) {html{padding:1%;}}
```

[Top](#pop-css)

