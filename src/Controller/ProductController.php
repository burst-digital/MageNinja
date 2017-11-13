<?php

namespace Drupal\mage_ninja\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\mage_ninja\Api\Api;
use Drupal\mage_ninja\Api\JsonExceptionResponse;
use Drupal\mage_ninja\Api\SearchCriteriaBuilder;
use Drupal\mage_ninja\Entity\MageNinjaProduct;
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
   * @return \Drupal\mage_ninja\Api\JsonExceptionResponse|\Symfony\Component\HttpFoundation\JsonResponse
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

      $endpoint = 'V1/products' . $searchCriteria;
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
      // TODO: Check for errors like 401 Unauthorized (instead of "importing" product with ID of 401)

      $processedProductsCount = 0;
      $createdProductsCount = 0;
      $deletedProductsCount = 0; // TODO: implement deleting products that exist in Drupal but not in Magento
      foreach($productIds as $productId) {
        $processedProductsCount++;

        /** @var MageNinjaProduct $productEntity */
        $productEntity = \Drupal::entityQuery('MageNinja_product')
          ->condition('reference_id', $productId)
          ->execute();

        if(empty($productEntity)) {
          MageNinjaProduct::create([
            'reference_id' => $productId
          ])->save();

          $createdProductsCount++;
        }
      }

      \Drupal::logger('mage_ninja')->debug('Import: ' . $processedProductsCount . ' products processed.');
      \Drupal::logger('mage_ninja')->info('Import: ' . $createdProductsCount . ' products imported.');
      \Drupal::logger('mage_ninja')->notice('Import: ' . $deletedProductsCount . ' products deleted.');

//      TODO: Don't import in one big request, but in batches
//      /** @var \Drupal\Core\Queue\QueueInterface $queue */
//      $queue = \Drupal::queue('MageNinja_product_import', TRUE);
//
//      $queue->createItem($productIds);

      return new RedirectResponse('/');
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }

  public function importById($id) {
    try {
      $productEntity = \Drupal::entityQuery('mage_ninja_product')
        ->condition('reference_id', $id)
        ->execute();

      if (empty($productEntity)) {
        $productEntity = MageNinjaProduct::create([
          'reference_id' => $id
        ]);
        $productEntity->save();
      }

      \Drupal::logger('mage_ninja')->info('Import: product with ID: \'' . $id . '\' imported.');

      return new RedirectResponse('/');
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }

  /**
   * Get all product Ids from Magento
   *
   * @return \Drupal\mage_ninja\Api\JsonExceptionResponse|\Symfony\Component\HttpFoundation\JsonResponse
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