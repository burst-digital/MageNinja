<?php

namespace Drupal\hmc\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface for defining hmc product entities.
 *
 * @ingroup hmc
 */
interface HmcProductInterface extends  ContentEntityInterface, EntityChangedInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the hmc product reference ID.
   *
   * @return int
   *   Reference ID of the hmc product.
   */
  public function getReferenceId();


  /**
   * Sets the hmc product reference ID.
   *
   * @param int $id
   *   The Reference ID.
   *
   * @return \Drupal\hmc\Entity\HmcProductInterface
   *   The called hmc product entity.
   */
  public function setReferenceId($id);

  /**
   * Gets the hmc product name.
   *
   * @return string
   *   Name of the hmc product.
   */
  public function getName();

  /**
   * Sets the hmc product name.
   *
   * @param string $name
   *   The hmc product name.
   *
   * @return \Drupal\hmc\Entity\HmcProductInterface
   *   The called hmc product entity.
   */
  public function setName($name);

  /**
   * Gets the hmc product creation timestamp.
   *
   * @return int
   *   Creation timestamp of the hmc product.
   */
  public function getCreatedTime();

  /**
   * Sets the hmc product creation timestamp.
   *
   * @param int $timestamp
   *   The hmc product creation timestamp.
   *
   * @return \Drupal\hmc\Entity\HmcProductInterface
   *   The called hmc product entity.
   */
  public function setCreatedTime($timestamp);

}
