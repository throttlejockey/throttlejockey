<?php

    function nl() {
        print "\r\n";
    }

    function isEmpty($text, $verbose=false) {
        $v = $verbose;
        $text = strip_tags(html_entity_decode($text, ENT_QUOTES, 'UTF-8'), '<img>');
        if($v) { print "strip_tags: $text"; nl(); }

        $text = str_replace('&nbsp;', '', $text);
        if($v) { print "str_replace: $text"; nl(); }

        $text = trim($text);
        if($v) { print "trim: $text"; nl(); }

        if (empty($text)) {
            if($v) { print "empty!"; nl(); }

            return true;
        }
            if($v) { print "NOT empty!"; nl(); }

        return false;
    }



    function x( $strVar, $var ) {
        $e = isEmpty($var);
        print "$strVar - ";
        nl();
        print "$var";
        nl();

        print "Is Empty: $e";

        nl();
    }




$test1 = <<<EOT
<p>.<img src="/image/data/15WebColors-01.jpg" style="width: 546px;"> </p>
EOT;

$test2 = <<<EOT
<img src="/image/data/15WebColors-01.jpg" style="width: 546px;">
EOT;

$test3 = <<<EOT
<p><img src="beef.jpg"> </p>
EOT;

$test4 = <<<EOT
<p><img src='beef.jpg'> </p>
EOT;


print isEmpty($test1, 1); nl();
print isEmpty($test2, 1); nl();
print isEmpty($test3, 1); nl();
print isEmpty($test4, 1); nl();

    // x( "1", $test1);
    // x( "2", $test2);
    // x( "3", $test3);
    // x( "4", $test4);

    // print "Test 1: $test1   //   " . isEmpty($test1);
    // nl();
    // print "Test 2: $test2   //   " . isEmpty($test2);
    // nl();


/*
include('config.php');
// $link = mysql_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD);

$mysqli = new mysqli( DB_HOSTNAME, DB_USERNAME, DB_PASSWORD );

// check connection
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

// $mysqli->query("CREATE TEMPORARY TABLE myCity LIKE City");

$city = '<br /> <img alt="" height="295" src="/image/data/15WebColors-01.jpg" width="500" />';

$test = <<<EOT
<p><img src="https://throttlejockey.com/image/data/15WebColors-01.jpg" style="width: 546px;"> </p>
EOT;

$test1 = <<<EOT
<p>.<img src="/image/data/15WebColors-01.jpg" style="width: 546px;"> </p>
EOT;

$test2 = <<<EOT
<img src="/image/data/15WebColors-01.jpg" style="width: 546px;">
EOT;

$test3 = <<<EOT
<p><img src="beef.jpg"> </p>
EOT;

$test4 = <<<EOT
<p><img src='beef.jpg'> </p>
EOT;

// this query will fail, cause we didn't escape $city
// if (!$mysqli->query("INSERT into myCity (Name) VALUES ('$city')")) {
//     printf("Error: %s\n", $mysqli->sqlstate);
// }

$t1 = $mysqli->real_escape_string($test1);
$t2 = $mysqli->real_escape_string($test2);

$t3 = $mysqli->real_escape_string($test3);
$t4 = $mysqli->real_escape_string($test4);



print "Test 1: $t1";
print "\r\n";
print "Test 2: $t2";
print "\r\n";
print "Test 3: $t3";
print "\r\n";
print "Test 4: $t4";
print "\r\n";

// this query with escaped $city will work
// if ($mysqli->query("INSERT into myCity (Name) VALUES ('$city')")) {
//     printf("%d Row inserted.\n", $mysqli->affected_rows);
// }

$mysqli->close();
*/



// ?>