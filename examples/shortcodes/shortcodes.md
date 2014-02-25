The wordpress shortcode API provides a simple set of functions for creating macro codes for use in post content.

This file details how to create enclosing/self-enclosing shortcodes as well as detailing the builtin shortcodes 
that ship with wordpress. This includes:
[Enclosed shortcodes](#enclosing)
[Enclosed shortcodes with attributes](#enclosing-attr)
[Self enclosed shortcodes](#self-enclosing)
[Nested shortcodes](#nested)
[Wordpresses builtin shortcodes](#builtin)
 

<a name="enclosing"/>
##Enclosing shortcodes

this is a shortcode in the form `[foobar]`. We would create an enclosing shortcode by adding the following to our functions.php file:

```php
function foobar_shortcode( $atts )
{
    return 'result of foobar shortcode';
}
add_shortcode( 'foobar', 'foobar_shortcode');
// this will create [foobar] shortcode that will render as 'result of foobar shortcode'
```

<a name="enclosing-attr"/>
##Enclosing shortcodes with attributes

Shortcodes can have attributes. An enclosing shortcode with attributes is in the form `[foobar name="baz"]`
An associative array is passed as the first arg to the function bound to the shortcut 

```php
function foobar_shortcode( $atts )
{
    extract( $atts );
    return "name = {$name}";
}
add_shortcode( 'foobar', 'foobar_shortcode');
// creates a foobar shortcode which takes a name attribute. [foobar name="baz"] returns "name = baz".
```


###Enclosing shortcodes with attributes and attribute defaults

If you want to have a shortcode with fallback default values for attributes you can use the `shortcode_atts` wp function

```php
function foobar_shortcode( $atts )
{
    extract( shortcode_atts( array('name' => 'baz', 'height' => 'tall') ), $atts );
    return "name is {$name} and height is {$height}";
}
add_shortcode( 'foobar', 'foobar_shortcode');
// [foobar height="small"] returns "name is baz and height is small"
```


<a name="self-enclosing"/>
##Self enclosing shortcodes

Self enclosing shortcodes are for when you want to enclose content with a shortcode. Self enclosing shortcodes are in the form `[foo]baz[/foo]`

```php
function foobar_shortcode( $atts, $content = null )
{
    return '<h1>' . $content . '</h1>';
}
add_shortcode( 'foobar', 'foobar_shortcode');
// [foo]baz[/foo] returns '<h1>baz</h1>'
```

<a name="nested"/>
##Nesting shortcodes in self enclosed short codes

A shortcode may be nested in a self-enclosed short code by making use of the `do_shortcode` function which will parse the shortcode from the
enclosed content correctly

```php
function foobar_shortcode( $atts, $content = null )
{
    return '<h1>' . do_shortcode($content) . '</h1>';
}

function baz_shortcode( $atts )
{
    return 'Hello world!';
}
add_shortcode( 'foobar', 'foobar_shortcode');
add_shortcode( 'baz', 'baz_shortcode');
// [foobar] Hello world from baz shortcode: [baz] [/foobar] returns '<h1>Hello world from baz shortcode: Hello world!</h1>' 
```
<a name="builtin"/>
##Wordpress builtin shortcodes

Wordpress ships with some handy shortcodes. See http://en.support.wordpress.com/shortcodes/


