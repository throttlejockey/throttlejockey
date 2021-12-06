<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
// $root = $_SERVER['DOCUMENT_ROOT'] . '/oc3.throttlejockey.com/';
// $root = "/home1/throttle/oc3.throttlejockey.com/";
$root = "/home/b16aa05/oc3.throttlejockey.com/";

if (file_exists($root . 'config.php')) {
    require_once($root . 'config.php');
    // print "config.php Found!";
} else {
    print ('config.php not found :(');
}

if (file_exists($root . 'system/library/ChromePHP.php')) {
    require_once($root . 'system/library/ChromePHP.php');
    // print "config.php Found!";
} else {
    print ( $root . 'system/library/ChromePHP.php not found :(');
}


// RM ++++++ 
/** SURE POST REQUIREMENTS -- 
Permissible size and weight of packages
Packages tendered for UPS SurePost service are subject to minimum and maximum size and weight restrictions. UPS SurePost packages must be at least
four inches high, six inches long, and .75 inches wide, may not exceed 130 inches in length and girth combined, and no single dimension may exceed 60
inches in length. The maximum weight of a UPS SurePost package is 70 pounds.


[92] SurePost less than 1 lb: 
    - weight: 1 ounce – 15.9 ounces. 
    - Package dimensions cannot exceed 130″ (length × twice width × twice height).

[93] SurePost 1 lb. or greater: 
    - weight: 1 lb. – 70 lbs. 
    - Package dimensions cannot exceed 130″.


Minimum dimension: 4" x 6" x 0.75"           
Maximum dimension: 130" 
    + No single dimension > 60"
Max Weight: 70lbs 

UPS SurePost packages with an actual weight of less than 1 pound must be less than 864 cubic inches to be eligible for the UPS SurePost - Less than 1 lb.
rates. UPS SurePost packages with an actual weight of less than 1 pound that are 864 cubic inches or more are subject to a minimum billable weight of
1 pound and the UPS SurePost – 1 lb. or Greater rates. To calculate the cubic size in inches, multiply the package length (longest side of the package) by
the width by the height.

Packages also must comply with the size, weight, and content limitations set forth in the USPS Domestic Mail Manual in effect at the time of shipping. The
USPS Domestic Mail Manual is available at http://pe.usps.com/text/dmm300/dmm300_landing.htm. Packages not conforming to these size and weight
limits are subject to additional charges. UPS SurePost packages also must meet the requirements for permissible commodities set forth in the UPS®
Tariff/Terms and Conditions of Service, available at ups.com/terms

**/

