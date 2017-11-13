<?php

namespace Drupal\mage_ninja\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\mage_ninja\Entity\MageNinjaProduct;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Imports all Magento products into Drupal.
 *
 * @QueueWorker(
 *   id = "mage_ninja_product_import"
 *   title = @Translation("Headless Magento Connection: product import")
 *   cron = {"time" = 60}
 * )
 */
class ImportQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    foreach($data as $productId) {
      MageNinjaProduct::create([
        'reference_id' => $productId
      ])->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    // TODO: Implement create() method.
  }
}