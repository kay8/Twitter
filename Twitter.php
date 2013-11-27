<?php
require_once( "twitteroauth/twitteroauth/twitteroauth.php" );

/**
* Twitter
*
* @author  Joe Sexton <joe@josephmsexton.com>
*/
class Twitter extends TwitterOAuth {

	/**
	 * constructor
	 *
	 * @author  Joe Sexton <joe.sexton@bigideas.com>
	 * @param   string $consumerKey
	 * @param   string $consumerSecret
	 * @param   string $accessToken
	 * @param   string $accessTokenSecret
	 */
	function __construct( $consumerKey, $consumerSecret, $accessToken, $accessTokenSecret )
	{
		parent::__construct( $consumerKey, $consumerSecret, $accessToken, $accessTokenSecret );
	}

	/**
	 * getTimelines
	 *
	 * @author  Joe Sexton <joe.sexton@bigideas.com>
	 * @param   array $params
	 * @return  array
	 */
	public function getTimelines( $timeline, array $params = array() ) {

		$tweets = $this->get( $timeline, $params );

		if ( $tweets ) {
			foreach ( $tweets as &$tweet ) {
				$tweet->formattedText = $this->addTweetEntityLinks( $tweet );
				$tweet->formattedDate = $this->formatDate( $tweet->created_at );
			}
		}

		return $tweets;
	}

	/**
	 * addTweetEntityLinks
	 * wrap <a> tags around entities in a tweet(hashtags, mentions, and urls)
	 *
	 * @author  Joe Sexton <joe.sexton@bigideas.com>
	 * @param   object $tweet a JSON tweet object v1.1 REST API
	 * @return  string tweet
	 */
	public function addTweetEntityLinks( $tweet ) {

		// actual tweet as a string
		$tweetText = $tweet->text;

		// create an array to hold urls
		$tweetEntites = array();

		// add each url to the array
		foreach( $tweet->entities->urls as $url ) {
			$tweetEntites[] = array (
					'type'    => 'url',
					'curText' => substr( $tweetText, $url->indices[0], ( $url->indices[1] - $url->indices[0] ) ),
					'newText' => "<a href='".$url->expanded_url."' target='_blank'>".$url->display_url."</a>"
				);
		}  // end foreach

		// add each user mention to the array
		foreach ( $tweet->entities->user_mentions as $mention ) {
			$string = substr( $tweetText, $mention->indices[0], ( $mention->indices[1] - $mention->indices[0] ) );
			$tweetEntites[] = array (
					'type'    => 'mention',
					'curText' => substr( $tweetText, $mention->indices[0], ( $mention->indices[1] - $mention->indices[0] ) ),
					'newText' => "<a href='http://twitter.com/".$mention->screen_name."' target='_blank'>".$string."</a>"
				);
		}  // end foreach

		// add each hashtag to the array
		foreach ( $tweet->entities->hashtags as $tag ) {
			$string = substr( $tweetText, $tag->indices[0], ( $tag->indices[1] - $tag->indices[0] ) );
			$tweetEntites[] = array (
					'type'    => 'hashtag',
					'curText' => substr( $tweetText, $tag->indices[0], ( $tag->indices[1] - $tag->indices[0] ) ),
					'newText' => "<a href='http://twitter.com/search?q=%23".$tag->text."&src=hash' target='_blank'>".$string."</a>"
				);
		}  // end foreach

		foreach ( $tweetEntites as $entity ) {
			$tweetText = str_replace( $entity['curText'], $entity['newText'], $tweetText );
		} // end foreach

		return $tweetText;
	}

	/**
	 * formatDate
	 * format the twitter date into something useful
	 *
	 * @author  Joe Sexton <joe.sexton@bigideas.com>
	 * @param   string $date
	 * @param   string $format
	 * @return  string
	 */
	public function formatDate( $date, $format = 'M jS' ) {

		// parse what twitter thinks a date should look like and format to a standard
		$dateArr = explode( ' ', $date );

		// Mon Mar 04 04:19:00 +0000 2013
		$unformattedDate = $dateArr[1].' '.$dateArr[2].' '.$dateArr[5];

		// Mar 4th
		$formattedDate = date( $format, strtotime( $unformattedDate ) );

		return $formattedDate;
	}
}