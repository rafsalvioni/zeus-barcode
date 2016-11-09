# zeus-barcode

## Introduction
zeus-barcode is a API to create, draw and manage barcode data. Its has many barcode implementations and they could be extended to create
other barcodes.

Before use API, you should have in mind the context of API. The API tree are be structured on form below:

### Basic API tree
- **BarcodeInterface**: Identifies all barcodes objects;
  - **ChecksumInterface**: Implemented by barcodes that using checksum on its data;
  - **FixedLengthInterface**: Implemented by barcodes that have a fixed length. Otherwise, it is variable length;
  - **TwoWidthInterface**: Implemented by barcodes that uses only two widths of bars/spaces: wide and narrow;
  
The most part of these interfaces have been traits to implement some of its methods and abstract classes that implements these interfaces
and use these traits.

### Encoders
Barcodes can be encoded on different ways. Most barcodes are encoded using bars and spaces. Others uses the height of bars
(full and half bar, for example). Now a days, we have implemented two encoders, but, you can implement others implementing **EncoderInterface**.

### Renderers
Once a barcode is created, we can draw this barcode in different formats. A renderer is who do this. A renderer should be implement
the **RendererInterface**. Now a days, we have three renderers: Image (PNG), SVG and PDF.

## Default usage
To create a barcode, you have to instantiate the barcode format wanted and gave the data to be encoded. For example, we create a Codabar
barcode:

```php
require 'vendor/autoload.php';

use Zeus\Barcode\Codabar;
use Zeus\Barcode\Renderer\ImageRenderer;

$bc = new Codabar('A123456B');
$renderer = new ImageRenderer();
$bc->draw($renderer)->render();
```

The code above will create a Ean13 barcode with data "A123456B". This barcode will be drawed using ImageRenderer and the
render() method show the result to browser, using especific headers.

All barcodes will be check if the data given can be encoded for them. A barcode exception will be throwed on error.

## Calculating barcode checksums
Some barcodes needs checksum digits. On these cases, we have two situations:
- We have the full barcode data, with checksum digit. In this case, the barcode class will check the checksum digit given and throws a exception if this checking is false;
- We are creating a new barcode or we don't have full barcode data. In this case, we will inform to barcode class this situation and it will make the checksum digit for us.

For example, we will use the Ean13 format to create a barcode with and without checksum digit:
```php
require 'vendor/autoload.php';

use Zeus\Barcode\Upc\Ean13;

// We don't know the checksum digit, so, we gave false to second argument
$bc = new Ean13('123456789098', false);
echo $bc->getChecksum(); // Prints "2"

// Now we already know the checksum. Let's check it:
$bc = new Ean13('1234567890982', true);
echo $bc->getChecksum(); // Prints "2"

// Let's give a wrong checksum digit
try {
    $bc = new Ean13('1234567890983');
}
catch (\Zeus\Barcode\Exception $ex) {
    echo $ex->getMessage();
}
```

## Setting narrow and wide bar/space width
To barcodes thas implements TwoWidthInterface, we can set which width of wide and narrow bar/space. Let's see:

```php
require 'vendor/autoload.php';

use Zeus\Barcode\Code2of5\Interleaved;
use Zeus\Barcode\Renderer\ImageRenderer;

$renderer = new ImageRenderer();

// With default width
$bc1 = new Interleaved('5236589', false);
// Changing widths
$bc2 = clone $bc1;
$bc2->setNarrowWidth(2)->setWideWidth(6);

$renderer->stream($bc1)
         ->stream($bc2)
         ->render();
```

Both widths are in pixels.

## Formatting barcode's apresentation
We can parametrize how our barcode will be showed. There are many options for this. Let's see:
- "barWidth": Width of a bar or space, in renderer's unit. Default 1;
- "barHeight": Height of a bar, in renderer's unit. Default 50;
- "foreColor": Foreground color, as integer. Used to draw bars and text. Default 0x000000 (black);
- "backColor": Background color, as integer. Default 0xffffff (white). If negative, will be transparent;
- "border": Border width, in renderer's unit. Default 0 (no border);
- "showText": Boolean to inform if the text representation of barcode should be showed. Default true;
- "textAlign": Alignment of text. Possible values: "center", "left" and "right". Default "center";
- "textPosition": Position of text. Possible values: "top" and "bottom". Default "bottom";
- "font": Font to write text. Can be a single string or a file path to a font file. If empty, uses default font renderer. Default '';
- "fontSize": Size of font, in points (pt). Default 9;
- "quietZone": Espace that encapsulates the barcode, in renderer's unit. Help to barcode readers. The value defined will be used on left and right. Default 30;

