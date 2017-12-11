<?php

namespace Drupal\mage_ninja\Batch;

use Drupal\mage_ninja\DataModel\Product;
use Drupal\mage_ninja\Entity\MageNinjaProduct;
use Drupal\mage_ninja\Controller\ProductController;

class Import {
  /**
   * Processes a page of items.
   *
   * @param $currentPage
   *  The page number
   *
   * @param $pageSize
   *  The amount of items on the page
   */
  public static function process($currentPage, $pageSize) {
    $controller = new ProductController();

    /** @var \Symfony\Component\HttpFoundation\JsonResponse $response */
    $response = $controller->getByPage($currentPage, $pageSize);

    /** @var \Symfony\Component\Serializer\Encoder\DecoderInterface $decoder */
    $decoder = \Drupal::service('serializer');

    /** @var array $items */
    $items = $decoder->decode($response->getContent(), 'json');

    foreach ($items as $item) {
      /** @var MageNinjaProduct $productEntity */
      $productEntity = \Drupal::entityQuery('mage_ninja_product')
        ->condition('reference_id', $item['id'])
        ->execute();

      if (empty($productEntity)) {
        $product = new Product($item);

        MageNinjaProduct::create($product->toArray())->save();
      }
    }
  }
}