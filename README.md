Twitter
=======

This class extends [Abraham's Twitter OAuth library](https://github.com/abraham/twitteroauth) and provides some additional formatting to tweets returned.  The class will add links around entities in tweets such as mentions, hashtags, and urls.  In addition, the class formats the created_at date into a useful date and is customizable using the PHP date formats.

See my post on [adding links around entities in the Twitter REST API](http://www.webtipblog.com/add-links-to-twitter-mentions-hashtags-and-urls-with-php-and-the-twitter-1-1-oauth-api/)<br>
See my post on [formatting the Twitter REST API date] (http://www.webtipblog.com/format-created-date-twitters-rest-api/)

[I develop web applications in Minneapolis, MN](http://www.josephmsexton.com)

Usage
------------

To use the class, simply create a Twitter object and pass configuration parameters.  Since this extends Abraham's Twitter OAuth library, the same parameters are needed to instantiate the object.  Also note that all of the methods available to the twitteroauth class remain available.
``` php
<?php

require_once( "Twitter.php" );
require_once( "twitteroauth/config.php" );

// create oauth object
$connection = new Twitter( CONSUMER_KEY, CONSUMER_SECRET, ACCES_TOKEN, ACCES_TOKEN_SECRET );

// connect and get data - https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
$data = $connection->getTimelines( 'statuses/user_timeline', array(
	'screen_name'      => 'joesexton00', // <-- your twitter handle here
	'count'            => 3,
	'include_entities' => true
) ); ?>

```

Using the getTimeline method will add two new nodes to the tweet objects returned by Twitter, a formattedText node that has <a> tags around entities, and a formattedDate node that has a formatted date.
``` php

<?php if ( $data ) : foreach ( $data as $tweet ) : ?>

	<blockquote class="tweet-text"><?php echo $tweet->formattedText; ?></blockquote>

	<a class="tweet-link" target="_blank" href="http://twitter.com/<?php echo $tweet->user->screen_name; ?>/status/<?php echo $tweet->id_str; ?>" >
		- via <?php echo strip_tags( $tweet->source ); ?> <?php echo $tweet->formattedDate; ?>
	</a>
<?php endforeach; endif; ?>

```

This would output the following text
<br>

Check out Word Finder Cheat for the best word game help! <span style="text-decoration:underline">wordfindercheat.com</span> via <span style="text-decoration:underline">@WordFinderCheat</span><br>
- via Tweet Button Nov 26th<br>
DropzoneJS is an open source library that provides drag'n'drop file uploads with image previews <span style="text-decoration:underline">dropzonejs.com</span> via <span style="text-decoration:underline">@matenyo</span><br>
- via Tweet Button Oct 12th<br>
We're under attack! I've never seen so many spiders in my life...<br>
- via Twitter for iPhone May 11th<br>

<br>
If the default date format is unsatisfactory, simply pass the created_at date to the formatDate() method with a format of your choosing.

``` php

$date = $connection->formateDate( $tweet->created_at, 'Y-m-d' );

```