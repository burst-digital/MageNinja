<?php

namespace Drupal\mage_ninja\DataModel;

class Pricing {
  /** @var float $price */
  protected $price;

  /** @var float $specialPrice */
  protected $specialPrice;

  /** @var string */
  protected $specialPriceFrom;

  /** @var string */
  protected $specialPriceTo;

  /**
   * Constructs Pricing DataModel from Magento API product array.
   *
   * @param array $inputArray
   */
  public function __construct($inputArray) {
    $this->price = $inputArray['price'];

    if(isset($inputArray['custom_attributes'])) {
      foreach($inputArray['custom_attributes'] as $attribute) {
        if($attribute['attribute_code'] === 'special_price') {
          $this->specialPrice = $attribute['value'];
        }

        if($attribute['attribute_code'] === 'special_from_date') {
          $this->specialPriceFrom = $attribute['value'];
        }

        if($attribute['attribute_code'] === 'special_to_date') {
          $this->specialPriceTo = $attribute['value'];
        }
      }
    }
  }

  /**
   * @return float
   */
  public function getPrice() {
    return $this->price;
  }

  /**
   * @param float $price
   */
  public function setPrice($price) {
    $this->price = $price;
  }

  /**
   * @return float
   */
  public function getSpecialPrice() {
    return $this->specialPrice;
  }

  /**
   * @param float $specialPrice
   */
  public function setSpecialPrice($specialPrice) {
    $this->specialPrice = $specialPrice;
  }

  /**
   * @return string
   */
  public function getSpecialPriceFrom() {
    return $this->specialPriceFrom;
  }

  /**
   * @param string $specialPriceFrom
   */
  public function setSpecialPriceFrom($specialPriceFrom) {
    $this->specialPriceFrom = $specialPriceFrom;
  }

  /**
   * @return string
   */
  public function getSpecialPriceTo() {
    return $this->specialPriceTo;
  }

  /**
   * @param string $specialPriceTo
   */
  public function setSpecialPriceTo($specialPriceTo) {
    $this->specialPriceTo = $specialPriceTo;
  }
}