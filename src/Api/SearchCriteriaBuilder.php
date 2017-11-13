<?php

namespace Drupal\mage_ninja\Api;

class SearchCriteriaBuilder {

  /**
   * String that will prefix the criteria string.
   * This prefix is required by the Magento API to indicate it is a
   * search criteria.
   *
   * @const string PREFIX
   */
  const PREFIX = 'searchCriteria';

  /**
   * Array to save the search criteria in.
   *
   * @var array
   */
  private $criteria = [];

  /**
   * @param array $criterion
   *  The criterion used to filter the search.
   *  Format: ['[pageSize]' => 10]
   *
   * @return $this
   */
  public function add($criterion) {
    $this->criteria = array_merge($this->criteria, $criterion);

    return $this;
  }

  /**
   * Returns string formatted as Magento search criteria.
   *
   * Example: ?searchCriteria[pageSize]=10&searchCriteria[currentPage]=1
   *
   * @return string
   */
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