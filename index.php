<?php
require_once('classes/Twitter.php');
require_once('classes/Tweet.php');

try {
    if (isset($argv) && isset($argv[1])) {
        $twitterUsername = $argv['1'];
    } else {
        $twitterUsername = 'Secretsales';
    }

    $twitter = new Twitter();
    $results = $twitter
        ->setUsername($twitterUsername)
        ->fetchFeed(100)
        ->calculateFrequency(10);

    if (count($results) > 0) {
        foreach ($results as $word => $count) {
            echo "$word => $count times \n";
        }
    }
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    echo "Please check and fix the cause of errors and try again.\n";
}

