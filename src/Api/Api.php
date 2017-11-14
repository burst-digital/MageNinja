<?php

namespace Drupal\mage_ninja\Api;

use GuzzleHttp\Client;

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
      /** @var \GuzzleHttp\Client client */
      self::$client = new Client([
        'base_uri' => \Drupal::config('mage_ninja.settings')->get('base_uri'),
      ]);
    }

    return self::$client;
  }

  /**
   * Requests an admin token from the Magento API.
   *
   * @return string
   *  The admin token.
   */
  public static function getAdminToken() {
    /** @var \Drupal\Core\Config\ImmutableConfig $config */
    $config = \Drupal::config('mage_ninja.settings');

    if ($config->get('admin_token') === null) {
      /** @var \GuzzleHttp\Client $client */
      $client = self::getClient();

      /** @var string $username */
      $username = $config->get('admin_username'); // TODO: Local test value 'burst';

      /** @var string $password */
      $password = $config->get('admin_password'); // TODO: Local test value '73xnY83383G6aC68';

      $endpoint = 'V1/integration/admin/token';
      $options = [
        'json' => [
          'username' => $username,
          'password' => $password,
        ],
      ];

      /** @var \GuzzleHttp\Psr7\Response $response */
      $response = $client->post($endpoint, $options);

      // Trim, because token is returned with surrounding double quotes (i.e.: "thisisatoken").
      /** @var string $token */
      $token = trim($response->getBody(), '"');

      \Drupal::service('config.factory')->getEditable('mage_ninja.settings')->set('admin_token', $token)->save();
    }

    return $config->get('admin_token');
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