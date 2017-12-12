<?php

namespace Drupal\mage_ninja\Form;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;

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
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var string $consumerKey */
    $consumerKey = $_GET['oauth_consumer_key'];

    /** @var string $consumerCallback */
    $consumerCallback = $_GET['success_call_back'];

    /** @var \Drupal\Core\Config\ImmutableConfig $config */
    $config = $this->config('mage_ninja.settings');

    /** @var string $integrationKey */
    $integrationKey = $form_state->getValue('integration_key');

    /** @var string $integrationSecret */
    $integrationSecret = $form_state->getValue('integration_secret');

    if($config->get('oauth_consumer_key') === $consumerKey) {
      // Check if the login credentials match those in the config
      if ($integrationKey === $config->get('integration_key') && Crypt::hashEquals($config->get('integration_secret'), $integrationSecret)) {
        $form_state->setResponse(new TrustedRedirectResponse($consumerCallback));
      }
      else {
        throw new \Exception('Incorrect login credentials.');
      }
    } else {
      throw new \Exception('Consumer keys received on different requests do not match.');
    }
  }
}