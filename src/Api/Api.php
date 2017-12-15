<?php

namespace Drupal\mage_ninja\Api;

use Drupal\Core\Config\ConfigException;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class Api {
  /**
   * Save the GuzzleHttp\Client created by the getClient() function
   *
   * @static \GuzzleHttp\Client|null
   */
  private static $client = null;

  /**
   * Singleton pattern for GuzzleHttp\Client
   *
   * @return \GuzzleHttp\Client
   */
  public static function getClient() {
    if (self::$client === null) {
      /** @var \Drupal\Core\Config\ImmutableConfig $config */
      $config = \Drupal::config('mage_ninja.settings');

      /** @var HandlerStack $handlerStack */
      $handlerStack = HandlerStack::create();

      try {
        /** @var Oauth1 $middleware */
        $middleware = new Oauth1([
          'consumer_key' => $config->get('oauth_consumer_key'),
          'consumer_secret' => $config->get('oauth_consumer_secret'),
          'verifier' => $config->get('oauth_verifier'),
          'token' => $config->get('oauth_token'),
          'token_secret' => $config->get('oauth_token_secret')
        ]);
        $handlerStack->push($middleware);

        /** @var Client client */
        self::$client = new Client([
          'base_uri' => $config->get('store_base_url'),
          'handler' => $handlerStack,
          'auth' => 'oauth'
        ]);
      } catch(ConfigException $e) {
        \Drupal::logger('mage_ninja')->error('OAuth configuration is not set. Make sure you set up the integration in magento: System > Extensions > Integrations.');
      }
    }

    return self::$client;
  }

  /**
   * Requests a customer token from the Magento API.
   *
   * @param string $username
   *  The customer's username.
   * @param string $password
   *  The customer's password.
   *
   * @return string
   *  The customer token.
   *
   * @throws \GuzzleHttp\Exception\RequestException
   */
  public static function getCustomerToken($username, $password) {
    $client = self::getClient();

    $endpoint = 'V1/integration/customer/token';
    $options = [
      'json' => [
        'username' => $username,
        'password' => $password,
      ],
    ];

    /** @var \GuzzleHttp\Psr7\Response $response */
    $response = $client->post($endpoint, $options);

    // Trim, because token is returned with surrounding double quotes (i.e.: "thisisatoken").
    // TODO: Use decoder instead of trim()
    /** @var string $token */
    $token = trim($response->getBody(), '"');

    return $token;
  }

  /**
   * Returns authentication array that is used to authenticate with
   * the Magento API.
   *
   * @param string $token
   *  The token to authenticate with.
   *
   * @return array
   */
  public static function getAuthHeader($token) {
    $header = ['Authorization' => 'Bearer ' . $token];

    return $header;
  }
}