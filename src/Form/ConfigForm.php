<?php

namespace Drupal\mage_ninja\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\mage_ninja\Controller\ProductController;

class ConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['mage_ninja.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mage_ninja_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('mage_ninja.settings');

    $form['import'] = [
      '#type' => 'details',
      '#title' => t('Import'),
      '#open' => TRUE,
    ];

    $form['import']['import_all'] = [
      '#type' => 'submit',
      '#value' => t('Import all products'),
      '#submit' => ['::importAll'],
    ];

    $form['connection'] = [
      '#type' => 'details',
      '#title' => t('Connection'),
      '#open' => TRUE,
    ];


    $form['connection']['base_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Magento base URI'),
      '#default_value' => $config->get('base_uri'),
      '#required' => TRUE
    ];

    $form['connection']['admin_username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Magento admin username'),
      '#default_value' => $config->get('admin_username'),
      '#required' => TRUE
    ];

    $form['connection']['admin_password'] = [
      '#type' => 'password',
      '#title' => $this->t('Magento admin password'),
      '#default_value' => $config->get('admin_password'),
      '#required' => TRUE
    ];

    return parent::buildForm($form, $form_state);;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('mage_ninja.settings');

    $config->set('base_uri', $form_state->getValue('base_uri'));
    $config->set('admin_username', $form_state->getValue('admin_username'));
    $config->set('admin_password', $form_state->getValue('admin_password'));

    $config->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Import all Magento products into Drupal.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function importAll(array &$form, FormStateInterface $form_state) {
    /** @var ProductController $controller */
    $controller = new ProductController();

    /** @var \Symfony\Component\HttpFoundation\JsonResponse $response */
    // Get one product to find the total product count which is also returned
    // from this endpoint.
    $response =  $controller->getByPage(1, 1);

    /** @var \Symfony\Component\Serializer\Encoder\DecoderInterface $decoder */
    $decoder = \Drupal::service('serializer');

    /** @var array $page */
    $page = $decoder->decode($response->getContent(), 'json');

    /////////////////////////////////////////////

    /** @var int $pageSize */
    $pageSize = 100;

    /** @var int $currentPage */
    $currentPage = 1;

    /** @var array $operations */
    $operations = [];

    /** @var int $totalPages */
    // Always round up to make sure pages with less than $pageSize are processed.
    // Read it every page in case the total_count changes.
    $totalPages = ceil($page['total_count'] / $pageSize);

    do {
      $operations[] = ['\Drupal\mage_ninja\Import\Batch::process', [$currentPage, $pageSize]];
      $currentPage++;
    } while($totalPages >= $currentPage);


    $batch = [
      'title' => t('MageNinja processing batch import'),
      'operations' => $operations,
      'init_message' => t('MageNinja initializing batch import'),
      'progress_message' => t('MageNinja processed @current out of @total (@remaining remaining). Estimated time remaining: @estimate (@elapsed elapsed).'),
      'error_message' => t('MageNinja encountered an error during the batch import process.')
    ];

    batch_set($batch);
  }
}