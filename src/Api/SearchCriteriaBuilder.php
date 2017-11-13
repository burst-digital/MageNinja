<?php

namespace Drupal\mage_ninja\Api;

class SearchCriteriaBuilder {
  const PREFIX = 'searchCriteria';

  private $criteria = [];

  public function add($criterion) {
    $this->criteria = array_merge($this->criteria, $criterion);

    return $this;
  }

  public function __toString() {
    $criteriaString = '';
    $index = 0;
    foreach($this->criteria as $criterion => $value) {
      if($index === 0) {
        $criteriaString .= '?';
      } else {
        $criteriaString .= '&';
      }
      $criteriaString .= self::PREFIX;
      $criteriaString .= $criterion;
      $criteriaString .= '=';
      $criteriaString .= $value;

      $index++;
    }

    return $criteriaString;
  }
}