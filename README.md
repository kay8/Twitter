Twitter
=======

This class extends [Abraham's Twitter OAuth library](https://github.com/abraham/twitteroauth) and provides some additional formatting to tweets returned.  The class will add links around entities in tweets such as mentions, hashtags, and urls.  In addition, the class creates a php DateTime object from the created_at date.

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
$connection = new Twitter( CONSUMER_KEY, CONSUMER_SECRET, ACCES_TOKEN, ACCES_TOKEN_SECRET ); // define in config.php

// connect and get data - https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
$data = $connection->get( 'statuses/user_timeline', array(
	'screen_name'      => 'joesexton00', // <-- your twitter handle here
	'count'            => 5,
	'include_entities' => true
) ); ?>

```

Using the get method will add three new nodes to the tweet objects returned by Twitter: a encoded_text node that has &lt;a &gt; tags around entities, a date_object node that contains a php DateTime object, and a tweet_link node that contains a link to the tweet on Twitter.
``` php

<?php if ( $data ) : foreach ( $data as $tweet ) : ?>

	<blockquote class="tweet-text"><?php echo $tweet->encoded_text; ?></blockquote>

	<a class="tweet-link" target="_blank" href="<?php echo $tweet->tweet_link; ?>" >
		- via <?php echo strip_tags( $tweet->source ); ?> <?php echo $tweet->date_object->format('M jS'); ?>
	</a>
<?php endforeach; endif; ?>

```

This would output the following text
<br>

> Check out Word Finder Cheat for the best word game help! **wordfindercheat.com** via **@WordFinderCheat**<br>

- via Tweet Button Nov 26th<br>

> DropzoneJS is an open source library that provides drag'n'drop file uploads with image previews **dropzonejs.com** via **@matenyo**<br>

- via Tweet Button Oct 12th<br>

> We're under attack! I've never seen so many spiders in my life...<br>

- via Twitter for iPhone May 11th<br>

