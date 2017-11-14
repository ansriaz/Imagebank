<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Flickr crawler
 *
 * PHP version 5.6
 * @author     Ans Riaz (ansriazch@gmail.com)
 * @package    ImageBank.
 * @category   Libraries
 * @copyright  Ans Riaz - https://www.about.me/rizh
 */

 // Key: ffe18affff0e85da5bbaeb46b2786c69
 // Secret: 3cc14352b87d7c16

require_once("phpFlickr.php");
$f = new phpFlickr('ffe18affff0e85da5bbaeb46b2786c69');

// Cache the Flickr requests for one hour
$lifetime = 60 * 60; // One hour
$f->enableCache("fs", "path/to/cache/folder", $lifetime);

$photos = $f->photos_search(array("tags"=>"brown,cow", "tag_mode"=>"any"));

echo $photos;

// foreach ($recent['photo'] as $photo) {
//     $owner = $f->people_getInfo($photo['owner']);
//     echo "<a href='http://www.flickr.com/photos/" . $photo['owner'] . "/" . $photo['id'] . "/'>";
//     echo $photo['title'];
//     echo "</a> Owner: ";
//     echo "<a href='http://www.flickr.com/people/" . $photo['owner'] . "/'>";
//     echo $owner['username'];
//     echo "</a><br>";
// }

?>
