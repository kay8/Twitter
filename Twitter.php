<?php
require_once( "twitteroauth/twitteroauth/twitteroauth.php" );

/**
* Twitter
*
* @author  Joe Sexton <joe@josephmsexton.com>
*/
class Twitter extends TwitterOAuth {

	/**
	 * @var string
	 */
	public $host = "https://api.twitter.com/1.1/";

	/**
	 * constructor
	 *
	 * @author  Joe Sexton <joe@josephmsexton.com>
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
	 * get
	 *
	 * @author  Joe Sexton <joe@josephmsexton.com>
	 * @param   string $url
	 * @param   array $params
	 * @return  array
	 */
	public function get( $url, array $params = array() ) {

		$tweets = parent::get( $url, $params );

		if ( $tweets ) {
			foreach ( $tweets as &$tweet ) {
				$tweet->encoded_text = $this->addTweetEntityLinks( $tweet );
				$tweet->date_object  = new DateTime( $tweet->created_at );
				$tweet->tweet_link   = $this->getTweetLink( $tweet );
			}
		}

		return $tweets;
	}

	/**
	 * addTweetEntityLinks
	 * wrap <a> tags around entities in a tweet(hashtags, mentions, and urls)
	 *
	 * @author  Joe Sexton <joe@josephmsexton.com>
	 * @param   object $tweet a JSON tweet object v1.1 REST API
	 * @return  string tweet
	 */
	public function addTweetEntityLinks( $tweet ) {

		// actual tweet as a string
		$text = $tweet->text;

		if ( !empty( $tweet->entities ) ) {

			$tweetEntities = array_merge(
				$this->_getUrlEntities( $tweet ),
				$this->_getMediaUrlEntities( $tweet ),
				$this->_getUserMentionEntities( $tweet ),
				$this->_getHashtagEntities( $tweet )
			);

			// replace the old text with the new text for each entity
			foreach ( $tweetEntities as $entity ) {
				$text = str_replace( $entity['curText'], $entity['newText'], $text );
			} // end foreach

		} // end if

		return $text;
	}

	/**
	 * get url entities
	 *
	 * @author	Joe Sexton <joe@josephmsexton.com>
	 * @param 	object $tweet JSON Tweet object
	 * @return 	array array of entities
	 */
	protected function _getUrlEntities( $tweet )
	{
		// create an array to hold urls
		$entities = array();

		// add each url to the array
		foreach( $tweet->entities->urls as $url ) {
			$entities[] = array(
					'type'    => 'url',
					'curText' => substr( $tweet->text, $url->indices[0], ( $url->indices[1] - $url->indices[0] ) ),
					'newText' => "<a href='".$url->expanded_url."' target='_blank'>".$url->display_url."</a>"
				);
		}  // end foreach

		return $entities;

	}

	/**
	 * get media url entities
	 *
	 * @author	Kay Yamagishi <kay.yamagishi.work@gmail.com>
	 * @param 	object $tweet JSON Tweet object
	 * @return 	array array of entities
	 */
	protected function _getMediaUrlEntities( $tweet )
	{
		// create an array to hold urls
		$entities = array();

		// add each url to the array
		foreach( $tweet->entities->media as $media_url ) {
			$entities[] = array(
					'type'    => 'url',
					'curText' => mb_substr( $tweetText, $media_url->indices[0], ( $media_url->indices[1] - $media_url->indices[0] ), 'UTF-8' ),
					'newText' => "<a href='".$media_url->expanded_url."' target='_blank'>".$media_url->display_url."</a>"
				);
		}  // end foreach

		return $entities;

	}

	/**
	 * get user mention entities
	 *
	 * @author	Joe Sexton <joe@josephmsexton.com>
	 * @param 	object $tweet JSON Tweet object
	 * @return 	array array of entities
	 */
	protected function _getUserMentionEntities( $tweet )
	{
		// create an array to hold urls
		$entities = array();

		// add each user mention to the array
		foreach ( $tweet->entities->user_mentions as $mention ) {
			$string = substr( $tweet->text, $mention->indices[0], ( $mention->indices[1] - $mention->indices[0] ) );
			$entities[] = array (
					'type'    => 'mention',
					'curText' => substr( $tweet->text, $mention->indices[0], ( $mention->indices[1] - $mention->indices[0] ) ),
					'newText' => "<a href='http://twitter.com/".$mention->screen_name."' target='_blank'>".$string."</a>"
				);
		}  // end foreach

		return $entities;

	}

	/**
	 * get hashtag entities
	 *
	 * @author	Joe Sexton <joe@josephmsexton.com>
	 * @param 	object $tweet JSON Tweet object
	 * @return 	array array of entities
	 */
	protected function _getHashtagEntities( $tweet )
	{
		// create an array to hold urls
		$entities = array();

		// add each hashtag to the array
		foreach ( $tweet->entities->hashtags as $tag ) {
			$string = substr( $tweet->text, $tag->indices[0], ( $tag->indices[1] - $tag->indices[0] ) );
			$entities[] = array (
					'type'    => 'hashtag',
					'curText' => substr( $tweet->text, $tag->indices[0], ( $tag->indices[1] - $tag->indices[0] ) ),
					'newText' => "<a href='http://twitter.com/search?q=%23".$tag->text."&src=hash' target='_blank'>".$string."</a>"
				);
		}  // end foreach

		return $entities;

	}

	/**
	 * getTweetLink
	 *
	 * @author	Joe Sexton <joe@josephmsexton.com>
	 * @param 	object $tweet JSON Tweet object
	 * @return 	string link to the tweet
	 */
	public function getTweetLink( $tweet )
	{
		$link = 'http://twitter.com/'.$tweet->user->screen_name.'/status/'.$tweet->id_str;

		return $link;

	} // end getTweetLink()
}