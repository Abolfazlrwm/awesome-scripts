<?php
/* ==============================
 * 📬 Telegram: @Abolfazl_rwm — ⚠️ استفاده، ویرایش و بازنشر فقط با ذکر منبع مجاز است؛ حذف این اعتبار اجازه نمی‌شود.
 * Use, edit and redistribute only with attribution to @Abolfazl_rwm.
 * ============================== */

$token = "token";
$channel_id = "-1002387961027";

function sendRequest($method, $params = []) {
    global $token;
    $url = "https://api.telegram.org/bot$token/$method";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function ensureUserStatesFolderExists() {
    $folderPath = __DIR__ . '/user_states';
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0755, true);
    }
}

function saveUserState($chat_id, $state) {
    ensureUserStatesFolderExists();
    $filename = __DIR__ . "/user_states/$chat_id.txt";
    file_put_contents($filename, $state);
}

function getUserState($chat_id) {
    $filename = __DIR__ . "/user_states/$chat_id.txt";
    if (file_exists($filename)) {
        return file_get_contents($filename);
    }
    return null;
}

function clearUserState($chat_id) {
    $filename = __DIR__ . "/user_states/$chat_id.txt";
    if (file_exists($filename)) {
        unlink($filename);
    }
}

function ensureBalanceFileExists() {
    $filePath = __DIR__ . '/balance.json';
    if (!file_exists($filePath)) {
        file_put_contents($filePath, json_encode([]));
    }
}

function getUserBalance($chat_id) {
    ensureBalanceFileExists();
    $filePath = __DIR__ . '/balance.json';
    $balances = json_decode(file_get_contents($filePath), true);
    return $balances[$chat_id] ?? [
        'eth' => 0,
        'sol' => 0,
        'ada' => 0,
        'dot' => 0,
        'trx' => 0,
        'avax' => 0,
        'atom' => 0,
        'bnb' => 0,
        'algo' => 0
    ];
}

function saveUserBalance($chat_id, $balances) {
    ensureBalanceFileExists();
    $filePath = __DIR__ . '/balance.json';
    $allBalances = json_decode(file_get_contents($filePath), true);
    $allBalances[$chat_id] = $balances;
    file_put_contents($filePath, json_encode($allBalances));
}

function ensureStakingPlansFileExists() {
    $filePath = __DIR__ . '/staking_plans.json';
    if (!file_exists($filePath)) {
        file_put_contents($filePath, json_encode([]));
    }
}

function getUserStakingPlans($chat_id) {
    ensureStakingPlansFileExists();
    $filePath = __DIR__ . '/staking_plans.json';
    $stakingPlans = json_decode(file_get_contents($filePath), true);
    return $stakingPlans[$chat_id] ?? [];
}

function saveUserStakingPlans($chat_id, $plans) {
    ensureStakingPlansFileExists();
    $filePath = __DIR__ . '/staking_plans.json';
    $allPlans = json_decode(file_get_contents($filePath), true);
    $allPlans[$chat_id] = $plans;
    file_put_contents($filePath, json_encode($allPlans));
}

function ensureWeeklyDataFileExists() {
    $filePath = __DIR__ . '/weekly_data.json';
    if (!file_exists($filePath)) {
        file_put_contents($filePath, json_encode([
            'start_time' => time(),
            'end_time' => time() + (7 * 24 * 60 * 60),
            'referrals' => []
        ]));
    }
}

function resetWeeklyData() {
    $filePath = __DIR__ . '/weekly_data.json';
    file_put_contents($filePath, json_encode([
        'start_time' => time(),
        'end_time' => time() + (7 * 24 * 60 * 60),
        'referrals' => []
    ]));
}

function incrementUserReferrals($chat_id) {
    ensureWeeklyDataFileExists();
    $filePath = __DIR__ . '/weekly_data.json';
    $weeklyData = json_decode(file_get_contents($filePath), true);
    if (!isset($weeklyData['referrals'][$chat_id])) {
        $weeklyData['referrals'][$chat_id] = 0;
    }
    $weeklyData['referrals'][$chat_id]++;
    file_put_contents($filePath, json_encode($weeklyData));
}

