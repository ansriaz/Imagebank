<?php

namespace App\Logic\Services\TwitterService;

require_once('vendor/j7mbo/twitter-api-php/TwitterAPIExchange.php');
use App\Models\Tweet;
use Log;

class TwitterCrawler
{


    public function __construct()
    {

    }

    public function crawl($tags)
    {

        // Log::info($tags);

        $TWITTER_CONSUMER_KEY = 'fK8IKZ7PzQcXxQPjXIPbmAR4n';
        $TWITTER_CONSUMER_SECRET = 'xbcDexEYyfHgwVlLQdlhJAMv9UrQgfu3WRPByYte0YAmdLtFGj';
        $TWITTER_ACCESS_TOKEN = '228843790-Wi9hpeTxQuLokfgGCJ972eQZPmFvLlwjQr8tEqoC';
        $TWITTER_ACCESS_TOKEN_SECRET = 'zo2mIwnb2oQW2PS9lXBjJ2XEMQcQWW7o8gRtkxunFNCAf';
        $URL_LOGIN = 'http://localhost/twitter_login.php';
        $URL_CALLBACK = 'http://localhost/twitter_callback.php';

        $settings = array(
            'oauth_access_token' => $TWITTER_ACCESS_TOKEN,
            'oauth_access_token_secret' => $TWITTER_ACCESS_TOKEN_SECRET,
            'consumer_key' => $TWITTER_CONSUMER_KEY,
            'consumer_secret' => $TWITTER_CONSUMER_SECRET
        );

        $twitter = new \TwitterAPIExchange($settings);

        foreach ($tags as $tag)
        {

            $max_id = "";
            $next_results = "";
            Log::info($tag);

            // for ($i=0; $i < 10 ; $i++)
            // do {

                $url = 'https://api.twitter.com/1.1/search/tweets.json';
                // if($i==0)
                // {
                    $getfield = '?q='.$tag.'&count=20&lang=en';
                // } else {
                    if($next_results != "" && !is_null($next_results))
                    {
                        $getfield = $next_results.'&count=20&lang=en';
                    }
                    // else
                    // {
                    //     $getfield = '?q='.$tag.'&count=20&lang=en&max_id='.($max_id+20);
                    // }
                // }
                $requestMethod = 'GET';

                $results = $twitter->setGetfield($getfield)
                    ->buildOauth($url, $requestMethod)
                    ->performRequest();

                // Log::info($results);
                $results = json_decode($results);
                
                if(isset($results->statuses))
                {
                    $statuses = $results->statuses;
                    // Log::info($statuses);

                    // $query = array(
                    //   "q" => "news", //#transformers4 since:2014-06-20 until:2014-07-09
                    //   "count" => 100,
                    //   // "result_type" => "mixed",
                    //   // "max_id" => $max_id,
                    //   // "lang" => "en"
                    // );
                    
                    foreach ($statuses as $result)
                    {

                        Log::info(json_encode($result));

                        if(is_null(Tweet::where('twitter_id' , $result->id)->first())) 
                        {
                            $tweet = new Tweet;

                            $tweet->tag = $tag;
                            $tweet->twitter_created_at = $result->created_at;
                            $tweet->favorite_count == $result->favorite_count;
                            $tweet->favorited = $result->favorited;
                            if(isset($result->geo) && !is_null($result->geo))
                            {
                                $tweet->geo = $result->geo->coordinates[0] . ',' . $result->geo->coordinates[1];
                            }
                            $tweet->text = $result->text;
                            if(isset($result->id_str))
                            {
                                $tweet->id_str = $result->id_str;
                            }
                            $tweet->twitter_id = $result->id;
                            $tweet->in_reply_to_screen_name = $result->in_reply_to_screen_name;
                            $tweet->in_reply_to_status_id = $result->in_reply_to_status_id;
                            $tweet->in_reply_to_status_id_str = $result->in_reply_to_status_id_str;
                            $tweet->in_reply_to_user_id = $result->in_reply_to_user_id;
                            $tweet->in_reply_to_user_id_str = $result->in_reply_to_user_id_str;
                            $tweet->is_quote_status = $result->is_quote_status;
                            $tweet->lang = $result->lang;
                            $place = $result->place;
                            if(isset($place))
                            {
                                $tweet->place = $place->place_type;
                                $tweet->place_name = $place->name;
                                $tweet->country = $place->country;
                                $tweet->country_code = $place->country_code;
                                $tweet->place_full_name = $place->full_name;
                                $tweet->place_coordinates = $place->bounding_box->coordinates[0][0][0] . ',' . $place->bounding_box->coordinates[0][0][1];
                            }
                            if(isset($result->possibly_sensitive))
                            {
                                $tweet->possibly_sensitive = $result->possibly_sensitive;
                            }
                            $tweet->retweeted = $result->retweeted;
                            $tweet->retweet_count = $result->retweet_count;
                            $tweet->source = $result->source;
                            $tweet->contributors = $result->contributors;
                            if(isset($result->coordinates) && !is_null($result->coordinates))
                            {
                                $tweet->coordinates = $result->coordinates->coordinates[0] . ',' . $result->coordinates->coordinates[1];
                            }
                            // $tweet->user = $result->user;
                            // $tweet->entities = $result->entities;

                            $tweet->save();
                        }
                    }
                    Log::info(json_encode($results->search_metadata));
                    if(isset($results->search_metadata->next_results))
                    {
                        $next_results = $results->search_metadata->next_results;
                    } else
                    {
                        $max_id = $results->search_metadata->max_id; // Set max_id for the next search result page
                        Log::info($max_id);
                    }

                }
            // } while ($next_results != '');
        }

    }
}
