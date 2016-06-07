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