function getWeeklyRewardsMessage($chat_id) {
    ensureWeeklyDataFileExists();
    $filePath = __DIR__ . '/weekly_data.json';
    $weeklyData = json_decode(file_get_contents($filePath), true);
    $currentTime = time();
    $endTime = $weeklyData['end_time'];
    $timeRemaining = $endTime - $currentTime;
    if ($timeRemaining <= 0) {
        resetWeeklyData();
        $weeklyData = json_decode(file_get_contents($filePath), true);
        $endTime = $weeklyData['end_time'];
        $timeRemaining = $endTime - $currentTime;
    }
    $daysRemaining = floor($timeRemaining / (60 * 60 * 24));
    $hoursRemaining = floor(($timeRemaining % (60 * 60 * 24)) / (60 * 60));
    $userReferrals = $weeklyData['referrals'][$chat_id] ?? 0;
    $allReferrals = $weeklyData['referrals'];
    arsort($allReferrals);
    $userRank = array_search($chat_id, array_keys($allReferrals)) + 1;
    $referralLink = "https://t.me/StakoraBot?start=$chat_id";
    $message = "🎉 *Weekly Rewards!* 🎉\n\n"
             . "This week’s rewards are as follows:\n"
             . "The person who has the most referrals this week will receive $1000 worth of Solana (SOL)! 💎🚀\n\n"
             . "Your total referrals this week: *$userReferrals*\n"
             . "Your rank this week: *$userRank*\n"
             . "Your unique referral link: `$referralLink`\n\n"
             . "---\n\n"
             . "🏆 *Top 3 Referral Leaders This Week:*\n";
    $top3 = array_slice($allReferrals, 0, 3, true);
    $rank = 1;
    foreach ($top3 as $userId => $referralCount) {
        $message .= "$rank. User $userId - $referralCount referrals\n";
        $rank++;
    }
    $message .= "\n---\n\n"
              . "⏳ *Time remaining in this week’s challenge:* $daysRemaining days and $hoursRemaining hours";
    return $message;
}

function getWalletAddress($currency) {
    $addresses = [
        'ETH' => '0x069AeFbD772E72A93fcC971555d86EECa3791dda',
        'SOL' => '9KMN8jHGJqxf3Umm2eBWbH74s864X2iQXw5bt7ctCCwi',
        'ADA' => 'addr1qywfam6leg72l7sunmqd2fpav6es8n99e2hqm2wuftdqjnf8kc03tyjjdhrrdllw9apg8anlmt8dv54qxwfkq0wzr97qp7vg0u',
        'DOT' => '14oqCqbrSVxA44aJ75JKomZiGwWZu7TnAAadX7YJ7qwihJWj',
        'TRX' => 'TLaurJEVzDwGnvTck2ebB8tTAuinJNxAcg',
        'AVAX' => '0x069AeFbD772E72A93fcC971555d86EECa3791dda',
        'ATOM' => 'cosmos1spx8naj6rt4pn6d62kyetxf299mskzarh56new',
        'BNB' => '0x069AeFbD772E72A93fcC971555d86EECa3791dda',
        'ALGO' => 'ALGO_ADDRESS_HERE'
    ];
    return $addresses[$currency] ?? 'Wallet address not found.';
}

function sendDepositInstructions($chat_id, $amount, $currency) {
    $walletAddress = getWalletAddress($currency);
    $message = "🌟 *You're About to Stake $currency!* 🌟\n\n"
             . "Great choice! $currency is one of the most popular and rewarding assets for staking. Earn passive income by staking your $currency with us and enjoy the benefits of supporting the network! 💎\n\n"
             . "Here’s how to get started:\n\n"
             . "1. You've chosen to stake *$amount USD* worth of $currency.\n\n"
             . "2. Send your $currency to the wallet address below.\n\n"
             . "3. Your staking process will start immediately after the deposit is confirmed! 🔥\n\n"
             . "4. Click the \"Confirm\" button once you’ve sent your $currency. This will verify your deposit and activate your staking. ✅\n\n"
             . "---\n\n"
             . "👇 *Your $currency Deposit Wallet Address:*\n\n"
             . "`$walletAddress`\n\n"
             . "---\n\n"
             . "💡 *Important:*\n\n"
             . "• Your deposit will be confirmed once the transaction is received.\n"
             . "• Make sure to double-check the wallet address before sending.\n\n"
             . "---\n\n"
             . "Click to Confirm Your Deposit:\n"
             . "🔘 [Confirm] ✅";
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => 'Confirm ✅', 'callback_data' => "confirm_$currency"]
            ],
            [
                ['text' => 'Back to Menu 🔙', 'callback_data' => 'back_to_menu']
            ]
        ]
    ];
    sendRequest('sendMessage', [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode($keyboard)
    ]);
}

$update = json_decode(file_get_contents("php://input"), true);

