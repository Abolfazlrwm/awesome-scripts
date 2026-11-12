<?php

header('Content-Type: application/json;charset=utf-8');

require_once 'vendor/autoload.php';
require_once 'config.php';


function refreshGuestToken($client) {
    try {
        $request = $client->request('POST', API_BASE_URL . '/1.1/guest/activate.json');
        $response = json_decode($request->getBody(), true);

        if (isset($response['guest_token'])) {
            file_put_contents(GUEST_TOKEN_FILE, $response['guest_token']);
            return $response['guest_token'];
        }
        throw new Exception('Guest token not found in API response');
    } catch (Exception $e) {
        throw new Exception('Error refreshing guest token: ' . $e->getMessage());
    }
}


function get($client, $tweetUrl, $retry = true) {
    $guestToken = getGuestToken($client);
    $tweetId = extractTweetIdFromUrl($tweetUrl);

    if (!$tweetId) {
        throw new Exception('Invalid tweet URL');
    }

    $url = API_BASE_URL . '/graphql/wqi5M7wZ7tW-X9S2t-Mqcg/TweetResultByRestId';

    $variables = [
        "tweetId" => $tweetId,
        "withCommunity" => false,
        "includePromotedContent" => false,
        "withVoice" => false
    ];

    $features = [
        "creator_subscriptions_tweet_preview_api_enabled" => true,
        "premium_content_api_read_enabled" => false,
        "communities_web_enable_tweet_community_results_fetch" => true,
        "c9s_tweet_anatomy_moderator_badge_enabled" => true,
        "responsive_web_grok_analyze_button_fetch_trends_enabled" => false,
        "responsive_web_grok_analyze_post_followups_enabled" => false,
        "responsive_web_jetfuel_frame" => true,
        "responsive_web_grok_share_attachment_enabled" => true,
        "articles_preview_enabled" => true,
        "responsive_web_edit_tweet_api_enabled" => true,
        "graphql_is_translatable_rweb_tweet_is_translatable_enabled" => true,
        "view_counts_everywhere_api_enabled" => true,
        "longform_notetweets_consumption_enabled" => true,
        "responsive_web_twitter_article_tweet_consumption_enabled" => true,
        "tweet_awards_web_tipping_enabled" => false,
        "responsive_web_grok_show_grok_translated_post" => false,
        "responsive_web_grok_analysis_button_from_backend" => true,
        "creator_subscriptions_quote_tweet_preview_enabled" => false,
        "freedom_of_speech_not_reach_fetch_enabled" => true,
        "standardized_nudges_misinfo" => true,
        "tweet_with_visibility_results_prefer_gql_limited_actions_policy_enabled" => true,
        "longform_notetweets_rich_text_read_enabled" => true,
        "longform_notetweets_inline_media_enabled" => true,
        "payments_enabled" => false,
        "profile_label_improvements_pcf_label_in_post_enabled" => true,
        "rweb_tipjar_consumption_enabled" => true,
        "verified_phone_label_enabled" => false,
        "responsive_web_grok_image_annotation_enabled" => true,
        "responsive_web_grok_imagine_annotation_enabled" => true,
        "responsive_web_grok_community_note_auto_translation_is_enabled" => false,
        "responsive_web_graphql_skip_user_profile_image_extensions_enabled" => false,
        "responsive_web_graphql_timeline_navigation_enabled" => true,
        "responsive_web_enhance_cards_enabled" => false
    ];

    $fieldToggles = [
        "withArticleRichContentState" => true,
        "withArticlePlainText" => false,
        "withGrokAnalyze" => false,
        "withDisallowedReplyControls" => false
    ];

    try {
        $response = $client->request('GET', $url, [
            'query' => [
                "variables" => json_encode($variables),
                "features" => json_encode($features),
                "fieldToggles" => json_encode($fieldToggles)
            ],
            'headers' => [
                'x-guest-token' => $guestToken
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);

    } catch (GuzzleHttp\Exception\ClientException $e) {
        if ($retry && $e->getResponse()->getStatusCode() === 403) {
            $guestToken = refreshGuestToken($client);
            return get($client, $tweetUrl, false);
        }
        throw $e;
    }
}

function parse($responseData) {
    $tweet = $responseData['data']['tweetResult']['result'] ?? null;

    if (!$tweet) {
        throw new Exception('Tweet not found in response');
    }

    $user = $tweet['core']['user_results']['result'] ?? null;
    $legacy = $tweet['legacy'] ?? [];

    $cleanText = $legacy['full_text'] ?? "";

    if (!empty($legacy['entities']['urls'])) {
        foreach ($legacy['entities']['urls'] as $u) {
            if (isset($u['url'])) {
                $cleanText = str_replace($u['url'], '', $cleanText);
            }
        }
    }

    if (!empty($legacy['entities']['media'])) {
        foreach ($legacy['entities']['media'] as $m) {
            if (isset($m['url'])) {
                $cleanText = str_replace($m['url'], '', $cleanText);
            }
        }
    }

    $cleanText = trim($cleanText);

    $userInfo = [
        "name" => $user['core']['name'] ?? "",
        "username" => $user['core']['screen_name'] ?? "",
        "avatar" => str_replace('_normal.', '.', $user['avatar']['image_url']) ?? "",
        "banner" => $user['legacy']['profile_banner_url'] ?? "",
        "bio" => $user['legacy']['description'] ?? "",
        "followers" => $user['legacy']['followers_count'] ?? 0,
        "following" => $user['legacy']['friends_count'] ?? 0,
        "verified" => $user['is_blue_verified'] ?? false,
        "profile_url" => "https://x.com/" . ($user['core']['screen_name'] ?? "")
    ];

    $medias = [];
    if (!empty($legacy['extended_entities']['media'])) {
        foreach ($legacy['extended_entities']['media'] as $m) {
            $item = ["type" => $m['type']];

            if ($m['type'] === 'photo') {
                $item['url'] = $m['media_url_https'];
            } else {
                $item['type'] = 'video';
                $variants = $m['video_info']['variants'] ?? [];
                $best = null;
                foreach ($variants as $v) {
                    if (isset($v['bitrate']) && ($best === null || $v['bitrate'] > $best['bitrate'])) {
                        $best = $v;
                    }
                }
                $item['url'] = $best['url'] ?? "";
                $item['thumbnail'] = $m['media_url_https'] ?? "";
            }

            $medias[] = $item;
        }
    }

    return [
        "text" => $cleanText,
        "created_at" => $legacy['created_at'] ?? "",
        "lang" => $legacy['lang'] ?? "",
        "likes" => $legacy['favorite_count'] ?? 0,
        "retweets" => $legacy['retweet_count'] ?? 0,
        "replies" => $legacy['reply_count'] ?? 0,
        "quotes" => $legacy['quote_count'] ?? 0,
        "views" => $tweet['views']['count'] ?? 0,
        "user_info" => $userInfo,
        "medias" => $medias,
        "tweet_url" => "https://x.com/{$userInfo['username']}/status/{$legacy['id_str']}"
    ];
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (isset($_GET['url'])) {

        if (!empty($_GET['url'])) {
            if (isValidTwitterUrl($_GET['url'])) {

                $client = Client();
                $responseData = get($client, $_GET['url']);

                if (isset($responseData['data']['tweetResult']['result'])) {
                    $clean = parse($responseData);
                    echo json_encode([
                        'status' => 200,
                        'developer' => 't.me/Mr_Kigga',
                        'data' => $clean
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                } else {
                    echo json_encode([
                        'message' => 'failed to fetch twitter url'
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }
            } else {
                echo json_encode([
                    'message' => 'is not valid twitter url'
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode([
                'message' => 'is empty url'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }


    } else {
        echo json_encode([
            'message' => 'is not set query url'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

} else {
    echo json_encode([
        'message' => 'is not request method get'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}