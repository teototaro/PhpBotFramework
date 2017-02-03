<?php

/*
 * This file is part of the PhpBotFramework.
 *
 * PhpBotFramework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, version 3.
 *
 * PhpBotFramework is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PhpBotFramework\Core;

use \PhpBotFramework\Exceptions\BotException;

use \PhpBotFramework\Entities\InlineKeyboard;

/**
 * \mainpage
 * \section Description
 * PhpBotFramework is a lightweight framework for [Telegram Bot API](https://core.telegram.org/bots/api).
 * Designed to be fast and easy to use, it provides all the features a user need in order to start
 * developing Telegram bots..
 *
 * \section Installation
 * You can install PhpBotFramework using **Composer**.
 *
 * Go to your project's folder and type:
 *
 *     composer require danyspin97/php-bot-framework
 *     composer install --no-dev
 *
 * \section Usage
 * You can start working on your bot creating a new instance of Bot or by creating a
 * class that inherits from it.
 *
 * Each API call will have <code>$_chat_id</code> set to the current user:
 * you can use CoreBot::setChatID() to change it.
 *
 * Below an example bot you can look to:
 *
 *
 *     <?php
 *
 *     // Include the framework
 *     require './vendor/autoload.php';
 *
 *     // Create the bot
 *     $bot = new PhpBotFramework\Bot("token");
 *
 *     // Add a command that will be triggered every time the user send /start
 *     $bot->addMessageCommand("start", function($bot, $message) {
 *         $bot->sendMessage("Hello, folks!");
 *     });
 *
 *     // Receive updates from Telegram using getUpdates
 *     $bot->getUpdatesLocal();
 *
 * \subsection Bot-Intherited Inheriting by Bot class
 *
 *     <?php
 *
 *     // Include the framework
 *     require './vendor/autoload.php';
 *
 *     // Create the class that will extends Bot class
 *     class EchoBot extends PhpBotFramework\Bot {
 *
 *         // Add the function for processing messages
 *         protected function processMessage($message) {
 *
 *             // Answer each message with the text received
 *             $this->sendMessage($message['text']);
 *         }
 *     }
 *
 *     $bot = new EchoBot("token");
 *
 *     // Process updates using webhook
 *     $bot->processWebhookUpdate();
 *
 * Override these method to make your bot handle each update type:
 * - Bot::processMessage($message)
 * - Bot::processCallbackQuery($callback_query)
 * - Bot::processInlineQuery($inline_query)
 * - Bot::processChosenInlineResult($_chosen_inline_result)
 * - Bot::processEditedMessage($edited_message)
 * - Bot::processChannelPost($post)
 * - Bot::processEditedChannelPost($edited_post)
 *
 * \section Features
 * - Modular: take only what you need
 * - Flexible HTTP requests with [Guzzle](https://github.com/guzzle/guzzle)
 * - Designed to be fast and easy to use
 * - Support for local updates and webhooks
 * - Support for the most important API methods
 * - Command-handle system for messages and callback queries
 * - Update type based processing
 * - Easy **inline keyboard** creation
 * - Inline query results handler
 * - Database support and facilities
 * - Redis support
 * - Support for multilanguage bots
 * - Support for bot states
 * - Highly-documented
 *
 * \section Requirements
 * - PHP >= 7.0
 * - php-mbstring
 * - Composer (to install the framework)
 * - Web server: *required for webhook* (we recommend [nginx](http://nginx.org/))
 * - SSL certificate: *required for webhook* (follow [these steps](https://devcenter.heroku.com/articles/ssl-certificate-self) to make a self-signed certificate or use [Let's Encrypt](https://letsencrypt.org/))
 *
 * \section GetUpdates-section Getting updates
 * Everytime a user interacts with the bot, an `update` is generated by Telegram's servers.
 *
 * There are two ways of receiving this updates:
 * - use [Telegram Bot API's `getUpdates`](https://core.telegram.org/bots/api#getupdates) method
 * - use webhooks (it's covered in the next section)
 *
 * If you want to use `getUpdates` in order to receive updates,
 * add one of these functions at the end of your bot:
 * - Bot::getUpdatesLocal()
 * - Bot::getUpdatesDatabase()
 * - Bot::getUpdatesRedis()
 *
 * The bot will process updates one a time and will call Bot::processUpdate() for each.
 *
 * The connection will be opened at the creation and used for the entire life of the bot.
 *
 * \section Webhook-section Webhook
 * An alternative way to receive updates is using **webhooks**.
 *
 * Everytime a user interacts with the bot, Telegram servers send the update through
 * a POST request to a URL chose by you.
 *
 * A web server will create an instance of the bot for every update received.
 *
 * If you want to use webhook: call Bot::processWebhookUpdate() at the end of your bot.
 *
 * The bot will get data from <code>php://input</code> and process it using Bot::processUpdate().
 * Each instance of the bot will open its connection.
 *
 * \subsection Setwebhooks-subsection Set webhook
 * You can set a URL for your bot's webhook using CoreBot::setWebhook():
 *
 *     //...
 *     $bot->setWebhook([ 'url' => 'https://example.com/mybotSECRETPATH' ])
 *
 * You can learn more about `setWebhook` and webhooks [here](https://core.telegram.org/bots/api#setwebhook).
 *
 * \section Message-commands Bot's commands
 * One of the most important tasks during a Telegram bot's development is register
 * the commands the bot will respond to.
 *
 * PhpBotFrameworks makes it easy:
 *
 *     $bot->addMessageCommand("start", function($bot, $message) {
 *         $bot->sendMessage("I am your personal bot, try /help command");
 *     });
 *
 *     $help_function = function($bot, $message) {
 *         $bot->sendMessage("This is the help message")
 *     };
 *
 *     $bot->addMessageCommand("/help", $help_function);
 *
 * \subsection Bot-commands-regex Check commands using regex
 *
 * You can also use **regular expressions** to check for the given command:
 *
 *     $bot->addMessageCommandRegex("number\d", $help_function);
 *
 * The closure will be called when the user send a command that match the given regex,
 * in this example: both <code>/number1</code> or <code>/number135</code>.
 *
 * \subsection Callback-commands Callback commands
 * You can also check for a callback query containing a particular string as data:
 *
 *     $bot->addCallbackCommand("back", function($bot, $callback_query) {
 *         $bot->editMessageText($callback_query['message']['message_id'], "You pressed back");
 *     });
 *
 * You should absolutely check Bot::addCallbackCommand() for learning more.
 *
 * \section InlineKeyboard-Usage Inline keyboards
 *
 * Telegram implements something called [inline keyboards](https://core.telegram.org/bots#inline-keyboards-and-on-the-fly-updating) which allows users to send commands to a
 * bot tapping on buttons instead of typing text.
 *
 * PhpBotFrameworks supports **inline keyboard** and you can easily integrate it with your bot:
 *
 *     $bot = new PhpBotFramework\Bot("token");
 *
 *     $command_function = function($bot, $message) {
 *         // Add a button to the inline keyboard with written 'Click me!' and
 *         // that open the Telegram site if pressed.
 *         $bot->inline_keyboard->addLevelButtons([
 *             'text' => 'Click me!',
 *             'url' => 'telegram.me'
 *         ]);
 *
 *         // Then send a message, with our keyboard in the parameter $reply_markup of sendMessage
 *         $bot->sendMessage("This is a test message", $bot->inline_keyboard->get());
 *     };
 *
 *     // Add the command
 *     $bot->addMessageCommand("start", $command_function);
 *
 * \section Sql-Database Database
 * A database is required in order to save offsets (if you use local updates)
 * and save user's language.
 *
 * We implemented a simpler way to connect to a database which is based on PDO:
 *
 *     $bot->connect([
 *         'adapter' => 'pgsql',
 *         'username' => 'sysuser',
 *         'password' => 'myshinypassword',
 *         'dbname' => 'my_shiny_bot'
 *     ]);
 *
 * This method will istantiate a new PDO connection and a new PDO object you can
 * access through `$bot->pdo`.
 *
 * If no adapter and host are specified: `mysql` and `localhost` are assigned.
 *
 * \subsection Redis-database Redis
 * **Redis** is used across PhpBotFramework in order to save offsets for local updates,
 * to store user's language (both as cache and persistent) and save bot states.
 *
 * Redis and the main database are complementary so you need to set both.
 *
 * All you need to do, in order to enable Redis for your bot, is create a new Redis object:
 *
 *     $bot->redis = new Redis();
 *
 * \section Multilanguage-section Multi-language Bot
 * This framework offers methods and facilities for develop a multi-language bot.
 *
 * All you need to do is create a `localization` folder in your project's root folder
 * and store there the JSON files with bot's messages:
 *
 * <code>localization/en.json</code>:
 *
 *     { "Welcome_Message": "Hello, folks!" }
 *
 * <code>localization/it.json</code>:
 *
 *     { "Welcome_Message": "Ciao, gente!" }
 *
 * <code>main.php</code>:
 *
 *     // ...
 *     // Load JSON files
 *     $bot->loadLocalization();
 *
 *     $start_function = function($bot, $message) {
 *         // Fetch user's language from database
 *         $user_language = $bot->getLanguageDatabase();
 *         $bot->sendMessage($this->localization[$user_language]['Greetings_Msg']);
 *     };
 *
 *     $bot->addMessageCommand("start", $start_function);
 *
 * So you can have a wonderful (multi-language) bot with a small effort.
 *
 * \section Source
 * **PhpBotFramework** is an open-source project so everyone can contribute to it.
 *
 * It's currently hosted on GitHub [here](https://github.com/DanySpin97/PhpBotFramework).
 *
 * \section Createdwith-section Made with PhpBotFramework
 *
 * - [MyAddressBookBot](https://github.com/DanySpin97/MyAddressBookBot): [Try it on Telegram](https://telegram.me/myaddressbookbot)
 * - [Giveaways_Bot](https://github.com/DanySpin97/GiveawaysBot): [Try it on Telegram](https://telegram.me/giveaways_bot)
 *
 * \section Testing
 * PhpBotFramework comes with a test suite you can run using **PHPUnit**.
 *
 * You need a valid bot token and chat ID in order to run tests:
 *
 *      export BOT_TOKEN=YOURBOTTOKEN
 *      export CHAT_ID=YOURCHATID
 *
 * After you've set the necessary, you can run the test suite typing:
 *
 *      phpunit
 *
 * \section Authors
 * This framework is developed and mantained by [Danilo Spinella](https://github.com/DanySpin97).
 *
 * \section License
 * PhpBotFramework is released under [GNU Lesser General Public License v3](https://www.gnu.org/licenses/lgpl-3.0.en.html).
 *
 * You may copy, distribute and modify the software provided that modifications are described and licensed for free under LGPL-3.
 *
 * Derivatives works (including modifications) can only be redistributed under LGPL-3, but applications that use the wrapper don't have to be.
 *
 */

