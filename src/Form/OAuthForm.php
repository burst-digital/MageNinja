<?php

namespace Drupal\mage_ninja\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

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
    $form['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#required' => TRUE
    ];

    $form['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
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
    /** @var \Drupal\Core\Config\ImmutableConfig $config */
    $config = $this->config('mage_ninja.settings');

    $config->set('oauth_username', $form_state->getValue('username'));
    $config->set('oauth_password', $form_state->getValue('password'));

    $config->save();
  }
}