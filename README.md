pop-css
=======

[![Build Status](https://github.com/popphp/pop-css/workflows/phpunit/badge.svg)](https://github.com/popphp/pop-css/actions)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=pop-css)](http://cc.popphp.org/pop-css/)

[![Join the chat at https://discord.gg/TZjgT74U7E](https://media.popphp.org/img/discord.svg)](https://discord.gg/TZjgT74U7E)

* [Overview](#overview)
* [Install](#install)
* [Quickstart](#quickstart)
* [Selectors](#selectors)
* [Media Queries](#media-queries)
* [Comments](#comments)
* [Colors](#colors)
* [Parse CSS](#parse-css)
* [Minify](#minify)

Overview
--------
`pop-css` provides the ability to create new CSS files as well as parse existing ones.
There is support for a number of CSS-based features such as media queries, comments
and even minification.

`pop-css` is a component of the [Pop PHP Framework](https://www.popphp.org/).

[Top](#pop-css)

Install
-------

Install `pop-css` using Composer.

    composer require popphp/pop-css

Or, require it in your composer.json file

    "require": {
        "popphp/pop-css" : "^3.0.0"
    }

`pop-css` requires PHP 8.4 or newer.

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

Selectors render in the order they were added, regardless of whether they're element, ID, or class
selectors — mixing selector types renders them in that same insertion order, not grouped by type.

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

**Multiple and compound selectors**

A selector's constructor accepts any valid CSS selector string as-is, including comma-grouped lists and
combinators. `isMultipleSelector()` and `hasDescendant()` let you inspect which kind of selector you're
holding:

```php
$grouped = new Selector('h1, h2, h3');
$grouped->isMultipleSelector(); // true

$descendant = new Selector('.login-footer > div');
$descendant->hasDescendant();   // true
```

**Setting properties three ways**

Besides `setProperty()`/`setProperties()`, a selector's properties are also reachable as magic properties or
via `ArrayAccess` — all three styles read and write the same underlying properties, so they're interchangeable:

```php
$box = new Selector('.box');

$box->setProperty('color', '#333');   // setProperty()
$box->padding = '10px';               // magic property
$box['margin'] = 0;                   // ArrayAccess

echo $box;
```

```css
.box {
    color: #333;
    padding: 10px;
    margin: 0;
}
```

`isset($box['color'])`/`isset($box->color)` and `unset($box['color'])`/`unset($box->color)` work the same way.

**Reading the margin/padding shorthand as longhand**

If a `margin` or `padding` shorthand property is set, the individual `margin-top`/`-right`/`-bottom`/`-left`
(and the `padding-*` equivalents) can be read back out even though they were never set explicitly — the
shorthand value is split according to CSS's normal 1/2/3/4-value rules:

```php
$box = new Selector('.box');
$box->setProperty('margin', '10px 20px 30px 40px');

echo $box['margin-top'];    // 10px
echo $box['margin-right'];  // 20px
echo $box['margin-bottom']; // 30px
echo $box['margin-left'];   // 40px
```

Setting an explicit `margin-top` (or any other longhand) takes precedence over the synthesized value.

**Reading, checking and removing properties**

```php
$box = new Selector('.box');
$box->setProperties(['color' => 'red', 'margin' => 0]);

$box->hasProperty('color');   // true
$box->getProperties();        // ['color' => 'red', 'margin' => 0]
$box->removeProperty('margin');
$box->getProperties();        // ['color' => 'red']
```

**Tab size**

Both `Selector` and `Media` accept an optional tab size (in spaces) as a constructor argument, controlling
the indentation used when rendering:

```php
$box = new Selector('div', 2);
$box->setProperty('color', 'red');
echo $box;
```

```css
div {
  color: red;
}
```

**Accessing and removing selectors on the main CSS object**

Once selectors have been added to the main `Css` object, they can be looked up, checked for, or removed by
name — and the `Css` object itself supports `ArrayAccess`, `Countable`, and iteration:

```php
$css = new Css();
$css->addSelector(new Selector('html'));

$css->hasSelector('html');        // true
$css->getSelector('html');        // Selector instance
$css->removeSelector('html');

// ArrayAccess - equivalent to addSelector(), naming the selector for you
$css['html'] = new Selector();
$css['html']->setProperty('margin', 0);
isset($css['html']);              // true
unset($css['html']);

// Countable / IteratorAggregate
count($css);
foreach ($css as $name => $selector) {
    // ...
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

#login {
    margin: 0;
    padding: 0;
    width: 50%;
}

p {
    margin: 0;
    padding: 0;
    width: 50%;
}

@media screen and (max-width: 480px) {
    #login {
        width: 75%;
    }
    
    p {
        width: 75%;
    }
}
```

**Conditions: `not` and `only`**

A media query's condition (`not` or `only`) can be set via `setCondition()`, or passed into the constructor:

```php
$media = new Media('screen');
$media->setCondition('not');

echo $media;
```

```css
@media not screen {
}
```

**Building a `Media` object in one call**

`Media`'s constructor accepts the type, a features array, the condition, and the tab size all at once, as an
alternative to building it up with separate `setFeature()`/`setCondition()` calls:

```php
$media = new Media('screen', ['max-width' => '480px'], 'only', 2);
```

**Reading and removing features**

```php
$media = new Media('screen');
$media->setFeatures(['max-width' => '480px', 'min-width' => '320px']);

$media->getFeatures();          // ['max-width' => '480px', 'min-width' => '320px']
$media->hasFeature('max-width'); // true
$media->getFeature('max-width'); // '480px'
```

**Accessing and removing media queries on the main CSS object**

```php
$css = new Css();
$css->addMedia(new Media('screen'))
    ->addMedia(new Media('print'));

$css->getAllMedia();   // array of both Media objects
$css->getMedia(0);     // the 'screen' Media object

$css->removeMedia(0);  // remove by index
$css->removeAllMedia();
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

Comments can be tailored with the `$wrap` and `$trailingNewLine` properties that can be passed into
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

**Reading and removing comments**

Comments can be added to a `Css`, `Media`, or `Selector` object (anything using `CommentTrait`), and read back
or removed the same way on all three:

```php
$css = new Css();
$css->addComment('First comment');
$css->addComment('Second comment');

$css->hasComments();      // true
$css->getComments();      // array of Comment objects

$css->removeComment(0);   // remove by index
$css->removeAllComments();
```

[Top](#pop-css)

Colors
------

Selector properties accept a `Pop\Color\Color` object anywhere they'd accept a string. The value is
normalized to a CSS-valid string at set time — regardless of which color space you build it from, including
formats (CMYK, Grayscale, HSV, HSB) that aren't natively CSS syntax on their own.

```php
use Pop\Css\Css;
use Pop\Css\Selector;
use Pop\Color\Color;

$box = new Selector('.box');
$box->setProperty('color', Color::rgb(255, 0, 0));
$box->setProperty('background-color', Color::cmyk(30, 20, 10, 5));
$box->setProperty('border-color', Color::oklch(0.7, 0.15, 30));

$css = new Css($box);
echo $css;
```

The above code will produce:

```css
.box {
    color: rgb(255, 0, 0);
    background-color: rgb(170, 194, 218);
    border-color: oklch(0.7 0.15 30);
}
```

Note that `background-color` rendered as `rgb(...)`, not raw CMYK numbers — CMYK (and Grayscale) aren't valid
CSS color syntax on their own, so they're automatically converted through RGB.

You can also read a property back out as a color object, whether the selector was built manually or produced
by parsing an existing CSS file:

```php
$color = $box->getColorProperty('color');
echo $color->toHex();
```

```text
#ff0000
```

`getColorProperty()` returns `null` if the property is missing, or if `Color::parse()` can't make sense of the
value at all. Note that `Color::parse()` has permissive fallback heuristics — any string containing exactly 3
spaces is parsed as CMYK, and any bare numeric string is parsed as Grayscale — so some ordinary non-color values
(a 4-value `margin`/`padding` shorthand, a bare numeric value like `line-height: 1.5`) get misidentified as
colors rather than returning `null`. That's `popphp/pop-color` behavior, not something `pop-css` filters.

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

In each case, it will return a CSS object populated with the related CSS objects from the content
of the source.

`parseFile()` throws a `Pop\Css\Exception` if the given file doesn't exist:

```php
use Pop\Css\Css;
use Pop\Css\Exception;

try {
    $css = Css::parseFile('path/to/missing.css');
} catch (Exception $e) {
    // file does not exist
}
```

Each static method has an equivalent instance method for parsing content into a `Css` object you already have:

```php
$css = new Css();
$css->parseCss($cssString);       // equivalent to Css::parseString()
$css->parseCssFile($cssFile);     // equivalent to Css::parseFile()
$css->parseCssUri($cssUri);       // equivalent to Css::parseUri()
```

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