/**
 * \addtogroup Core Core(Internal)
 * \brief Core of the framework.
 * @{
 */

/**
 * \class CoreBot
 * \brief Core of the framework
 * \details Contains data used by the bot to works, curl request handling, and all api methods (sendMessage, editMessageText, etc).
 */
class CoreBot
{
    /** @} */

    use Updates,
        Send,
        Edit,
        Inline,
        Chat;

    /**
     * \addtogroup Bot Bot
     * @{
     */

    /**
     * \addtogroup Core Core(Internal)
     * \brief Core of the framework.
     * @{
     */

    /** \brief Chat_id of the user that interacted with the bot. */
    protected $_chat_id;

    /** \brief Bot id. */
    protected $_bot_id;

    /** \brief Url request (containing $token). */
    protected $_api_url;

    /** \brief Implements interface for execute HTTP requests. */
    protected $_http;

    /**
     * \brief Initialize a new bot.
     * \details Initialize a new bot passing its token.
     * @param $token Bot's token given by @botfather.
     */
    public function __construct(string $token)
    {
        // Check token is valid
        if (is_numeric($token) || $token === '') {
            throw new BotException('Token is not valid or empty');
        }

        $this->_api_url = "https://api.telegram.org/bot$token/";

        // Init connection and config it
        $this->_http = new \GuzzleHttp\Client([
            'base_uri' => $this->_api_url,
            'connect_timeout' => 5,
            'verify' => false,
            'timeout' => 60,
            'http_errors' => false
        ]);
    }