Example:

```php
require 'vendor/autoload.php';

use Zeus\Barcode\Code2of5\Interleaved;
use Zeus\Barcode\Renderer\ImageRenderer;

$renderer = new ImageRenderer();

$bc = new Interleaved('5236589', false);
$bc->backColor = 0xffffaa;
$bc->foreColor = 0x0000ff;
$bc->barwidth  = 2;
$bc->barheight = 150;
$bc->fontsize  = 5;

$bc->draw($renderer)->render();
```

You can use too the scale() method on barcode. This method will resize all dimension options proportionallity.

## Managing renderers
We see above how to use renderers on basic way. However, renderers can be setted to do some boring tasks when we use barcodes.

### Styling renderer apresentation
As barcodes, renderers has some parameters to change how a barcode draw will be done. Are they:
- "offsetTop": Offset from top, in renderer's unit. Default 0;
- "offsetLeft": Offset from left, in renderer's unit. Default 0;
- "backColor": Background color, as integer, used on resize (see "merge" option). Default 0xffffff (white). Not used for FpdfRenderer;
- "merge": This is the best option... see below. Boolean value, default false;
- "type": Image type to render. Valid values are "png", "jpg", "jpeg" and "gif". Default "png". Supported only by ImageRenderer;

Using last example, we can do this:
```php
require 'vendor/autoload.php';

use Zeus\Barcode\Code2of5\Interleaved;
use Zeus\Barcode\Renderer\ImageRenderer;

$renderer = new ImageRenderer();

$bc = new Interleaved('5236589', false);
$bc->backColor = 0xffffaa;
$bc->foreColor = 0x0000ff;
$bc->barwidth  = 2;
$bc->barheight = 150;
$bc->fontsize  = 5;

$renderer->offsetTop = 50;
$renderer->offsetLeft = 50;
$renderer->backColor = 0xababab;

$bc->draw($renderer)->render();
```

### Using renderer's "merge" mode
By default, renderers draw one barcode at a time. If you draw a barcode and right after you draw another barcode, the resource of first draw will be lost.
However, if you set "merge" option to "true", renderer will merge all barcodes draw with him, returning the resource of this merge.
On wide/narrow example we can see this when we use the stream() method.

Example:

```php
require 'vendor/autoload.php';

use Zeus\Barcode\Code2of5\Interleaved;
use Zeus\Barcode\Renderer\ImageRenderer;

$renderer = new ImageRenderer();

$bc = new Interleaved('5236589', false);
$bc->backColor = 0xffffaa;
$bc->foreColor = 0x0000ff;
$bc->barwidth  = 2;
$bc->barheight = 150;
$bc->fontsize  = 5;

// Setting render to merge mode
$renderer->merge = true;

$renderer->offsetTop = 50;
$renderer->offsetLeft = 50;
$renderer->backColor = 0xababab;

$bc2 = clone $bc;
$bc2->backColor = 0xffffff;

$bc->draw($renderer); // Draw barcode 1
$renderer->offsetLeft += $bc->getTotalWidth() + 20; // Add a offset
$bc2->draw($renderer);  // Draw barcode 2
$renderer->render(); // Show the result
```

The **RendererInterface::stream()** method do the same of previous example. Its draw a lot of barcodes side by side.

### Using a external resource to render
Sometimes, we need to draw a barcode on a external resource. Using the setResource() method, we can define a external resource and
barcode will put it, always considering "offsetLeft" and "offsetTop" options. For this, the merge mode will be activated.

Example:

```php
require 'vendor/autoload.php';

use Zeus\Barcode\Code2of5\Interleaved;
use Zeus\Barcode\Renderer\ImageRenderer;

$bc = new Interleaved('5236589', false);

$renderer = new ImageRenderer();
$renderer->setResource("<IMAGE PATH OR GD RESOURCE>");
$renderer->offsetLeft = 50;
$renderer->offsetTop = 50;

$bc->draw($renderer)->render();
```
