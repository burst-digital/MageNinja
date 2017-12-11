<?php

namespace Drupal\mage_ninja\Controller;

use Drupal\Core\Controller\ControllerBase;

class OAuthController extends ControllerBase {
  public function callback() {
    // Data received from Magento
    $oauth_consumer_key = $_POST['oauth_consumer_key'];
    $oauth_consumer_key_secret = $_POST['oauth_consumer_key_secret'];
    $store_base_url = $_POST['store_base_url'];
    $oauth_verifier = $_POST['oauth_verifier'];

    /** @var \Drupal\Core\Config\Config $config */
    $config = \Drupal::service('config.factory')->getEditable('mage_ninja.settings');

    $config->set('oauth_consumer_key', $oauth_consumer_key)->save();
    $config->set('oauth_consumer_key_secret', $oauth_consumer_key_secret)->save();
    $config->set('oauth_verifier', $oauth_verifier)->save();
    $config->set('store_base_url', $store_base_url)->save();
  }
}