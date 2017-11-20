<?php

namespace Drupal\mage_ninja\Import;

use Drupal\mage_ninja\Entity\MageNinjaProduct;
use Drupal\mage_ninja\Controller\ProductController;

class Batch {
  public static function process($currentPage, $pageSize) {
    $controller = new ProductController();

    /** @var \Symfony\Component\HttpFoundation\JsonResponse $response */
    $response =  $controller->getByPage($currentPage, $pageSize);

    /** @var \Symfony\Component\Serializer\Encoder\DecoderInterface $decoder */
    $decoder = \Drupal::service('serializer');

    /** @var array $page */
    $page = $decoder->decode($response->getContent(), 'json');

    /** @var int $totalPages */
    // Always round up to make sure pages with less than $pageSize are processed.
    // Read it every page in case the total_count changes.

    /** @var array $batch */
    $items = $page['items'];

    foreach ($items as $item) {
      /** @var MageNinjaProduct $productEntity */
      $productEntity = \Drupal::entityQuery('mage_ninja_product')
        ->condition('reference_id', $item['id'])
        ->execute();

      if (empty($productEntity)) {
        $product = [
          'reference_id' => $item['id'],
          'sku' => $item['sku'],
          'name' => $item['name'],
          'price' => $item['price'],
        ];

        if (isset($item['custom_attributes']['special_price'])) {
          $product['special_price'] = $item['custom_attributes']['special_price'];
        }
        if (isset($item['custom_attributes']['special_from_date'])) {
          $product['special_price_from'] = $item['custom_attributes']['special_from_date'];
        }
        if (isset($item['custom_attributes']['special_to_date'])) {
          $product['special_price_to'] = $item['custom_attributes']['special_to_date'];
        }

        MageNinjaProduct::create($product)->save();
      }
    }
  }
}