if (isset($update['message']['text']) && strpos($update['message']['text'], '/start') === 0) {
    $chat_id = $update['message']['chat']['id'];
    $text = $update['message']['text'];
    $referrer_id = null;
    if (strpos($text, '/start') === 0) {
        $parts = explode(' ', $text);
        if (count($parts) > 1 && is_numeric($parts[1])) {
            $referrer_id = $parts[1];
        }
    }
    if ($referrer_id && $referrer_id != $chat_id) {
        incrementUserReferrals($referrer_id);
        sendRequest('sendMessage', [
            'chat_id' => $referrer_id,
            'text' => "🎉 *1 new referral added!* 🎉\n\n"
                    . "You have gained 1 referral for this week's challenge! Keep inviting more people to climb the leaderboard! 🚀",
            'parse_mode' => 'Markdown'
        ]);
    }
    $welcome_text = "Hello! 👋\n"
                  . "Welcome to our Staking Bot! 🚀\n"
                  . "Here, you can stake your cryptocurrencies and earn passive income with ease. 💎\n\n"
                  . "✅ Features of the bot:\n\n"
                  . "• Fast and secure staking\n"
                  . "• Real-time earnings tracking\n"
                  . "• Easy withdrawals\n\n"
                  . "To get started, choose one of the options below or try out the commands. If you have any questions, feel free to ask! 🎯\n\n"
                  . "Join us and make smart investments today!";
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => 'Deposit 📥', 'callback_data' => 'deposit'],
                ['text' => 'Withdrawal 📤', 'callback_data' => 'withdrawal']
            ],
            [
                ['text' => 'Staking 🎖', 'callback_data' => 'staking'],
                ['text' => 'Weekly rewards 🎁', 'callback_data' => 'weekly_rewards']
            ],
            [
                ['text' => 'Balance 💰', 'callback_data' => 'balance']
            ]
        ]
    ];
    sendRequest('sendMessage', [
        'chat_id' => $chat_id,
        'text' => $welcome_text,
        'reply_markup' => json_encode($keyboard)
    ]);
}

