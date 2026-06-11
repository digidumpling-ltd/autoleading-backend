<?php

use Stevebauman\Purify\Cache\CacheDefinitionCache;
use Webkul\Core\Purifier\Definitions\ExtendedHtml5Definition;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Config
    |--------------------------------------------------------------------------
    |
    | This option defines the default config that is provided to HTMLPurifier.
    |
    */

    'default' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Config sets
    |--------------------------------------------------------------------------
    |
    | Here you may configure various sets of configuration for differentiated use of HTMLPurifier.
    | A specific set of configuration can be applied by calling the "config($name)" method on
    | a Purify instance. Feel free to add/remove/customize these attributes as you wish.
    |
    | Documentation: http://htmlpurifier.org/live/configdoc/plain.html
    |
    |   Core.Encoding               The encoding to convert input to.
    |   HTML.Doctype                Doctype to use during filtering.
    |   HTML.Allowed                The allowed HTML Elements with their allowed attributes.
    |   HTML.ForbiddenElements      The forbidden HTML elements. Elements that are listed in this
    |                               string will be removed, however their content will remain.
    |   CSS.AllowedProperties       The Allowed CSS properties.
    |   AutoFormat.AutoParagraph    Newlines are converted in to paragraphs whenever possible.
    |   AutoFormat.RemoveEmpty      Remove empty elements that contribute no semantic information to the document.
    |
    */

    'configs' => [

        'default' => [
            'Core.Encoding' => 'utf-8',
            'HTML.Doctype' => 'HTML 4.01 Transitional',
            'HTML.Allowed' => 'h1[style|class],h2[style|class],h3[style|class],h4[style|class],h5[style|class],h6[style|class],b[class],u[class],strong[class],i[class],em[class],s[class],del[class],small[style|class],a[href|title|target|rel|style|class],ul[class],ol[class],li[class],p[style|class],br,span[style|class],img[width|height|alt|src|style|class],blockquote[class],table[style|cellpadding|cellspacing|width|class],thead[class],tbody[class],tfoot[class],tr[style|class],th[style|colspan|rowspan|scope|class],td[style|colspan|rowspan|class],div[style|class],hr,section[style|class]',
            'HTML.ForbiddenElements' => '',
            'CSS.AllowTricky' => true,
            'CSS.Proprietary' => true,
            'CSS.AllowedProperties' => 'font,font-size,font-weight,font-style,font-family,text-decoration,color,background-color,text-align,margin,margin-top,margin-bottom,margin-left,margin-right,padding,padding-top,padding-bottom,padding-left,padding-right,border,border-top,border-bottom,border-left,border-right,border-collapse,border-color,border-radius,width,height,max-width,min-width,display,vertical-align,line-height,white-space',
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | HTMLPurifier definitions
    |--------------------------------------------------------------------------
    |
    | Here you may specify a class that augments the HTML definitions used by
    | HTMLPurifier. Additional HTML5 definitions are provided out of the box.
    | When specifying a custom class, make sure it implements the interface:
    |
    |   \Stevebauman\Purify\Definitions\Definition
    |
    | Note that these definitions are applied to every Purifier instance.
    |
    | Documentation: http://htmlpurifier.org/docs/enduser-customize.html
    |
    */

    'definitions' => ExtendedHtml5Definition::class,

    /*
    |--------------------------------------------------------------------------
    | HTMLPurifier CSS definitions
    |--------------------------------------------------------------------------
    |
    | Here you may specify a class that augments the CSS definitions used by
    | HTMLPurifier. When specifying a custom class, make sure it implements
    | the interface:
    |
    |   \Stevebauman\Purify\Definitions\CssDefinition
    |
    | Note that these definitions are applied to every Purifier instance.
    |
    | CSS should be extending $definition->info['css-attribute'] = values
    | See HTMLPurifier_CSSDefinition for further explanation
    |
    */

    'css-definitions' => null,

    /*
    |--------------------------------------------------------------------------
    | Serializer
    |--------------------------------------------------------------------------
    |
    | The storage implementation where HTMLPurifier can store its serializer files.
    | If the filesystem cache is in use, the path must be writable through the
    | storage disk by the web server, otherwise an exception will be thrown.
    |
    */

    'serializer' => [
        'driver' => env('CACHE_STORE', env('CACHE_DRIVER', 'file')),
        'cache' => CacheDefinitionCache::class,
    ],

    // 'serializer' => [
    //    'disk' => env('FILESYSTEM_DISK', 'local'),
    //    'path' => 'purify',
    //    'cache' => \Stevebauman\Purify\Cache\FilesystemDefinitionCache::class,
    // ],

];
