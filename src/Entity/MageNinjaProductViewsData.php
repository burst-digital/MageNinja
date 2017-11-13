<?php

namespace Drupal\mage_ninja\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for mage_ninja product entities.
 */
class MageNinjaProductViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