if (isset($update['callback_query'])) {
    $callback_data = $update['callback_query']['data'];
    $chat_id = $update['callback_query']['message']['chat']['id'];
    $message_id = $update['callback_query']['message']['message_id'];
    switch ($callback_data) {
        case 'deposit':
            $response_text = "📥 Choose a cryptocurrency to deposit\n\n"
                           . "Which cryptocurrency would you like to deposit? 💎\n"
                           . "Select one of the options below to start the deposit process:";
            $crypto_keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '1. Ethereum (ETH) 🌐', 'callback_data' => 'deposit_eth'],
                        ['text' => '2. Solana (SOL) 🔥', 'callback_data' => 'deposit_sol']
                    ],
                    [
                        ['text' => '3. Cardano (ADA) 🌀', 'callback_data' => 'deposit_ada'],
                        ['text' => '4. Polkadot (DOT) ⚡', 'callback_data' => 'deposit_dot']
                    ],
                    [
                        ['text' => '5. TRON (TRX) 🚀', 'callback_data' => 'deposit_trx'],
                        ['text' => '6. Avalanche (AVAX) ❄️', 'callback_data' => 'deposit_avax']
                    ],
                    [
                        ['text' => '7. Cosmos (ATOM) 🌌', 'callback_data' => 'deposit_atom'],
                        ['text' => '8. Binance Coin (BNB) 💼', 'callback_data' => 'deposit_bnb']
                    ],
                    [
                        ['text' => '9. Algorand (ALGO) 🧩', 'callback_data' => 'deposit_algo']
                    ],
                    [
                        ['text' => 'Back to menu 🔙', 'callback_data' => 'back_to_menu']
                    ]
                ]
            ];
            sendRequest('editMessageText', [
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'text' => $response_text,
                'reply_markup' => json_encode($crypto_keyboard)
            ]);
            break;
        case 'deposit_eth':
        case 'deposit_sol':
        case 'deposit_ada':
        case 'deposit_dot':
        case 'deposit_trx':
        case 'deposit_avax':
        case 'deposit_atom':
        case 'deposit_bnb':
        case 'deposit_algo':
            $currency = str_replace('deposit_', '', $callback_data);
            $currency = strtoupper($currency);
            sendRequest('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "💵 Please enter the amount of $currency you want to deposit (in USD).\n\n"
                        . "Minimum: $20\n"
                        . "Maximum: $200,000",
                'parse_mode' => 'Markdown'
            ]);
            saveUserState($chat_id, 'awaiting_deposit_amount_' . strtolower($currency));
            break;
        case strpos($callback_data, 'confirm_') === 0:
            $currency = str_replace('confirm_', '', $callback_data);
            $currency = strtoupper($currency);
            saveUserState($chat_id, 'awaiting_tx_hash_' . $currency);
            sendRequest('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "🔗 Please enter the transaction hash or link for your $currency deposit:",
                'parse_mode' => 'Markdown'
            ]);
            break;
        case 'withdrawal':
            $response_text = "📤 Choose a cryptocurrency to withdraw\n\n"
                           . "Which cryptocurrency would you like to withdraw? 💎\n"
                           . "Select one of the options below to start the withdrawal process:";
            $crypto_keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '1. Ethereum (ETH) 🌐', 'callback_data' => 'withdraw_eth'],
                        ['text' => '2. Solana (SOL) 🔥', 'callback_data' => 'withdraw_sol']
                    ],
                    [
                        ['text' => '3. Cardano (ADA) 🌀', 'callback_data' => 'withdraw_ada'],
                        ['text' => '4. Polkadot (DOT) ⚡', 'callback_data' => 'withdraw_dot']
                    ],
                    [
                        ['text' => '5. TRON (TRX) 🚀', 'callback_data' => 'withdraw_trx'],
                        ['text' => '6. Avalanche (AVAX) ❄️', 'callback_data' => 'withdraw_avax']
                    ],
                    [
                        ['text' => '7. Cosmos (ATOM) 🌌', 'callback_data' => 'withdraw_atom'],
                        ['text' => '8. Binance Coin (BNB) 💼', 'callback_data' => 'withdraw_bnb']
                    ],
                    [
                        ['text' => '9. Algorand (ALGO) 🧩', 'callback_data' => 'withdraw_algo']
                    ],
                    [
                        ['text' => 'Back to menu 🔙', 'callback_data' => 'back_to_menu']
                    ]
                ]
            ];
            sendRequest('editMessageText', [
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'text' => $response_text,
                'reply_markup' => json_encode($crypto_keyboard)
            ]);
            break;
        case 'withdraw_eth':
        case 'withdraw_sol':
        case 'withdraw_ada':
        case 'withdraw_dot':
        case 'withdraw_trx':
        case 'withdraw_avax':
        case 'withdraw_atom':
        case 'withdraw_bnb':
        case 'withdraw_algo':
            $currency = str_replace('withdraw_', '', $callback_data);
            $currency = strtolower($currency);
            saveUserState($chat_id, 'awaiting_withdraw_amount_' . $currency);
            sendRequest('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "💵 Please enter the amount of $currency you want to withdraw (in USD).\n\n"
                        . "Minimum: $100",
                'parse_mode' => 'Markdown'
            ]);
            break;
        case 'staking':
            $welcome_text = "🌟 *Welcome to the Staking Section!* 🌟\n\n"
                          . "Here, you can stake your cryptocurrencies and earn passive income with ease. 💎\n\n"
                          . "Choose one of the options below to get started:";
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Your Active Plans 📊', 'callback_data' => 'active_plans'],
                        ['text' => 'Start Staking 🚀', 'callback_data' => 'start_staking']
                    ],
                    [
                        ['text' => 'About Staking ℹ️', 'callback_data' => 'about_staking']
                    ],
                    [
                        ['text' => 'Back to menu 🔙', 'callback_data' => 'back_to_menu']
                    ]
                ]
            ];
            sendRequest('editMessageText', [
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'text' => $welcome_text,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode($keyboard)
            ]);
            break;
        case 'active_plans':
            $stakingPlans = getUserStakingPlans($chat_id);
            if (empty($stakingPlans)) {
                $response_text = "📊 *Your Active Staking Plans*\n\n"
                               . "You don't have any active staking plans at the moment. Start staking now to earn rewards! 🚀";
            } else {
                $response_text = "📊 *Your Active Staking Plans*\n\n";
                foreach ($stakingPlans as $plan) {
                    $response_text .= "• Currency: {$plan['currency']}\n"
                                   . "• Amount: {$plan['amount']} USD\n"
                                   . "• Duration: {$plan['duration']} days\n"
                                   . "• Estimated Reward: {$plan['reward']} USD\n\n";
                }
            }
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Back to menu 🔙', 'callback_data' => 'back_to_menu']
                    ]
                ]
            ];
            sendRequest('editMessageText', [
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'text' => $response_text,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode($keyboard)
            ]);
            break;
        case 'start_staking':
            $response_text = "🚀 *Start Staking*\n\n"
                           . "Which cryptocurrency would you like to stake? 💎\n"
                           . "Select one of the options below:";
            $crypto_keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '1. Ethereum (ETH) 🌐', 'callback_data' => 'stake_eth'],
                        ['text' => '2. Solana (SOL) 🔥', 'callback_data' => 'stake_sol']
                    ],
                    [
                        ['text' => '3. Cardano (ADA) 🌀', 'callback_data' => 'stake_ada'],
                        ['text' => '4. Polkadot (DOT) ⚡', 'callback_data' => 'stake_dot']
                    ],
                    [
                        ['text' => '5. TRON (TRX) 🚀', 'callback_data' => 'stake_trx'],
                        ['text' => '6. Avalanche (AVAX) ❄️', 'callback_data' => 'stake_avax']
                    ],
                    [
                        ['text' => '7. Cosmos (ATOM) 🌌', 'callback_data' => 'stake_atom'],
                        ['text' => '8. Binance Coin (BNB) 💼', 'callback_data' => 'stake_bnb']
                    ],
                    [
                        ['text' => '9. Algorand (ALGO) 🧩', 'callback_data' => 'stake_algo']
                    ],
                    [
                        ['text' => 'Back to menu 🔙', 'callback_data' => 'back_to_menu']
                    ]
                ]
            ];
            sendRequest('editMessageText', [
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'text' => $response_text,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode($crypto_keyboard)
            ]);
            break;
        case 'stake_eth':
        case 'stake_sol':
        case 'stake_ada':
        case 'stake_dot':
        case 'stake_trx':
        case 'stake_avax':
        case 'stake_atom':
        case 'stake_bnb':
        case 'stake_algo':
            $currency = str_replace('stake_', '', $callback_data);
            $currency = strtoupper($currency);
            $response_text = "🌟 *Staking $currency*\n\n"
                           . "Choose the duration for your staking plan:\n\n"
                           . "7 days = 5% return\n"
                           . "14 days = 12% return\n"
                           . "30 days = 33% return\n"
                           . "120 days = 65% return\n"
                           . "360 days = 95% return";
            $duration_keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '7 Days', 'callback_data' => "stake_duration_7_$currency"],
                        ['text' => '14 Days', 'callback_data' => "stake_duration_14_$currency"]
                    ],
                    [
                        ['text' => '30 Days', 'callback_data' => "stake_duration_30_$currency"],
                        ['text' => '120 Days', 'callback_data' => "stake_duration_120_$currency"]
                    ],
                    [
                        ['text' => '360 Days', 'callback_data' => "stake_duration_360_$currency"]
                    ],
                    [
                        ['text' => 'Back to menu 🔙', 'callback_data' => 'back_to_menu']
                    ]
                ]
            ];
            sendRequest('editMessageText', [
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'text' => $response_text,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode($duration_keyboard)
            ]);
            break;
        case strpos($callback_data, 'stake_duration_') === 0:
            $parts = explode('_', $callback_data);
            $duration = $parts[2];
            $currency = $parts[3];
            $balances = getUserBalance($chat_id);
            $currency_lower = strtolower($currency);
            if ($balances[$currency_lower] >= 20) {
                $reward = calculateReward($duration, $balances[$currency_lower]);
                $stakingPlans = getUserStakingPlans($chat_id);
                $stakingPlans[] = [
                    'currency' => $currency,
                    'amount' => $balances[$currency_lower],
                    'duration' => $duration,
                    'reward' => $reward
                ];
                saveUserStakingPlans($chat_id, $stakingPlans);
                $response_text = "✅ *Staking Confirmed!*\n\n"
                               . "Your $currency has been locked for $duration days. 🚀\n\n"
                               . "Estimated Reward: $reward USD\n\n"
                               . "Thank you for using our staking service! 💎";
            } else {
                $response_text = "❌ *Insufficient Balance!*\n\n"
                               . "Your balance is less than $20. Please deposit more $currency to start staking. 💰";
            }
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Back to menu 🔙', 'callback_data' => 'back_to_menu']
                    ]
                ]
            ];
            sendRequest('editMessageText', [
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'text' => $response_text,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode($keyboard)
            ]);
            break;
        case 'about_staking':
            $about_text = "🌟 *About Staking* 🌟\n\n"
                        . "Staking is a process where you lock your cryptocurrencies to support the network and earn rewards in return. 💎\n\n"
                        . "Here’s how it works:\n\n"
                        . "1. **Choose a cryptocurrency** to stake (e.g., ETH, SOL, ADA).\n"
                        . "2. **Select a duration** for your staking plan (e.g., 7 days, 30 days).\n"
                        . "3. **Lock your funds** and start earning rewards. 🚀\n\n"
                        . "Our staking plans offer competitive returns:\n\n"
                        . "• 7 days = 5% return\n"
                        . "• 14 days = 12% return\n"
                        . "• 30 days = 33% return\n"
                        . "• 120 days = 65% return\n"
                        . "• 360 days = 95% return\n\n"
                        . "Start staking today and grow your crypto portfolio! 💰";
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Back to menu 🔙', 'callback_data' => 'back_to_menu']
                    ]
                ]
            ];
            sendRequest('editMessageText', [
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'text' => $about_text,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode($keyboard)
            ]);
            break;
        case 'weekly_rewards':
            resetWeeklyData();
            $message = getWeeklyRewardsMessage($chat_id);
            sendRequest('sendMessage', [
                'chat_id' => $chat_id,
                'text' => $message,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true
            ]);
            break;
        case 'balance':
            $balances = getUserBalance($chat_id);
            $balance_text = "💰 YOUR ACCOUNT BALANCE:\n\n";
            $has_balance = false;
            foreach ($balances as $currency => $amount) {
                if ($amount > 0) {
                    $balance_text .= "YOUR ACCOUNT BALANCE ($currency): $amount$\n";
                    $has_balance = true;
                }
            }
            if (!$has_balance) {
                $balance_text = "💰 YOUR ACCOUNT BALANCE: 0$";
            }
            sendRequest('sendMessage', [
                'chat_id' => $chat_id,
                'text' => $balance_text,
                'parse_mode' => 'Markdown'
            ]);
            break;
        case 'back_to_menu':
            $response_text = "Hello! 👋\n"
                           . "Welcome back to the main menu! 🚀\n"
                           . "What would you like to do today?";
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Deposit 📥', 'callback_data' => 'deposit'],
                        ['text' => 'Withdrawal 📤', 'callback_data' => 'withdrawal']
                    ],
                    [
                        ['text' => 'Staking 🎖', 'callback_data' => 'staking'],
                        ['text' => 'Weekly rewards 🎁', 'callback_data' => 'weekly_rewards']
                    ],
                    [
                        ['text' => 'Balance 💰', 'callback_data' => 'balance']
                    ]
                ]
            ];
            sendRequest('editMessageText', [
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'text' => $response_text,
                'reply_markup' => json_encode($keyboard)
            ]);
            clearUserState($chat_id);
            break;
        default:
            break;
    }
}

