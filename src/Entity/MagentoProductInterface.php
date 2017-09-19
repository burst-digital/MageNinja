<?php

namespace Drupal\hmc\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface for defining Magento product entities.
 *
 * @ingroup hmc
 */
interface MagentoProductInterface extends  ContentEntityInterface, EntityChangedInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Magento product reference ID.
   *
   * @return int
   *   Reference ID of the Magento product.
   */
  public function getReferenceId();


  /**
   * Sets the Magento product reference ID.
   *
   * @param int $id
   *   The Reference ID.
   *
   * @return \Drupal\hmc\Entity\MagentoProductInterface
   *   The called Magento product entity.
   */
  public function setReferenceId($id);

  /**
   * Gets the Magento product name.
   *
   * @return string
   *   Name of the Magento product.
   */
  public function getName();

  /**
   * Sets the Magento product name.
   *
   * @param string $name
   *   The Magento product name.
   *
   * @return \Drupal\hmc\Entity\MagentoProductInterface
   *   The called Magento product entity.
   */
  public function setName($name);

  /**
   * Gets the Magento product creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Magento product.
   */
  public function getCreatedTime();

  /**
   * Sets the Magento product creation timestamp.
   *
   * @param int $timestamp
   *   The Magento product creation timestamp.
   *
   * @return \Drupal\hmc\Entity\MagentoProductInterface
   *   The called Magento product entity.
   */
  public function setCreatedTime($timestamp);

}
