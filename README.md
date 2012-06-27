TMDB4PHP
========

PHP wrapper classes for TMDB (TheMovieDatabase) API v3.

Thanks to:
People at Calibrate (www.calibrate.be)


Basic usage:
-----------
require_once('TMDB.php');

$db = TMDB::getInstance('<your api-key>');
$db->adult = true;  // return adult content
$db->paged = false; // merges all paged results into a single result automatically

$title = 'The Matrix';
$year = '1999';

$results = $db->search('movie', array('query'=>$title, 'year'=>$year));

$movie = reset($results);
$images = $movie->posters('300');

TMDB.php
contains the API wrapper for querying TMDB. Results are transformed into the helper objects automatically. 
For example searching for 'movie' will result in a array of Movie objects.

More info will come soon.
