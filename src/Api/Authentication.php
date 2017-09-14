<?php

namespace Drupal\hmc\Api;

use GuzzleHttp\Client;

class Authentication {

  // TODO: Replace with base_uri from Drupal admin
  private static $base_uri = 'http://docker.for.mac.localhost/rest/default/';

  private static $client = NULL;

  private static $token = NULL;

  /**
   * Singleton pattern for GuzzleHttp\Client
   *
   * @return \GuzzleHttp\Client
   */
  public static function getClient() {
    if (self::$client === NULL) {
      self::$client = new Client([
        'base_uri' => self::getBaseUri(),
      ]);
    }

    return self::$client;
  }

  /**
   * Returns the Magento API base uri.
   *
   * @return string
   */
  public static function getBaseUri() {
    return self::$base_uri;
  }

  /**
   * Requests a token from the Magento API.
   *
   * @return string
   */
  public static function getAdminToken() {
    if (self::$token === NULL) {
      $client = self::getClient();

      // TODO: Replace with credentials from Drupal admin
      $username = 'burst';
      $password = '73xnY83383G6aC68';

      $endpoint = 'V1/integration/admin/token';
      $options = [
        'Content-Type' => 'application/json',
        'json' => [
          'username' => $username,
          'password' => $password,
        ],
      ];

      $response = $client->post($endpoint, $options);

      // Trim, because token is returned with surrounding double quotes (i.e.: "thisisatoken").
      self::$token = trim($response->getBody(), '"');
    }

    return self::$token;
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
      'Content-Type' => 'application/json',
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
  public static function getAuthentication($token) {
    $authentication = ['Authorization' => 'Bearer ' . $token];

    return $authentication;
  }
}