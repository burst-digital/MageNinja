<?php

namespace Drupal\hmc\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\hmc\Api\Api;
use Drupal\hmc\Api\JsonExceptionResponse;
use Drupal\hmc\Api\SearchCriteriaBuilder;
use Drupal\hmc\Entity\HmcProduct;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProductController extends ControllerBase {
  /**
   * Returns the product from the Magento API.
   *
   * @param int $id
   *  The id of the product in Magento.
   *
   * @return JsonResponse
   */
  public function getById($id) {
    try {
      $client = Api::getClient();
      $token = Api::getAdminToken();
      $authHeader = Api::getAuthHeader($token);

      $endpoint = 'V1/products/id/' . $id;
      $options = [
        'headers' => $authHeader
      ];

      $response = $client->get($endpoint, $options);
      $product = json_decode($response->getBody());

      return new JsonResponse($product);
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }

  /**
   * Get a subset of products from Magento.
   *
   * @param int $pageSize
   * @param int $currentPage
   *
   * @return \Drupal\hmc\Api\JsonExceptionResponse|\Symfony\Component\HttpFoundation\JsonResponse
   */
  public function getByPage($currentPage, $pageSize) {
    try {
      $client = Api::getClient();
      $token = Api::getAdminToken();
      $authHeader = Api::getAuthHeader($token);

      $searchCriteria = new SearchCriteriaBuilder();
      $searchCriteria
        ->add(['[pageSize]' => $pageSize])
        ->add(['[currentPage]' => $currentPage]);

      $endpoint = 'V1/products?' . $searchCriteria;
      $options = [
        'headers' => $authHeader
      ];

      $response = $client->get($endpoint, $options);
      $products = json_decode($response->getBody());

      return new JsonResponse($products);
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }

  public function import() {
    try {
      /** @var array $productCount */
      $productIds = json_decode($this->getAllIds()->getContent());

      $processedProductsCount = 0;
      $createdProductsCount = 0;
      $deletedProductsCount = 0; // TODO: implement deleting products that exist in Drupal but not in Magento
      foreach($productIds as $productId) {
        $processedProductsCount++;

        /** @var HmcProduct $productEntity */
        $productEntity = \Drupal::entityQuery('hmc_product')
          ->condition('reference_id', $productId)
          ->execute();

        if(empty($productEntity)) {
          HmcProduct::create([
            'reference_id' => $productId
          ])->save();

          $createdProductsCount++;
        }
      }

      \Drupal::logger('hmc')->debug('Import: ' . $processedProductsCount . ' products processed.');
      \Drupal::logger('hmc')->info('Import: ' . $createdProductsCount . ' products imported.');
      \Drupal::logger('hmc')->notice('Import: ' . $deletedProductsCount . ' products deleted.');

//      TODO: Don't import in one big request, but in batches
//      /** @var \Drupal\Core\Queue\QueueInterface $queue */
//      $queue = \Drupal::queue('hmc_product_import', TRUE);
//
//      $queue->createItem($productIds);

      return new RedirectResponse('/');
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }

  /**
   * Get all product Ids from Magento
   *
   * @return \Drupal\hmc\Api\JsonExceptionResponse|\Symfony\Component\HttpFoundation\JsonResponse
   */
  public function getAllIds() {
    try {
      $client = Api::getClient();
      $token = Api::getAdminToken();
      $authHeader = Api::getAuthHeader($token);

      $endpoint = 'V1/catalog/products';
      $options = [
        'headers' => $authHeader
      ];

      $response = $client->get($endpoint, $options);
      $productIds = json_decode($response->getBody());

      return new JsonResponse($productIds);
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }
}