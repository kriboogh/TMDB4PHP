TMDB4PHP
========

PHP wrapper classes for TMDB (TheMovieDatabase) API v3.

Thanks to:
Calibrate (www.calibrate.be)

Requirements:
------------
 PHP 5.3+
 Curl
 TMDB Api-key

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

Assets
------

I created several objects for the 'assets' TMDB uses. These include:
* Movie
* Person
* Company
* Collection
* Genre

You can create a new asset and have it filled in with information automatically by passing in the id in the constructor.

For example:

$movie = new Movie(10);
Will load a movie object with the info for a movie with tmdb id 10;