    /** @} */

    /**
     * \addtogroup Bot Bot
     * @{
     */

    /**
     * \brief Get chat ID of the current user.
     * @return int Chat ID of the user.
     */
    public function getChatID()
    {
        return $this->_chat_id;
    }

    /**
     * \brief Set current chat ID.
     * \details Change the chat ID on which the bot acts.
     * @param $chat_id The new chat ID to set.
     */
    public function setChatID($chat_id)
    {
        $this->_chat_id = $chat_id;
    }

    /**
     * \brief Get bot ID using `getMe` method.
     * @return int Bot id, 0 on errors.
     */
    public function getBotID() : int
    {
        // If it is not valid
        if (!isset($this->_bot_id) || $this->_bot_id == 0) {
            // get it again
            $this->_bot_id = ($this->getMe())['id'];
        }

        return $this->_bot_id ?? 0;
    }

    /** @} */

    /**
     * \addtogroup Api Api Methods
     * \brief Implementations for Telegram Bot API's methods.
     * @{
     */

    /**
     * \brief Exec any api request using this method.
     * \details Use this method for custom api calls using this syntax:
     *
     *     $param = [
     *             'chat_id' => $_chat_id,
     *             'text' => 'Hello!'
     *     ];
     *     apiRequest("sendMessage", $param);
     *
     * @param $method The method to call.
     * @param $parameters Parameters to add.
     * @return Depends on api method.
     */
    public function apiRequest(string $method, array $parameters)
    {
        return $this->execRequest($method . '?' . http_build_query($parameters));
    }

