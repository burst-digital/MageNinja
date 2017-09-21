<?php

namespace Drupal\hmc\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\hmc\Api\JsonExceptionResponse;
use Drupal\hmc\Api\Api;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends ControllerBase {

  /**
   * Requests a customer token from the Magento API.
   *
   * @param string $username
   *  The customer's username.
   * @param string $password
   *  The customer's password.
   *
   * @return JsonResponse
   */
  public function getCustomerToken($username, $password) {
    try {
      $token = Api::getCustomerToken($username, $password);

      return new JsonResponse(['token' => $token]);
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }

  public function getAdminToken() {
    try {
      $token = Api::getAdminToken();

      return new JsonResponse(['token' => $token]);
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }
}