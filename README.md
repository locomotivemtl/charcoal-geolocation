Charcoal Geolocation
===============

[![License][badge-license]][charcoal-geolocation]
[![Latest Stable Version][badge-version]][charcoal-geolocation]
[![Code Quality][badge-scrutinizer]][dev-scrutinizer]
[![Coverage Status][badge-coveralls]][dev-coveralls]
[![Build Status][badge-travis]][dev-travis]

A [Charcoal][charcoal-app] service provider my cool feature.



## Table of Contents

-   [Installation](#installation)
    -   [Dependencies](#dependencies)
-   [Configuration](#configuration)
-   [Usage](#usage)
-   [Development](#development)
    -  [API Documentation](#api-documentation)
    -  [Development Dependencies](#development-dependencies)
    -  [Coding Style](#coding-style)
-   [Credits](#credits)
-   [License](#license)



## Installation

The preferred (and only supported) method is with Composer:

```shell
$ composer require locomotivemtl/charcoal-geolocation
```

### Dependencies

-   [charcoal-core](https://github.com/locomotivemtl/charcoal-core) 
-   [charcoal-admin](https://github.com/locomotivemtl/charcoal-admin)


#### Required

-   [**PHP 5.6+**](https://php.net): _PHP 7_ is recommended.


## Configuration

Add the _views_ and the _metadata_ paths to the config file.

```json
"metadata": {
       "paths": [
           "...",
           "vendor/locomotivemtl/charcoal-geolocation/metadata/"
       ]
   },
   "view": {
       "paths": [
           "...",
           "vendor/locomotivemtl/charcoal-geolocation/views/"
       ]
   }
```


## Usage

The module is basically a collection of properties, inputs and widgets.
All you have to do is use them.

-   GeolocationCollection widget implementation example for an object's dashboard :
    
```json
"map": {
    "type": "charcoal/geolocation/widget/geolocation-collection",
    "geometry_property": "geometry",
    "infobox_template": "project/widget/example-infobox",
    "priority": 10
}
```

To use the properties provided by this Module, you have to refer to them with their full PHP path from the object metadata : 
`Charcoal\\Geolocation\\Property\\MapStructureProperty`
`Charcoal\\Geolocation\\Property\\PointProperty`
`Charcoal\\Geolocation\\Property\\PolylineProperty`
`Charcoal\\Geolocation\\Property\\PolygonProperty`



## Widgets

The following base widgets are available to build the various _admin_ templates:

-   `GeolocationCollection`
    -   Widget That can show a collection of object that has a property that implements _GeolocationInterface_
    -   Specify the property ident to use
    -   Specify a template to show the 
    
## Property Inputs

The following property inputs are available to build forms in the _admin_ module:

-	`MapStructure`
	-	A specialized widget to add and edit `geolocation data` on a map.
	    -   Supported geolocation types:
	        -   Point
	        -   Polyline
	        -   Polygon
    -   Cannot be _multiple_
    -	Requires google-map.
    -   Requires [gmap](http://beneroch.com/gmap).
    -   Extends StructureProperty from [charcoal-property][charcoal-property].
    -   Implements __GeolocationInterface__.

-   `AbstractGeolocation`
    -   Base property for: 
        -   `Point`
        -   `Polyline`
        -   `Polygon`
    -   Extends AbstractProperty from [charcoal-property][charcoal-property].
    -   Implements __GeolocationInterface__.

-	`Point`
    -	A specialized widget to add and edit `Point` geolocation data on a map.
    -	Requires google-map.
    -   Requires [gmap](http://beneroch.com/gmap).
        
-	`Polyline`
    -	A specialized widget to add and edit `Polyline` geolocation data on a map.
    -	Requires google-map.
    -   Requires [gmap](http://beneroch.com/gmap).

-	`Polygon`
    -	A specialized widget to add and edit `Polygon` geolocation data on a map.
    -	Requires google-map.
    -   Requires [gmap](http://beneroch.com/gmap).

## Development

To install the development environment:

```shell
$ composer install
```

To run the scripts (phplint, phpcs, and phpunit):

```shell
$ composer test
```



### API Documentation

-   The auto-generated `phpDocumentor` API documentation is available at:  
    [https://locomotivemtl.github.io/charcoal-geolocation/docs/master/](https://locomotivemtl.github.io/charcoal-geolocation/docs/master/)
-   The auto-generated `apigen` API documentation is available at:  
    [https://codedoc.pub/locomotivemtl/charcoal-geolocation/master/](https://codedoc.pub/locomotivemtl/charcoal-geolocation/master/index.html)


### Development Dependencies

-   [php-coveralls/php-coveralls][phpcov]
-   [phpunit/phpunit][phpunit]
-   [squizlabs/php_codesniffer][phpcs]
-   [seld/jsonlint][jsonlint]


### Coding Style

The charcoal-geolocation module follows the Charcoal coding-style:

-   [_PSR-1_][psr-1]
-   [_PSR-2_][psr-2]
-   [_PSR-4_][psr-4], autoloading is therefore provided by _Composer_.
-   [_phpDocumentor_](http://phpdoc.org/) comments.
-   [phpcs.xml.dist](phpcs.xml.dist) and [.editorconfig](.editorconfig) for coding standards.

> Coding style validation / enforcement can be performed with `composer phpcs`. An auto-fixer is also available with `composer phpcbf`.



## Credits

-   [Locomotive](https://locomotive.ca/)
-	Joel Alphonso <joel@locomotive.ca>

## License

Charcoal is licensed under the MIT license. See [LICENSE](LICENSE) for details.



[charcoal-geolocation]:  https://packagist.org/packages/locomotivemtl/charcoal-geolocation
[charcoal-property]:     https://packagist.org/packages/locomotivemtl/charcoal-property
[charcoal-app]:          https://packagist.org/packages/locomotivemtl/charcoal-app

[dev-scrutinizer]:    https://scrutinizer-ci.com/g/locomotivemtl/charcoal-geolocation/
[dev-coveralls]:      https://coveralls.io/r/locomotivemtl/charcoal-geolocation
[dev-travis]:         https://travis-ci.org/locomotivemtl/charcoal-geolocation

[badge-license]:      https://img.shields.io/packagist/l/locomotivemtl/charcoal-geolocation.svg?style=flat-square
[badge-version]:      https://img.shields.io/packagist/v/locomotivemtl/charcoal-geolocation.svg?style=flat-square
[badge-scrutinizer]:  https://img.shields.io/scrutinizer/g/locomotivemtl/charcoal-geolocation.svg?style=flat-square
[badge-coveralls]:    https://img.shields.io/coveralls/locomotivemtl/charcoal-geolocation.svg?style=flat-square
[badge-travis]:       https://img.shields.io/travis/locomotivemtl/charcoal-geolocation.svg?style=flat-square

[psr-1]:  https://www.php-fig.org/psr/psr-1/
[psr-2]:  https://www.php-fig.org/psr/psr-2/
[psr-3]:  https://www.php-fig.org/psr/psr-3/
[psr-4]:  https://www.php-fig.org/psr/psr-4/
[psr-6]:  https://www.php-fig.org/psr/psr-6/
[psr-7]:  https://www.php-fig.org/psr/psr-7/
[psr-11]: https://www.php-fig.org/psr/psr-11/
