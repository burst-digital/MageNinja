<?php

namespace Drupal\mage_ninja\Form;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class OAuthForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mage_ninja_oauth_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['integration_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Integration key'),
      '#description' => 'Find this in Drupal: Configuration > Web services > MageNinja',
      '#required' => TRUE
    ];

    $form['integration_secret'] = [
      '#type' => 'password',
      '#title' => $this->t('Integration secret'),
      '#description' => 'Find this in Drupal: Configuration > Web services > MageNinja',
      '#required' => TRUE
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit')
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\Core\Config\ImmutableConfig $config */
    $config = $this->config('mage_ninja.settings');

    /** @var string $integrationKey */
    $integrationKey = $form_state->getValue('integration_key');

    /** @var string $integrationSecret */
    $integrationSecret = $form_state->getValue('integration_secret');

    if(!$integrationKey || empty($integrationKey)) {
      $form_state->setErrorByName('integration_key', $this->t('Integration key is a required field.'));
    }

    if(!$integrationSecret || empty($integrationSecret)) {
      $form_state->setErrorByName('integration_secret', $this->t('Integration secret is a required field.'));
    }

    if($integrationKey !== $config->get('integration_key') || !Crypt::hashEquals($config->get('integration_secret'), $integrationSecret)) {
      $form_state->setErrorByName('integration_secret', $this->t('Login credentials are invalid.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\Core\Config\ImmutableConfig $config */
    $config = $this->config('mage_ninja.settings');

    /** @var string $consumerKey */
    $consumerKey = $_GET['oauth_consumer_key'];

    /** @var string $consumerCallback */
    $consumerCallback = $_GET['success_call_back'];

    // Make sure the consumerKey sent in the request is the same as the one received from Magento
    if($config->get('oauth_consumer_key') === $consumerKey) {
      $handlerStack = HandlerStack::create();

      $middleware = new Oauth1([
        'consumer_key' => $config->get('oauth_consumer_key'),
        'consumer_secret' => $config->get('oauth_consumer_secret'),
        'verifier' => $config->get('oauth_verifier'),
        'token_secret' => ''
      ]);
      $handlerStack->push($middleware);

      $client = new Client([
//        'base_uri' => $config->get('store_base_url'),
        'base_uri' => 'http://webserver.magento',
        'handler' => $handlerStack,
        'auth' => 'oauth'
      ]);

      /*
       * REQUEST TOKEN
       */
      $response = $client->post('/oauth/token/request');
      $body = (string)$response->getBody();

      // Format $body into usable variables.
      // $body = 'oauth_token=hp0blt5hlel4qfq02utc03a98xkgnv7b&oauth_token_secret=0e14acixb3l5nl6io0mj4x8ek0147c83'
      $bodyArray = explode('&', $body);
      $oauthRequestToken = explode('=', $bodyArray[0])[1];
      $oauthRequestTokenSecret = explode('=', $bodyArray[1])[1];


      /*
       * ACCESS TOKEN
       */
      $response = $client->post('/oauth/token/access');
      $body = (string)$response->getBody();

      // Format $body into usable variables.
      // $body = 'oauth_token=hp0blt5hlel4qfq02utc03a98xkgnv7b&oauth_token_secret=0e14acixb3l5nl6io0mj4x8ek0147c83'
      $bodyArray = explode('&', $body);
      $oauthAccessToken = explode('=', $bodyArray[0])[1];
      $oauthAccessTokenSecret = explode('=', $bodyArray[1])[1];

      /** @var \Drupal\Core\Config\Config $config */
      $config = \Drupal::service('config.factory')->getEditable('mage_ninja.settings');

      $config->set('oauth_token', $oauthAccessToken)->save();
      $config->set('oauth_token_secret', $oauthAccessTokenSecret)->save();

      $form_state->setResponse(new TrustedRedirectResponse($consumerCallback));
    } else {
      throw new \Exception('Consumer key is invalid.');
    }
  }
}