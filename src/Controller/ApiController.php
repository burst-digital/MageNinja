<?php

namespace Drupal\headless_magento_connection\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends ControllerBase {
  /*
   * Magento API local test credentials
   */
  private static $base_uri = 'http://docker.for.mac.localhost/rest/default/';
//  private static $magento_username = 'burst';
//  private static $magento_password = '73xnY83383G6aC68';

  /**
   * @return string
   */
  public function getToken($username, $password) {
    $client = new \GuzzleHttp\Client([
      'base_uri' => self::getBaseUri()
    ]);

    $response = $client->post('V1/integration/admin/token', [
      'Content-Type' => 'application/json',
      'json' => [
        'username' => $username,
        'password' => $password
      ]
    ]);

    // Token is returned with surrounding double quotes (i.e.: "thisisatoken").
    $token = trim($response->getBody(), '"');

    return new JsonResponse(['token' => $token]);
  }

  /**
   * @return string
   */
  public static function getBaseUri() {
    return self::$base_uri;
  }
}