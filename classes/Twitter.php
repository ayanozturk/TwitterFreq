<?php
/**
 * Class Twitter
 */
class Twitter
{
    /* @var string */
    protected $consumerKey = '';
    /* @var string */
    protected $consumerSecret = '';
    /* @var mixed */
    protected $authentication = null;
    /* @var array */
    protected $tweets = array();
    /* @var string */
    protected $username;

    public function __construct(){
        $config = include __DIR__ . '/../config/global.php';
        $this->consumerKey = $config['twitter']['consumer_key'];
        $this->consumerSecret= $config['twitter']['consumer_secret'];
    }
    
    /**
     * Authenticate with twitter
     * @return $this
     * @throws Exception
     */
    public function authenticate()
    {
        if (!$this->authentication) {
            try {
                $ch = curl_init();
            } catch (\Exception $e) {
                echo "cURL library not found. Please install php5-curl extension.";
                die();
            }

            $data = array();
            $data['grant_type'] = "client_credentials";
            curl_setopt($ch,CURLOPT_URL, 'https://api.twitter.com/oauth2/token');
            curl_setopt($ch,CURLOPT_POST, true);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch,CURLOPT_USERPWD, $this->consumerKey . ':' . $this->consumerSecret);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($ch);
            $arrayResult = json_decode($result);
            if (isset($arrayResult->errors)) {
                throw new \Exception($arrayResult->errors[0]->message);
            }

            $this->authentication = $result;

            curl_close($ch);
        }

        return $this;
    }

    /**
     * Fetch Twitter Feed
     *
     * @param int $numberOfTweets Number of Tweets
     * @return $this
     * @throws InvalidArgumentException
     */
    public function fetchFeed($numberOfTweets = 10)
    {
        if (!$this->getAuthentication()) {
            $this->authenticate();
        }

        if (!is_numeric($numberOfTweets)) {
            throw new \InvalidArgumentException('Limit has to be an integer');
        }

        $token = json_decode($this->getAuthentication());
        $feed = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name={$this->getUsername()}&count={$numberOfTweets}&include_rts=1";
        $cache_file = dirname(__FILE__).'/cache/'.'twitter-cache';

        $modified = filemtime( $cache_file );
        $now = time();
        $interval = 10;

        if ( !$modified || ( ( $now - $modified ) > $interval ) ) {
            $bearer = $token->access_token;
            $context = stream_context_create(array(
                'http' => array(
                    'method'=>'GET',
                    'header'=>"Authorization: Bearer " . $bearer
                )
            ));

            $json = file_get_contents( $feed, false, $context );

            if ( $json ) {
                $cache_static = fopen( $cache_file, 'w' );
                fwrite( $cache_static, $json );
                fclose( $cache_static );
            }
        }

        $json = file_get_contents( $cache_file );
        $tweetData = json_decode($json);

        if (count($tweetData) > 0) {
            foreach ($tweetData as $data) {
                $tweet = new Tweet();
                $tweet->exchangeArray($data);
                $this->addTweet($tweet);
            }
        }

        return $this;
    }

    /**
     * Calculate Word Frequency
     *
     * @param int $limit
     * @return array
     * @throws InvalidArgumentException
     */
    public function calculateFrequency($limit = 10)
    {
        if (!is_numeric($limit)) {
            throw new \InvalidArgumentException('Limit has to be an integer');
        }

        $tweets = $this->getTweets();
        $results = array();

        if (count($tweets) > 0) {
            /* @var $tweet Tweet */
            foreach ($tweets as $tweet) {
                $words = explode(' ', $tweet->getText());

                foreach ($words as $word) {
                    $word = str_replace(array('.', ','), '', strtolower(trim($word)));
                    if (!isset($results[$word])) {
                        $results[$word] = 0;
                    }
                    $results[$word] += 1;
                }
            }
        }

        arsort($results);
        $output = array_slice($results, 0, $limit);
        return $output;
    }

    /* Getters and Setters */

    /**
     * Add a tweet
     * @param Tweet $tweet Tweet Data
     * @return $this
     */
    public function addTweet(Tweet $tweet)
    {
        $this->tweets[] = $tweet;
        return $this;
    }

    /**
     * @return string
     */
    public function getConsumerKey()
    {
        return $this->consumerKey;
    }

    /**
     * @param string $consumerKey
     * @return $this
     */
    public function setConsumerKey($consumerKey)
    {
        $this->consumerKey = $consumerKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getConsumerSecret()
    {
        return $this->consumerSecret;
    }

    /**
     * @param string $consumerSecret
     * @return $this
     */
    public function setConsumerSecret($consumerSecret)
    {
        $this->consumerSecret = $consumerSecret;
        return $this;
    }

    /**
     * @return null
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * @param null $authentication
     * @return $this
     */
    public function setAuthentication($authentication)
    {
        $this->authentication = $authentication;
        return $this;
    }

    /**
     * @return array
     */
    public function getTweets()
    {
        return $this->tweets;
    }

    /**
     * @param array $tweets
     * @return $this
     */
    public function setTweets($tweets)
    {
        $this->tweets = $tweets;
        return $this;
    }

    /**
     * Get Twitter Username
     *
     * @return string
     * @throws Exception
     */
    public function getUsername()
    {
        if (!is_string($this->username)) {
            throw new \Exception('Username has to be a string');
        }

        return $this->username;
    }

    /**
     * Set Twitter Username
     *
     * @param string $username
     * @return $this
     * @throws Exception
     */
    public function setUsername($username)
    {
        if (!is_string($username)) {
            throw new \Exception('Username has to be a string');
        }

        $this->username = trim($username);
        return $this;
    }

}
