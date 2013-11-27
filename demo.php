<?php

require_once( "Twitter.php" );
require_once( "twitteroauth/config.php" );

// create oauth object
$connection = new Twitter( CONSUMER_KEY, CONSUMER_SECRET, ACCES_TOKEN, ACCES_TOKEN_SECRET );

// connect and get data - https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
$data = $connection->getTimelines( 'statuses/user_timeline', array(
	'screen_name'      => 'joesexton00', // <-- your twitter handle here
	'count'            => 5,
	'include_entities' => true
) ); ?>

<?php if ( $data ) : foreach ( $data as $tweet ) : ?>

	<blockquote class="tweet-text"><?php echo $tweet->formattedText; ?></blockquote>

	<a class="tweet-link" target="_blank" href="http://twitter.com/<?php echo $tweet->user->screen_name; ?>/status/<?php echo $tweet->id_str; ?>" >
		- via <?php echo strip_tags( $tweet->source ); ?> <?php echo $tweet->formattedDate; ?>
	</a>
<?php endforeach; endif; ?>