    /** @} */

    /**
     * \addtogroup Core Core(internal)
     * @{
     */

    /**
     * \brief Process an api method by taking method and parameter.
     * \details optionally create a object of $class class name with the response as constructor param.
     * @param string $method Method to call.
     * @param array $param Parameter for the method.
     * @param string $class Class name of the object to create using response.
     * @return mixed Response or object of $class class name.
     */
    protected function processRequest(string $method, array $param, string $class = '')
    {
        $response = $this->execRequest("$method?" . http_build_query($param));

        if ($response === false) {
            return false;
        }

        if ($class !== '') {
            $object_class = "PhpBotFramework\Entities\\$class";

            return new $object_class($response);
        }

        return $response;
    }


    /** \brief Core function to execute HTTP request.
     * @param $url The request's URL.
     * @return Array|false Url response decoded from JSON, false on error.
     */
    protected function execRequest(string $url)
    {
        $response = $this->_http->request('POST', $url);
        $http_code = $response->getStatusCode();

        if ($http_code === 200) {
            $response = json_decode($response->getBody(), true);

            return $response['result'];
        } elseif ($http_code >= 500) {
            // do not wat to DDOS server if something goes wrong
            sleep(10);
            return false;
        } else {
            $response = json_decode($response->getBody(), true);
            error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
            return false;
        }
    }

    /** @} */

    /** @} */
}