if (isset($update['message']['text'])) {
    $chat_id = $update['message']['chat']['id'];
    $text = $update['message']['text'];
    $user_state = getUserState($chat_id);

    if (strpos($user_state, 'awaiting_deposit_amount_') === 0) {
        $currency = str_replace('awaiting_deposit_amount_', '', $user_state);
        $currency = strtoupper($currency);
        $amount = floatval($text);

        if ($amount >= 20 && $amount <= 200000) {
            sendDepositInstructions($chat_id, $amount, $currency);
        } else {
            sendRequest('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "❌ Invalid amount! Please enter a value between $20 and $200,000.",
                'parse_mode' => 'Markdown'
            ]);
        }
    } elseif (strpos($user_state, 'awaiting_tx_hash_') === 0) {
        $currency = str_replace('awaiting_tx_hash_', '', $user_state);
        $currency = strtoupper($currency);
        $amount_state = str_replace('awaiting_tx_hash_', 'awaiting_deposit_amount_', $user_state);
        $amount = floatval(getUserState($chat_id));
        sendRequest('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "✅ Your deposit has been confirmed!\n\n"
                    . "Your transaction has been received and is being processed. Your balance will be updated within the next 5 minutes.",
            'parse_mode' => 'Markdown'
        ]);
        sendRequest('sendMessage', [
            'chat_id' => $channel_id,
            'text' => "New deposit received!\n\n"
                    . "User ID: $chat_id\n"
                    . "Currency: $currency\n"
                    . "Amount: $amount USD\n"
                    . "Transaction Hash: $text",
            'parse_mode' => 'Markdown'
        ]);
        clearUserState($chat_id);
    }
}

function calculateReward($duration, $amount) {
    $rates = [
        7 => 0.05,
        14 => 0.12,
        30 => 0.33,
        120 => 0.65,
        360 => 0.95
    ];
    return $amount * $rates[$duration];
}
?>