<?php

namespace Drupal\mage_ninja\DataModel;

class Product {
  /** @var int $id */
  protected $id;

  /** @var string $sku */
  protected $sku;

  /** @var string $name */
  protected $name;

  /** @var \Drupal\mage_ninja\DataModel\Pricing $price */
  protected $pricing;

  /** @var array $inputArray */
  protected $inputArray;

  /**
   * Constructs Product DataModel from Magento API product array.
   *
   * @param array $inputArray
   */
  public function __construct($inputArray) {
    $this->inputArray = $inputArray;

    $this->setId($inputArray['id']);
    $this->setSku($inputArray['sku']);
    $this->setName($inputArray['name']);

    $pricing = new Pricing($inputArray);
    $this->setPricing($pricing);
  }

  /**
   * Returns the Product DataModel as array that maps to MageNinjaProduct.
   *
   * @return array $output
   */
  public function toArray() {
    $output = [
      'reference_id' => $this->id,
      'sku' => $this->sku,
      'name' => $this->name,
      'price' => $this->pricing->getPrice(),
      'special_price' => $this->pricing->getSpecialPrice(),
      'special_price_from' => $this->pricing->getSpecialPriceFrom(),
      'special_price_to' => $this->pricing->getSpecialPriceTo(),
    ];

    return $output;
  }

  /**
   * @param int $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * @param string $sku
   */
  public function setSku($sku) {
    $this->sku = $sku;
  }

  /**
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * @param \Drupal\mage_ninja\DataModel\Pricing $pricing
   */
  public function setPricing($pricing) {
    $this->pricing = $pricing;
  }

  /**
   * @return int
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getSku() {
    return $this->sku;
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return \Drupal\mage_ninja\DataModel\Pricing
   */
  public function getPricing() {
    return $this->pricing;
  }
}