function isSurePostEligible( $package ) {

    // print "isSurePostEligible(): \r\n";

    // print_r($package);
    // Minimum dimension: 4" x 6" x 0.75"  
    // define("MIN_TOTAL_DIMENSION", 18); // getLengthPlusGirth( 6, 4, 0.75);
    // print "\r\n".getLengthPlusGirth( 6, 4, 0.75)."\r\n";
    // - Package dimensions cannot exceed 130″.
    define("MAX_TOTAL_DIMENSION", 130);
    // No single dimension > 60"
    define("MAX_SINGLE_DIMENSION", 60);

    // [93] SurePost 1 lb. or greater: 
    // - weight: 1 lb. – 70 lbs. 
    define("SUREPOST_ONE_POUND_OR_GREATER_MIN_WEIGHT_OZS", LbsToOzs( 1 ));  // 16oz
    define("SUREPOST_ONE_POUND_OR_GREATER_MAX_WEIGHT_OZS", LbsToOzs( 70 )); // 1120oz

    // [92] SurePost less than 1 lb: 
    // - weight: 1 ounce – 15.9 ounces. 
    define("SUREPOST_LESS_THAN_ONE_POUND_MIN_WEIGHT_OZS",     0); // Pretty sure this *should* be .. 1);    // ounces
    define("SUREPOST_LESS_THAN_ONE_POUND_MAX_WEIGHT_OZS",  15.9); // ounces
    define("SUREPOST_LESS_THAN_ONE_POUND_MAX_CUBIC_INCHES", 864); // ounces

    // Sort dimensions length first. GTL
    $packageDimensions = getSortedDimensions($package);
  
    if( $packageDimensions[0] > MAX_SINGLE_DIMENSION ) { 
        print "\r\nNOT SurePost eligible. \r\n - Reason: a single dimension exceeds max single dimension. \r\n - Length: " . $packageDimensions[0] . "\" \r\n - Max Weight: " . MAX_SINGLE_DIMENSION . "\"\r\n";
        return false;
    }

    // Minimum dimension: 6" x 4" x 0.75"  
    if( $packageDimensions[0] < 6 ||
        $packageDimensions[1] < 4 ||
        $packageDimensions[2] < 0.75 ) {
            $msg  = "Minimum Dimension: 6 x 4 x 0.75 \r\n";
            $msg .= "Package Dimension: " . $packageDimensions[0] . ' x ' . $packageDimensions[1] . ' x ' . $packageDimensions[2] . "\r\n";
            print "\r\nNOT SurePost eligible. \r\n - Reason: Minimum Dimension not met. \r\n $msg \r\n";
            return false;            
        }


    // print "before convert to OZs $weight \r\n";
    $weight = LbsToOzs($package['weight']);  
    // print "after convert to OZs $weight \r\n";

    $totalDimension   = getLengthPlusGirth($packageDimensions[0], $packageDimensions[1], $packageDimensions[2]); // L + ( WIDTH * 2 ) * ( HEIGHT * 2)
    $totalCubicInches = getCubicInches($packageDimensions[0], $packageDimensions[1], $packageDimensions[2]);     // L * W * H

    print "totalDimension: $totalDimension\r\n";
    print "totalCubicInches: $totalCubicInches\r\n";

    // Minimum dimension: 4" x 6" x 0.75", Max Total dimension: 130"
    if( $totalDimension >= MAX_TOTAL_DIMENSION ) {
        print "\r\nNOT eligible for SurePost service. \r\nReason: Package Dimension not supported.\r\nPackage Size: $totalDimension\" \r\n - Max Dimension: " . MAX_TOTAL_DIMENSION . " \r\n - Minimum Dimension: " . MIN_TOTAL_DIMENSION . "\"\r\n";
        return false;
    }
    
    
    // [92] SurePost < 1 lb: 
    // Min Weight: 1 ounce – 15.9 ounces.
    if( $weight <= SUREPOST_LESS_THAN_ONE_POUND_MAX_WEIGHT_OZS && $weight >= SUREPOST_LESS_THAN_ONE_POUND_MIN_WEIGHT_OZS ) {
        // print "So far we are SurePost less than 1lb eligible.\r\n";
        if( $totalCubicInches < SUREPOST_LESS_THAN_ONE_POUND_MAX_CUBIC_INCHES ) {
            print "We are eligible for SurePost < 1 pound! \r\n";
        }
    }


    // [93] SurePost 1 lb. or greater: 
    // - weight: 1 lb. – 70 lbs. 
    if( $weight <= SUREPOST_ONE_POUND_OR_GREATER_MAX_WEIGHT_OZS && $weight >= SUREPOST_ONE_POUND_OR_GREATER_MIN_WEIGHT_OZS ) { 
        print "We are eligible for: SurePost > 1LB .\r\n";
        // print "\r\nNOT eligible for SurePost 1 lb or greater. \r\n - Reason: Package weight not supported.\r\n - Package Weight: $weight ounces \r\n - Max Weight: " . SUREPOST_ONE_POUND_OR_GREATER_MAX_WEIGHT_OZS . "\r\n - Min Weight: " . SUREPOST_ONE_POUND_OR_GREATER_MIN_WEIGHT_OZS . "\r\n";
        // return false;
    }
 

    print_r( $packageDimensions );
    
    print "totalDimension: ";
    print($totalDimension);

}

function LbsToOzs($n){ return $n * 16; }
function OzsToLBS($n){ return $n / 16; }

// Sort greatest to least. Length is the largest number.
// returns an Array.
function getSortedDimensions( $package ) {
    // print "getSortedDimensions() :: \r\n";
    $dims =  [$package['length'], $package['width'], $package['height'] ];
    rsort($dims, SORT_NUMERIC);
    return $dims;
}

function getLengthPlusGirth( $length, $width, $height ) {
    // print "getLengthPlusGirth() :: \r\n";
    // print "length: $length | width: $width | height: $height\r\n";
    return $length + (( $width*2 ) * ( $height*2 ));
}


function getCubicInches( $length, $width, $height ) {
    // print "getLengthPlusGirth() :: \r\n";
    // print "length: $length | width: $width | height: $height\r\n";
    return $length * $width * $height;
}

// print "\r\n==";
// print getLengthPlusGirth( 6, 4, 0.75);
// print "==\r\n";

