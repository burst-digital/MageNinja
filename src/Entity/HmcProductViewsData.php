<?php

namespace Drupal\hmc\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for hmc product entities.
 */
class HmcProductViewsData extends EntityViewsData {

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
