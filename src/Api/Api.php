<?php

namespace Drupal\mage_ninja\Api;

use GuzzleHttp\Client;

class Api {
  private static $client = NULL;

  /**
   * Singleton pattern for GuzzleHttp\Client
   *
   * @return \GuzzleHttp\Client
   */
  public static function getClient() {
    if (self::$client === NULL) {
      self::$client = new Client([
        'base_uri' => \Drupal::config('mage_ninja.settings')->get('base_uri'),
      ]);
    }

    return self::$client;
  }

  /**
   * Requests a token from the Magento API.
   *
   * @return string
   */
  public static function getAdminToken() {
    $config = \Drupal::config('mage_ninja.settings');

    if ($config->get('admin_token') === null) {
      $client = self::getClient();

      $config = \Drupal::config('mage_ninja.settings');
      $username = $config->get('admin_username'); // TODO: Remove test value 'burst';
      $password = $config->get('admin_password'); // TODO: Remove test value '73xnY83383G6aC68';

      $endpoint = 'V1/integration/admin/token';
      $options = [
        'json' => [
          'username' => $username,
          'password' => $password,
        ],
      ];

      $response = $client->post($endpoint, $options);

      // Trim, because token is returned with surrounding double quotes (i.e.: "thisisatoken").
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

    $response = $client->post($endpoint, $options);

    // Trim, because token is returned with surrounding double quotes (i.e.: "thisisatoken").
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