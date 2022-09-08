<?
// truncation function
function truncateString($str, $chars, $to_space, $replacement="...") {
    if($chars > strlen($str)) return $str;

    $str = substr($str, 0, $chars);
    $space_pos = strrpos($str, " ");
    if($to_space && $space_pos >= 0) 
    $str = substr($str, 0, strrpos($str, " "));

    return($str . $replacement);
}


// minor settings
$hashTag = "#wikibot";
$hashLength = strlen($hashTag);
$tweetLength = 200; // can change this, but currently set to old Twitter standards
$maxLength = $tweetLength - $hashLength;
$randomNumber = rand(1,10);


// get a list of 10 random pages from wikipedia, and select one at random
$apiResults = file_get_contents("https://en.wikipedia.org/w/api.php?action=query&list=random&format=json&rnnamespace=0&rnlimit=10");
$jsonSpew = json_decode($apiResults, true);
$wikiID = $jsonSpew['query']['random'][$randomNumber]['id'];


// get the JSON data for that specific page
$wikiAPIString = "https://en.wikipedia.org/w/api.php?action=query&format=json&prop=extracts&pageids=".$wikiID."&callback=&utf8=1&exsentences=5&exlimit=1";

$secondGet = file_get_contents($wikiAPIString);
$secondGet = substr($secondGet, 5);
$secondGet = substr($secondGet, 0, -1);

$secondSpew = json_decode($secondGet, true);

$reduceArray = $secondSpew['query']['pages'][$wikiID];
$bigBlock = $reduceArray['extract'];

// strip HTML tags from bigBlock
$bigBlock = strip_tags($bigBlock);


// build the stupid tweet
$myTweet = truncateString($bigBlock, $maxLength, TRUE, '...').' '.$hashTag;

// Execute the tweet using the t CLI command
$command = "t update '".$myTweet."'";
exec ($command);
?>