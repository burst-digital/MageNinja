<?php

namespace Drupal\mage_ninja\Controller;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Psr7\Response;

class OAuthController extends ControllerBase {
  public function callback() {
    // This function is called by Magento before the user is sent to the OAuthForm.

    $oauthConsumerKey = $_POST['oauth_consumer_key'];
    $oauthConsumerSecret = $_POST['oauth_consumer_secret'];
    $storeBaseUrl = $_POST['store_base_url'];
    $oauthVerifier = $_POST['oauth_verifier'];

    /** @var \Drupal\Core\Config\Config $config */
    $config = \Drupal::service('config.factory')->getEditable('mage_ninja.settings');

    $config->set('oauth_consumer_key', $oauthConsumerKey)->save();
    $config->set('oauth_consumer_secret', $oauthConsumerSecret)->save();
    $config->set('oauth_verifier', $oauthVerifier)->save();
    $config->set('store_base_url', $storeBaseUrl)->save();

    return new Response();
  }
}