//  Hacky shim to work offline.
function getWeightUnit( $class_id ) {
    static $weights;

    switch( $class_id ) {
        case 5:
        $weights[$class_id]['unit'] = 'lbs';
        // print "lbs\r\n";
        break;
        case 6:
        $weights[$class_id]['unit'] = 'oz';
        // print "oz\r\n";
        break;
    }

    return	$weights[$class_id]['unit'];
}

// ++++++ RM
function get_largest_package( $packages ) {
    $largestSize = 0;
    $weightTotal = 0;
    $retHeight = '';
    $retWidth = '';
    $retLength = '';
    if ( $packages ) {

        foreach ( $packages as $key => $parcel ) {
            if ( !empty( $parcel['height'] ) && !empty( $parcel['width'] ) && !empty( $parcel['length'] ) ) {
                $Height = $parcel['height'];
                $Length = $parcel['length'];
                $Width  = $parcel['width'];
                $Weight = $parcel['weight'];

                // $totalDim = getLengthPlusGirth( $Length, $Width, $Height );
                // print "Length + Girth: $totalDim inches\r\n";

                // print $totalDim;
                // Convert all weights to LBS
                $WeightClass = $parcel['weight_class_id'];
                $WeightCode = getWeightUnit( $WeightClass );

                // convert OZ to LB.. just divide by 16.
                if ( $WeightCode == 'oz' ) {
                    print( "Weight is an OZ as : $Weight \r\n" );
                    $Weight = $Weight/16;
                    print( "Weight converted to LBS is $Weight \r\n" );
                }

                print( "$ Weight Code: " . $WeightCode . "\r\n" );
                $currentSize = $Width * $Height * $Length;
                $weightTotal += ( float )$Weight;

                if ( $currentSize > $largestSize ) {
                    $largestSize = $currentSize;
                    $retHeight  = $Height;
                    $retWidth   = $Width;
                    $retLength  = $Length;
                }
            }
        }

        $package_total_weight = $weightTotal;

        return array( 'width'  => $retWidth,
                     'height'  => $retHeight,
                     'length'  => $retLength,
                     'weight'  => $package_total_weight // Our weight will always be returned as LBS, even if < 1. So we'll want to convert that back to OZ before checking for Min weight SurePost
                     );
    }
}
// RM ++++++
$packages = array (
    0 =>
    array (
        'cart_id' => '93',
        'product_id' => '334',
        'name' => '2019 Team Honda HRC Sticker Sheet',
        'model' => '19TEAMSS',
        'shipping' => '1',
        'image' => 'catalog/IMG_0642.jpg',
        'option' =>
        array (),
        'download' =>
        array (),
        'quantity' => '1',
        'minimum' => '1',
        'subtract' => '0',
        'stock' => true,
        'price' => 3.0,
        'total' => 3.0,
        'reward' => 0,
        'points' => 0,
        'tax_class_id' => '0',
        'weight' => 5.0,
        'weight_class_id' => '6',
        'length' => '6.00000000',
        'width' => '9.00000000',
        'height' => '1.00000000',
        'length_class_id' => '3',
        'recurring' => false,
    ),
    // /*
    1 =>
    array (
        'cart_id' => '94',
        'product_id' => '428',
        'name' => '\'21 JULY 4th HONDA HRC FULL KIT.',
      'model' => '21JULY4TH',
      'shipping' => '1',
      'image' => 'catalog/BIKE_PICS/21RedBudPit_450.jpg',
      'option' => 
      array (
        0 => 
        array (
          'product_option_id' => '48227',
          'product_option_value_id' => '157202',
          'option_id' => '13',
          'option_value_id' => '1222',
          'name' => 'Honda Model & Year',
          'value' => '\'21, \'22 CRF450R/WE',
          'type' => 'select',
          'quantity' => '0',
          'subtract' => '0',
          'price' => '0.0000',
          'price_prefix' => '+',
          'points' => '0',
          'points_prefix' => '+',
          'weight' => '0.00000000',
          'weight_prefix' => '+',
        ),
        1 => 
        array (
          'product_option_id' => '48223',
          'product_option_value_id' => '',
          'option_id' => '19',
          'option_value_id' => '',
          'name' => 'RACE NUMBER',
          'value' => '2',
          'type' => 'text',
          'quantity' => '',
          'subtract' => '',
          'price' => '',
          'price_prefix' => '',
          'points' => '',
          'points_prefix' => '',
          'weight' => '',
          'weight_prefix' => '',
        ),
        2 => 
        array (
          'product_option_id' => '48221',
          'product_option_value_id' => '157184',
          'option_id' => '36',
          'option_value_id' => '163',
          'name' => 'NUMBER COLOR',
          'value' => 'BLACK',
          'type' => 'select',
          'quantity' => '0',
          'subtract' => '0',
          'price' => '0.0000',
          'price_prefix' => '+',
          'points' => '0',
          'points_prefix' => '+',
          'weight' => '0.00000000',
          'weight_prefix' => '+',
        ),
        3 => 
        array (
          'product_option_id' => '48222',
          'product_option_value_id' => '157186',
          'option_id' => '20',
          'option_value_id' => '101',
          'name' => 'RACE NUMBER STYLE',
          'value' => 'INTERNATIONAL',
          'type' => 'select',
          'quantity' => '0',
          'subtract' => '0',
          'price' => '0.0000',
          'price_prefix' => '+',
          'points' => '0',
          'points_prefix' => '+',
          'weight' => '0.00000000',
          'weight_prefix' => '+',
        ),
        4 => 
        array (
          'product_option_id' => '48224',
          'product_option_value_id' => '157192',
          'option_id' => '23',
          'option_value_id' => '72',
          'name' => '# BACKGROUND COLOR',
          'value' => 'WHITE',
          'type' => 'select',
          'quantity' => '0',
          'subtract' => '0',
          'price' => '0.0000',
          'price_prefix' => '+',
          'points' => '0',
          'points_prefix' => '+',
          'weight' => '0.00000000',
          'weight_prefix' => '+',
        ),
        5 => 
        array (
          'product_option_id' => '48228',
          'product_option_value_id' => '157213',
          'option_id' => '25',
          'option_value_id' => '68',
          'name' => 'HUB/MINI FRONT PLATE DECALS',
          'value' => 'YES, 8 hub decals',
          'type' => 'select',
          'quantity' => '0',
          'subtract' => '0',
          'price' => '10.0000',
          'price_prefix' => '+',
          'points' => '0',
          'points_prefix' => '+',
          'weight' => '0.00000000',
          'weight_prefix' => '+',
        ),
        6 => 
        array (
          'product_option_id' => '48229',
          'product_option_value_id' => '157218',
          'option_id' => '28',
          'option_value_id' => '112',
          'name' => 'PLATE LOGOS or NAME',
          'value' => 'MONSTER SUPERCROSS',
          'type' => 'select',
          'quantity' => '0',
          'subtract' => '0',
          'price' => '5.0000',
          'price_prefix' => '+',
          'points' => '0',
          'points_prefix' => '+',
          'weight' => '0.00000000',
          'weight_prefix' => '+',
        ),
        7 => 
        array (
          'product_option_id' => '48231',
          'product_option_value_id' => '157224',
          'option_id' => '18',
          'option_value_id' => '61',
          'name' => 'FRONT PLATE SHAPE',
          'value' => 'STOCK/OEM',
          'type' => 'select',
          'quantity' => '0',
          'subtract' => '0',
          'price' => '0.0000',
          'price_prefix' => '+',
          'points' => '0',
          'points_prefix' => '+',
          'weight' => '0.00000000',
          'weight_prefix' => '+',
        ),
        8 => 
        array (
          'product_option_id' => '48225',
          'product_option_value_id' => '157199',
          'option_id' => '306',
          'option_value_id' => '1216',
          'name' => 'ADD SEAT COVER',
          'value' => 'NO THANKS',
          'type' => 'select',
          'quantity' => '0',
          'subtract' => '0',
          'price' => '0.0000',
          'price_prefix' => '+',
          'points' => '0',
          'points_prefix' => '+',
          'weight' => '0.00000000',
          'weight_prefix' => '+',
        ),
      ),
      'download' => 
      array (
      ),
      'quantity' => '1',
      'minimum' => '1',
      'subtract' => '0',
      'stock' => true,
      'price' => 214.990000000000009094947017729282379150390625,
      'total' => 214.990000000000009094947017729282379150390625,
      'reward' => 0,
      'points' => 0,
      'tax_class_id' => '0',
      'weight' => 84.0,
      'weight_class_id' => '5',
      'length' => '24.00000000',
      'width' => '18.00000000',
      'height' => '1.00000000',
      'length_class_id' => '3',
      'recurring' => false,
    ),
    // */
);



$p = get_largest_package( $packages );

$e = isSurePostEligible( $p );

// print_r( $p );