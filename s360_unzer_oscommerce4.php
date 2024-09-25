<?php

namespace common\modules\orderPayment;

require_once('lib/S360UnzerOsCommerce4/autoload.php');

use common\classes\Migration;
use common\classes\modules\ModulePayment;
use common\classes\modules\ModuleSortOrder;
use common\classes\modules\ModuleStatus;
use common\classes\modules\PaymentTokensInterface;
use common\classes\modules\TransactionalInterface;
use common\classes\modules\TransactionSearchInterface;
use common\classes\Order;
use common\helpers\Order as OrderHelper;
use common\helpers\OrderPayment as OrderPaymentHelper;
use common\helpers\Warehouses;
use common\models\OrdersPayment;
use common\services\PaymentTransactionManager;
use S360UnzerOsCommerce4\Admin\Helper;
use S360UnzerOsCommerce4\PaymentMethodHelper;
use S360UnzerOsCommerce4\TranslationHelper;
use UnzerSDK\Constants\BasketItemTypes;
use UnzerSDK\Constants\ExemptionType;
use UnzerSDK\Constants\PaymentState;
use UnzerSDK\Constants\Salutations;
use UnzerSDK\Constants\ShippingTypes;
use UnzerSDK\Resources\Basket;
use UnzerSDK\Resources\Customer;
use UnzerSDK\Resources\EmbeddedResources\Address;
use UnzerSDK\Resources\EmbeddedResources\BasketItem;
use UnzerSDK\Resources\Metadata;
use UnzerSDK\Resources\Payment;
use UnzerSDK\Resources\PaymentTypes\Paypage;
use UnzerSDK\Resources\PaymentTypes\Prepayment;
use UnzerSDK\Resources\TransactionTypes\Charge;
use UnzerSDK\Unzer;
use yii\db\Query;

use S360UnzerOsCommerce4\Admin\WebhookConfigurationHelper;
use S360UnzerOsCommerce4\Admin\PaymentMethodConfigurationHelper;

/**
 * @var string
 */
class s360_unzer_oscommerce4 extends ModulePayment implements TransactionalInterface, PaymentTokensInterface, TransactionSearchInterface
{
    var $code, $title, $description, $enabled;

    private $pluginVersion = '1.0.0';
    private $pluginType = 'unzerdev/oscommerce';

    const CODE = 's360_unzer_oscommerce4';

    /**
     * Default values for translations
     *
     * @var string[]
     */
    protected $defaultTranslationArray = [
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_PUBLIC_TITLE' => 'Unzer Payments',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_DESCRIPTION' => 'Unzer Payments offers various payment methods, which can be integrated into your online shop both quickly and easily. Your mix of payment methods to increase online shop revenue.',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_HEADER_BACKGROUND_COLOR' => 'Header Background Color',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_HEADER_FONT_COLOR' => 'Header Font Color',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_HEADER_FONT_SIZE' => 'Header Font Size',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_SHOP_NAME_BACKGROUND_COLOR' => 'Shop Name Background Color',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_SHOP_NAME_FONT_COLOR' => 'Shop Name Font Color',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_SHOP_NAME_FONT_SIZE' => 'Shop Name Font Size',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_TAGLINE_BACKGROUND_COLOR' => 'Tagline Background Color',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_TAGLINE_FONT_COLOR' => 'Tagline Font Color',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_TAGLINE_FONT_SIZE' => 'Tagline Font Size',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_MESSAGE_WEBHOOK_ERROR' => 'There was an error creating the unzer webhook. Error:',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_MESSAGE_UNZER_UNAVAILABLE' => 'This payment method is currently unavailable',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_NO_WEBHOOKS' => 'No webhooks registered yet. Please check your credentials and save again.',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_INIT_WEBHOOKS' => 'Init Webhooks',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_NO_PAYMENT_METHODS' => 'Unzer is not set up yet, please add or check your credentials.',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_ALIPAY_LABEL' => 'Alipay',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_APPLEPAY_LABEL' => 'Apple Pay',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_CARD_LABEL' => 'Credit/Debit Card',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_EPS_LABEL' => 'EPS',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_GIROPAY_LABEL' => 'Giropay',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_GOOGLEPAY_LABEL' => 'Google Pay',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_IDEAL_LABEL' => 'iDEAL',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_INSTALLMENTSECURED_LABEL' => 'Secured Installment',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_INVOICE_LABEL' => 'Invoice',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_INVOICESECURED_LABEL' => 'Secured Invoice',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_KLARNA_LABEL' => 'Klarna',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_PIS_LABEL' => 'PIS',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_PAYU_LABEL' => 'PayU',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_PAYPAL_LABEL' => 'PayPal',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_PREPAYMENT_LABEL' => 'Prepayment',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_PRZELEWY24_LABEL' => 'Przelewy24',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_SEPADIRECTDEBIT_LABEL' => 'SEPA Direct Debit',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_SEPADIRECTDEBITSECURED_LABEL' => 'Secured SEPA Direct Debit',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_SOFORT_LABEL' => 'Sofort',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_TWINT_LABEL' => 'TWINT',
        'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_WECHATPAY_LABEL' => 'WeChat Pay',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_ENABLE_PAYMENT_METHOD' => 'Enable %s?',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_METHOD' => 'Method',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_TM' => 'Transaction Type',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_STATE' => 'Status',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_ENABLED' => 'Enabled',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_DISABLED' => 'Disabled',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_CHARGE' => 'Charge',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_AUTHORIZE' => 'Authorize',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_WEBHOOK_EXISTS' => 'A Webhook for this platform already exists.',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_WEBHOOK_ADDED' => 'Webhook with %s created.',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_WEBHOOK_ERROR' => 'There was an error creating the webhook. Message: %s',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_CONFIRM_DELETE_WEBHOOK' => 'Are you sure you want to delete this webhook?',
        "MODULE_PAYMENT_S360_UNZER_OSC4_REGISTER_WEBHOOKS" => 'Register Webhooks',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_PAYMENT_METHODS' => 'Payment Methods',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_CUSTOMIZATION' => 'Checkout Customization',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_WEBHOOK_MANAGEMENT' => 'Webhook Management',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_webhook_deleted' => 'Webhook deleted',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_PREPAYMENT' => "Please transfer the amount of %s to the following account:<br /><br />"
            . "Holder: %s<br/>"
            . "IBAN: %s<br/>"
            . "BIC: %s<br/><br/>"
            . "<i>Please use only this identification number as the descriptor: </i><br/>"
            . "%s"
    ];

    private $extraParams = [
        'MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_BACKGROUND_COLOR',
        'MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_FONT_COLOR',
        'MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_FONT_SIZE',
        'MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_BACKGROUND_COLOR',
        'MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_FONT_COLOR',
        'MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_FONT_SIZE',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_BACKGROUND_COLOR',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_FONT_COLOR',
        'MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_FONT_SIZE',
    ];

    private $checkoutErrors = [];

    private $metadata;

    /**
     * Initializes the Unzer Embedded Payment Page module.
     *
     * This method is called when creating a new instance of the class. It sets the necessary properties
     * for the Unzer Embedded Payment Page module, such as the code, title, public title, description, and enabled status.
     * If the module's status is not defined, the enabled property is set to false.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->code = self::CODE;
        $this->title = 'Unzer Payments for osCommerce 4';

        $this->public_title = defined('MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_PUBLIC_TITLE') ? MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_PUBLIC_TITLE : $this->title;
        $this->description = defined('MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_DESCRIPTION') ? MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_DESCRIPTION : '';

        $this->enabled = true;

        if (!defined('MODULE_PAYMENT_S360_UNZER_OSC4_STATUS')) {
            $this->enabled = false;
        }

        $this->dont_update_stock = ((defined('MODULE_PAYMENT_S360_UNZER_OSC4_UPDATE_STOCK_BEFORE_PAYMENT') && MODULE_PAYMENT_S360_UNZER_OSC4_UPDATE_STOCK_BEFORE_PAYMENT != 'True') ? true : false);
        $this->order_status = defined('MODULE_PAYMENT_S360_UNZER_OSC4_BEFORE_PAYMENT_ORDER_STATUS_ID') && ((int)MODULE_PAYMENT_S360_UNZER_OSC4_BEFORE_PAYMENT_ORDER_STATUS_ID > 0) ? (int)MODULE_PAYMENT_S360_UNZER_OSC4_BEFORE_PAYMENT_ORDER_STATUS_ID : 0;
        $this->paid_status = defined('MODULE_PAYMENT_S360_UNZER_OSC4_COMPLETED_ORDER_STATUS_ID') && ((int)MODULE_PAYMENT_S360_UNZER_OSC4_COMPLETED_ORDER_STATUS_ID > 0) ? (int)MODULE_PAYMENT_S360_UNZER_OSC4_COMPLETED_ORDER_STATUS_ID : 0;

        if (\Yii::$app->controller->route && ($_POST['action'] ?? '') === 'initWebhooks') {
            tep_admin_check_login();
            (new WebhookConfigurationHelper($this->getUnzer(), $_GET['platform_id']))->registerWebhooks();
        }
        if (\Yii::$app->controller->route && ($_GET['action'] ?? '') === 'deleteWebhook') {
            tep_admin_check_login();
            (new WebhookConfigurationHelper($this->getUnzer(), $_GET['platform_id']))->deleteWebhook($_POST['webhookId']);
        }
    }

    /**
     * Configure configuration array
     *
     * @return array[]
     */
    public function configure_keys()
    {
        $statusIdBeforeCheckout = $this->getOrderStatusId('MODULE_PAYMENT_S360_UNZER_OSC4_BEFORE_PAYMENT_ORDER_STATUS_ID');
        $statusIdOrderPartiallyPaid = $this->getOrderStatusId('MODULE_PAYMENT_S360_UNZER_OSC4_PARTIALLY_PAID_ORDER_STATUS_ID');
        $statusIdOrderCompleted = $this->getOrderStatusId('MODULE_PAYMENT_S360_UNZER_OSC4_COMPLETED_ORDER_STATUS_ID');
        $statusIdOrderPaymentAuthorized = $this->getOrderStatusId('MODULE_PAYMENT_S360_UNZER_OSC4_AUTHORIZED_ORDER_STATUS_ID');
        $statusIdOrderCanceled = $this->getOrderStatusId('MODULE_PAYMENT_S360_UNZER_OSC4_CANCELLED_ORDER_STATUS_ID');
        $statusIdOrderPartiallyRefunded = $this->getOrderStatusId('MODULE_PAYMENT_S360_UNZER_OSC4_PART_REFUNDED_ORDER_STATUS_ID');

        $configuration = [
            'MODULE_PAYMENT_S360_UNZER_OSC4_STATUS' => [
                'title' => 'Enable Unzer Payment?',
                'value' => 'False',
                'description' => '',
                'sort_order' => 10,
                'set_function' => 'tep_cfg_select_option(array(\'True\', \'False\'),',
            ],
            'MODULE_PAYMENT_S360_UNZER_OSC4_SORT_ORDER' => [
                'title' => 'Sort order of display',
                'sort_order' => 20,
                'description' => 'Sort order of display. Lowest is displayed first.',
                'value' => 10,
            ],
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEST_MODE' => [
                'title' => 'Test mode (no charges)',
                'description' => '',
                'value' => 'False',
                'sort_order' => 30,
                'set_function' => 'tep_cfg_select_option(array(\'True\', \'False\'),',
            ],
            'MODULE_PAYMENT_S360_UNZER_OSC4_SANDBOX_PUBLIC_KEY' => [
                'title' => 'Test Mode Public Key',
                'value' => '',
                'sort_order' => 40,
            ],
            'MODULE_PAYMENT_S360_UNZER_OSC4_SANDBOX_PRIVATE_KEY' => [
                'title' => 'Test Mode Private Key',
                'value' => '',
                'sort_order' => 50,
            ],
            'MODULE_PAYMENT_S360_UNZER_OSC4_PRODUCTION_PUBLIC_KEY' => [
                'title' => 'Production Mode Public Key',
                'value' => '',
                'sort_order' => 60,
            ],
            'MODULE_PAYMENT_S360_UNZER_OSC4_PRODUCTION_PRIVATE_KEY' => [
                'title' => 'Production Mode Private Key',
                'value' => '',
                'sort_order' => 70,
            ],
            'MODULE_PAYMENT_S360_UNZER_OSC4_BEFORE_PAYMENT_ORDER_STATUS_ID' => [
                'title' => 'Pre-Payment Order Status',
                'description' => 'Define the status for orders before redirecting to the payment gateway',
                'value' => $statusIdBeforeCheckout,
                'sort_order' => 80,
                'use_function' => '\\common\\helpers\\Order::get_order_status_name',
                'set_function' => 'tep_cfg_pull_down_order_statuses(',
            ],
            'MODULE_PAYMENT_S360_UNZER_OSC4_AUTHORIZED_ORDER_STATUS_ID' => [
                'title' => 'Order Status for authorized Payments',
                'description' => 'Define the status for orders that have been authorized',
                'value' => $statusIdOrderPaymentAuthorized,
                'sort_order' => 90,
                'set_function' => 'tep_cfg_pull_down_order_statuses(',
                'use_function' => '\\common\\helpers\\Order::get_order_status_name',
            ],
            'MODULE_PAYMENT_S360_UNZER_OSC4_PARTIALLY_PAID_ORDER_STATUS_ID' => [
                'title' => 'Order Status for partially paid orders',
                'description' => 'Define the status for orders that have been partially paid',
                'value' => $statusIdOrderPartiallyPaid,
                'sort_order' => 100,
                'set_function' => 'tep_cfg_pull_down_order_statuses(',
                'use_function' => '\\common\\helpers\\Order::get_order_status_name',
            ],
            'MODULE_PAYMENT_S360_UNZER_OSC4_COMPLETED_ORDER_STATUS_ID' => [
                'title' => 'Order Status for Successful Payment',
                'description' => 'Define the status for orders that have been successfully completed',
                'value' => $statusIdOrderCompleted,
                'sort_order' => 110,
                'set_function' => 'tep_cfg_pull_down_order_statuses(',
                'use_function' => '\\common\\helpers\\Order::get_order_status_name',
            ],
            'MODULE_PAYMENT_S360_UNZER_OSC4_CANCELLED_ORDER_STATUS_ID' => [
                'title' => 'Order Status for Cancelled Payment',
                'description' => 'Define the status for orders that have been cancelled',
                'value' => $statusIdOrderCanceled,
                'sort_order' => 120,
                'set_function' => 'tep_cfg_pull_down_order_statuses(',
                'use_function' => '\\common\\helpers\\Order::get_order_status_name',
            ],
            'MODULE_PAYMENT_S360_UNZER_OSC4_PART_REFUNDED_ORDER_STATUS_ID' => [
                'title' => 'Order Status for partially refunded Payments',
                'description' => 'Define the status for orders that have been partially refunded',
                'value' => $statusIdOrderPartiallyRefunded,
                'sort_order' => 130,
                'set_function' => 'tep_cfg_pull_down_order_statuses(',
                'use_function' => '\\common\\helpers\\Order::get_order_status_name',
            ],
            'MODULE_PAYMENT_S360_UNZER_OSC4_UPDATE_STOCK_BEFORE_PAYMENT' => [
                'title' => 'Update Inventory Before Payment?',
                'value' => 'False',
                'sort_order' => 140,
                'description' => 'Should the product stock be updated even if the payment has not been completed?',
                'set_function' => 'tep_cfg_select_option(array(\'True\', \'False\'), ',
            ],
            'MODULE_PAYMENT_S360_UNZER_OSC4_ENABLE_LOG' => [
                'title' => 'Enable debug log?',
                'value' => 'False',
                'sort_order' => 150,
                'description' => 'If you enable this option, a debug log is written.',
                'set_function' => 'tep_cfg_select_option(array(\'True\', \'False\'), ',
            ],
        ];

        return $configuration;

    }

    /**
     * Install the module
     *
     * @param int $platform_id The ID of the platform being installed on
     * @return bool Indicates if the installation was successful or not
     */
    public function install($platform_id)
    {
        $this->installTranslation();

        tep_db_query("CREATE TABLE IF NOT EXISTS `s360_unzer_oscommerce4_configuration` (
              `platform_id` int(11) NOT NULL DEFAULT '0',
              `key` VARCHAR(200) NOT NULL DEFAULT '',
              `value` TEXT NOT NULL DEFAULT ''
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


        tep_db_query("CREATE TABLE IF NOT EXISTS `s360_unzer_oscommerce4_payment_method` (
          `platform_id` int(11) NOT NULL DEFAULT 0,
          `method` varchar(200) NOT NULL DEFAULT '',
          `state` tinyint(1) NOT NULL DEFAULT 0,
          `transaction_mode` tinyint(1) NOT NULL DEFAULT 0,
          UNIQUE KEY `platform_method` (`platform_id`,`method`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
        ");

        return parent::install($platform_id);
    }

    public function remove($platform_id) {
        \Yii::$app->getDb()->createCommand()->delete('s360_unzer_oscommerce4_configuration', [
            'platform_id' => $platform_id,
        ])->execute();

        \Yii::$app->getDb()->createCommand()->delete('s360_unzer_oscommerce4_payment_method', [
            'platform_id' => $platform_id,
        ])->execute();

        parent::remove($platform_id);
    }

    /**
     * Install translation for payment module
     *
     * @return void
     */
    private function installTranslation()
    {
        // no need to install translations when english the one and only active language
        $languages = \common\models\Languages::find()->select('code')->asArray()->all();
        $languages = array_column($languages, 'code');
        if (count($languages) === 1 && in_array('en', $languages)) return;

        $migration = new Migration();

        $addTranslation = [];

        foreach (TranslationHelper::$translationArray as $language => $translations) {
            foreach ($translations as $translationKey => $translationValue) {
                $addTranslation[$translationKey][$language] = $translationValue;
                $addTranslation[$translationKey]['en'] = $this->defaultTranslationArray[$translationKey];
            }
        }

        foreach ($this->configure_keys() as $key => $config) {
            $addTranslation[strtoupper($key) . '_TITLE']['en'] = $config['title'];
            $addTranslation[strtoupper($key) . '_DESCRIPTION']['en'] = $config['description'];
        }

        $migration->addTranslation('payment', $addTranslation);
    }

    /**
     * Get extra params for the given platform_id
     *
     * @param int $platform_id The platform_id to get extra params for
     *
     * @return array[] An array of extra params for the given platform_id
     */
    function get_extra_params($platform_id)
    {
        $params = [];

        /**
         * during installation process the function
         * get_extra_params is called BEFORE the 'install' function and therefore, this table doesn't exist yet.
         */
        try {
            foreach ((new Query())
                         ->from('s360_unzer_oscommerce4_configuration')
                         ->where(sprintf('platform_id = %d', $platform_id))
                         ->all() as $result) {
                $params[$result['key']] = $result['value'];
            };
        } catch (\Exception$e) {
        }

        return $params;
    }

    /**
     * Save extra parameters in the database for a given platform ID
     *
     * @return void
     */
    function save_extra_params()
    {
        $postData = \Yii::$app->request->post();

        $platform_id = (int)$postData['platform_id'];

        $this->emptyConfigurationTable($platform_id);

        foreach ($this->extraParams as $extraParam) {
            if (array_key_exists($extraParam, $postData) && $postData[$extraParam]) {
                \Yii::$app->getDb()->createCommand()->insert('s360_unzer_oscommerce4_configuration', [
                    'platform_id' => $platform_id,
                    'key' => $extraParam,
                    'value' => $postData[$extraParam]
                ])->execute();
            }
        }
    }

    /**
     * Configure and save additional params
     *
     * @return string
     */
    public function extra_params()
    {
        $postData = \Yii::$app->request->post();
        $platformId = $postData['platform_id'] ?? $_GET['platform_id'];

        if (($unzer = $this->getUnzer())) {
            $paymentMethodConfigurationHelper = new PaymentMethodConfigurationHelper($unzer, $platformId);
            $webhookConfigurationHelper = new WebhookConfigurationHelper($unzer, $platformId);
        }

        if (array_key_exists('set', $postData) && array_key_exists('module', $postData) && array_key_exists('platform_id', $postData)) {
            $this->save_extra_params();
            if ($paymentMethodConfigurationHelper) {
                $paymentMethodConfigurationHelper->saveConfiguration($postData);
            }
        }

        $params = $this->get_extra_params($platformId);

        $params['paymentMethods'] = [];
        $params['webhooks'] = [];

        if ($unzer) {
            $params['paymentMethods'] = $paymentMethodConfigurationHelper->getConfiguration();
            $params['webhooks'] = $webhookConfigurationHelper->getConfiguration();
            $params['initWebhookUrl'] = $webhookConfigurationHelper->getInitUrl();
            $params['deleteWebhookUrl'] = $webhookConfigurationHelper->getDeleteUrl();
        }

        $view = \Yii::$app->getView();

        $view->registerCssFile(Helper::getBase() . 'lib/common/modules/orderPayment/lib/S360UnzerOsCommerce4/admin/admin.css');

        return $view->renderFile(\Yii::getAlias('@site_root/') . 'lib/common/modules/orderPayment/lib/S360UnzerOsCommerce4/admin/configuration.tpl', $params);
    }

    /**
     * Online or Offline Payment Method
     *
     * @return true
     */
    function isOnline()
    {
        return true;
    }

    /**
     * Return the key which is used for sorting
     */
    public function describe_sort_key()
    {
        return new ModuleSortOrder('MODULE_PAYMENT_S360_UNZER_OSC4_SORT_ORDER');
    }

    /**
     * Return the Module Status
     *
     * @return ModuleStatus
     */
    public function describe_status_key()
    {
        return new ModuleStatus('MODULE_PAYMENT_S360_UNZER_OSC4_STATUS', 'True', 'False');
    }


    public function popUpMode()
    {
        return true;
    }

    public function tlPopupJS(): string
    {
        return '';
    }

    /**
     * osCommerce has rounding issues.
     * This function returns the difference up to 0.05.
     * If the difference is higher, false is returned.
     *
     * @return float|bool
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    function getRoundingIssueDifference()
    {
        $order = $this->manager->getOrderInstance();

        $totalCalculated = 0;

        foreach ($order->products as $productData) {
            $totalCalculated += $this->getFinalPrice($productData['configurator_price']);
        }

        foreach ($this->getOrderTotals() as $subTotal) {
            if ($subTotal['code'] === 'ot_coupon') {
                $totalCalculated -= $this->getFinalPrice($subTotal['value']);
            } else if ($subTotal['code'] === 'ot_shipping') {
                $totalCalculated += $this->getFinalPrice($subTotal['value']);
            }
        }

        $diff = $this->getFinalPrice($order->info['total_inc_tax']) - $totalCalculated;

        if ($diff <= 0.1 && $diff >= -0.1) {
            return $diff;
        }
        return false;
    }

    /** Payment Selection On Checkout */
    function selection()
    {
        $this->addUnzerScriptsToCheckout();

        $fields = [];

        $platformId = $this->manager->getPlatformId();
        $currency = $this->manager->getOrderInstance()->info['currency'];
        $billingCountry = $this->manager->getBillingAddress()['country']['iso_code_2'];

        foreach (PaymentMethodHelper::getActiveMethods($platformId, $currency, $billingCountry) as $paymentType => $shortCode) {
            $fields[]['field'] = $this->getFieldHtml($paymentType);
        }

        return array(
            'id' => $this->code,
            'fields' => $fields,
        );
    }

    /**
     * Create the field html
     *
     * Render hidden input element
     *
     * @param $paymentType
     * @return string
     */
    protected function getFieldHtml($paymentType)
    {
        return '<label class="unzerLabel ' . $paymentType . '"><input type="radio" name="payment" value="' . self::CODE . '" data-unzer-payment-method="' . $paymentType . '"/><span>' . TranslationHelper::getPaymentMethodLabel($paymentType) . '</span></label>';
    }

    /**
     * Before the order is processed init the unzer checkout and check for possible errors
     *
     *
     * @return void
     * @throws \Exception
     */
    public function before_process()
    {
        $order = $this->manager->getOrderInstance();

        $this->unzerPreValidation();

        $order->isPaidUpdated = true;

        if (!empty($this->checkoutErrors)) {
            echo json_encode(['success' => false, 'messages' => $this->checkoutErrors]);
            die;
        }

        parent::before_process();
    }

    /**
     * Create the paypage and return the payment page
     * after the order was processed
     *
     * @return void
     */
    public function after_process()
    {
        $ppgId = false;
        $success = true;

        $paypage = $this->initUnzerCheckout();

        if ($paypage) {
            $ppgId = $paypage->getPayment()->getPayPage()->getId();

            $order = $this->manager->getOrderInstance();

            OrderPaymentHelper::createDebitFromOrder($order, $this->getFinalPrice($order->info['total_inc_tax']), false, [
                'id' => $paypage->getPaymentId(),
                'payment_class' => $this->code,
                'payment_method' => $this->title,
                'comments' => $paypage->getId()
            ]);

        } else {
            $success = false;
        }

        echo json_encode([
            'ppg' => $ppgId,
            'messages' => $this->checkoutErrors,
            'success' => $success,
        ]);

        die();
    }

    /**
     * Pre-Validation for Unzer
     *
     * @return bool
     */
    private function unzerPreValidation()
    {
        return $this->initializeUnzerComponents();
    }

    /**
     * Initialize Unzer Checkout
     *
     * @return Paypage|false Returns the initialized Paypage object on success, false otherwise
     */
    private function initUnzerCheckout()
    {
        $order = $this->manager->getOrderInstance();
        $unzerPaymentMethod = \Yii::$app->getRequest()->post('unzerPaymentMethod');
        $orders_id = $this->getOrderId($order);

        if (!$this->initializeUnzerComponents()) {
            return false;
        }

        $paypage = $this->initializePayPage($order, $orders_id, $unzerPaymentMethod);
        if (!$paypage) {
            return false;
        }

        try {
            if (PaymentMethodHelper::getTransactionMode($this->_getPlatformId(), $unzerPaymentMethod) === PaymentMethodHelper::STATE_AUTHORIZE) {
                $paypage = $this->unzer->initPayPageAuthorize($paypage, $this->customer, $this->basket, $this->metadata);
            } else {
                $paypage = $this->unzer->initPayPageCharge($paypage, $this->customer, $this->basket, $this->metadata);
            }
        } catch (\Exception $e) {
            $this->log($e);
            $this->checkoutErrors[] = $e->getClientMessage();
            return false;
        }

        $paymentId = $paypage->getPayment()->getPayPage()->getPaymentId();
        $this->manager->set('unzer_payment_id', $paymentId);

        return $paypage;
    }

    /**
     * Initialize Unzer components such as customer and basket
     *
     * @return bool
     */
    private function initializeUnzerComponents()
    {
        $this->unzer = $this->getUnzer();

        if (!$this->unzer) {
            $this->checkoutErrors[] = MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_MESSAGE_UNZER_UNAVAILABLE;
            return false;
        }

        $this->customer = $this->createUnzerCustomer();

        try {
            $this->basket = $this->createBasket();
        } catch (\Exception $e) {
            $this->log($e);
            $this->checkoutErrors[] = $e->getClientMessage();
            return false;
        }

        try {
            $this->unzer->createOrUpdateCustomer($this->customer);
            $this->unzer->createBasket($this->basket);
        } catch (\Exception $e) {
            $this->log($e);
            $this->checkoutErrors[] = $e->getClientMessage();
            return false;
        }

        $this->metadata = $this->createMetadata();

        return true;
    }

    private function createMetadata()
    {
        $metaData = new Metadata();
        $metaData->setShopType(PROJECT_VERSION_NAME);
        $metaData->setShopVersion( PROJECT_VERSION_MAJOR . '.' . PROJECT_VERSION_MINOR . '.' . PROJECT_VERSION_PATCH);
        $metaData->addMetadata('pluginType', $this->pluginType);
        $metaData->addMetadata('pluginVersion', $this->pluginVersion);
        return $metaData;
    }

    /**
     * Initialize PayPage object
     *
     * @param $order
     * @param $orders_id
     * @param $unzerPaymentMethod
     * @return Paypage|false
     */
    private function initializePayPage($order, $orders_id, $unzerPaymentMethod)
    {
        try {
            $paypage = new Paypage($this->getFinalPrice($order->info['total_inc_tax']), $order->info['currency'], $this->getReturnUrl());
        } catch (\Exception $e) {
            $this->log($e);
            $this->checkoutErrors[] = $e->getClientMessage();
            return false;
        }


        $this->log([
            'currency' => $order->info['currency'],
            PaymentMethodHelper::getExcludeTypes($this->unzer, $unzerPaymentMethod),
        ]);

        $paypage->setOrderId($orders_id)
            ->setShopName(STORE_NAME)
            ->setInvoiceId($order->getOrderId())
            ->setExemptionType(ExemptionType::LOW_VALUE_PAYMENT);


        if (!$this->manager->isCustomerAssigned()) {
            $paypage->setAdditionalAttribute('disabledCOF', 'card,paypal,sepa-direct-debit');
        }

        if ($unzerPaymentMethod) {
            $excludeTypes = PaymentMethodHelper::getExcludeTypes($this->unzer, $unzerPaymentMethod);
            $paypage->setExcludeTypes($excludeTypes);
        }

        $paypage->setCss($this->getPaymentPageStyles());
        return $paypage;
    }

    /**
     * Create an Unzer customer with billing and shipping address
     *
     * @return \UnzerSDK\Resources\Customer
     */
    private function createUnzerCustomer()
    {
        $order = $this->manager->getOrderInstance();
        $customerIdentity = $order->customer;

        $billingAddress = (new Address())
            ->setName($order->billing['firstname'] . ' ' . $order->billing['lastname'])
            ->setStreet($order->billing['street_address'])
            ->setZip($order->billing['postcode'])
            ->setCity($order->billing['city'])
            ->setCountry($order->billing['country']['iso_code_2']);

        $shippingAddress = (new Address())
            ->setName($order->delivery['firstname'] . ' ' . $order->delivery['lastname'])
            ->setStreet($order->delivery['street_address'])
            ->setZip($order->delivery['postcode'])
            ->setCity($order->delivery['city'])
            ->setCountry($order->delivery['country']['iso_code_2'])
            ->setShippingType($this->manager->isBillAsShip() ? ShippingTypes::EQUALS_BILLING : ShippingTypes::DIFFERENT_ADDRESS);

        $customer = (new Customer())
            ->setId($customerIdentity['id'])
            ->setFirstname($customerIdentity['firstname'])
            ->setLastname($customerIdentity['lastname'])
            ->setSalutation($this->getSalutation($customerIdentity['gender']))
            ->setEmail($customerIdentity['email_address'])
            ->setPhone($customerIdentity['telephone'])
            ->setBillingAddress($billingAddress)
            ->setShippingAddress($shippingAddress);


        if ($this->manager->isBillAsShip() && $order->delivery['company']) {
            $customer->setCompany($order->delivery['company']);
        }

        if (!$this->manager->isBillAsShip() && $order->billing['company']) {
            $customer->setCompany($order->billing['company']);
        }

        if ($customerIdentity->customers_dob !== '0000-00-00 00:00:00') {
            $dob = new \DateTime($customerIdentity->customers_dob);
            $customer->setBirthDate($dob->format('Y-m-d'));
        }

        return $customer;
    }

    /**
     * Get Salutation based on gender
     *
     * @param string $gender
     * @return string
     */
    private function getSalutation($gender)
    {
        return $gender === 'f' || $gender === 's' ? Salutations::MRS : ($gender === 'm' ? Salutations::MR : Salutations::UNKNOWN);
    }

    /**
     * Create the basket object for Unzer
     *
     * @return Basket
     * @throws \Exception
     */
    private function createBasket()
    {
        $order = $this->manager->getOrderInstance();
        $basket = new Basket();

        $basket->setTotalValueGross($this->getFinalPrice($order->info['total_inc_tax']))
            ->setCurrencyCode($order->info['currency'])
            ->setOrderId($order->getOrderId());

        $i = 0;

        foreach ($order->products as $productData) {
            $diff = 0;
            if ($i === 0) {
                /**
                 * to resolve rounding issues in oscommerce we add or subtract the rounding difference up to 0.05 to
                 * the first basket item
                 */
                $diff = $this->getRoundingIssueDifference();
            }
            $basketItem = (new BasketItem())
                ->setBasketItemReferenceId($order->getOrderId() . '-' . $productData['id'])
                ->setQuantity($productData['qty'])
                ->setAmountPerUnitGross($this->getFinalPrice($productData['configurator_price']) + $diff)
                ->setVat($productData['tax'])
                ->setTitle($productData['name'])
                ->setType($productData['is_virtual'] ? BasketItemTypes::DIGITAL : BasketItemTypes::GOODS);
            $basket->addBasketItem($basketItem);
            $i++;
        }

        foreach ($this->getOrderTotals() as $subTotal) {
            if ($subTotal['code'] === 'ot_coupon') {
                $this->addCouponBasketItem($basket, $subTotal);
            } else if ($subTotal['code'] === 'ot_shipping') {
                $this->addShippingBasketItem($basket, $subTotal);
            }
        }

        return $basket;
    }

    private function getOrderId($order)
    {
        if ($this->isPartlyPaid()) {
            $invoice = $this->manager->getOrderSplitter()->getInvoiceInstance();
            return $invoice ? $invoice->parent_id : $order->order_id;
        }

        return $order->order_id;
    }

    /**
     * Add a shipping basket item to the given basket
     *
     * @param Basket $basket
     * @param array $shippingData
     * @return void
     */
    private function addShippingBasketItem(Basket $basket, array $shippingData): void
    {
        $basketItem = (new BasketItem())
            ->setBasketItemReferenceId($this->manager->getOrderInstance()->getOrderId() . '-shipping')
            ->setQuantity(1)
            ->setAmountPerUnitGross($this->getFinalPrice($shippingData['value_inc_tax']))
            ->setTitle($shippingData['title'])
            ->setType(BasketItemTypes::SHIPMENT);

        $basket->addBasketItem($basketItem);
    }

    /**
     * Add a coupon as a basket item
     *
     * @param Basket $basket
     * @param array $couponData
     * @return void
     */
    private function addCouponBasketItem(Basket $basket, array $couponData): void
    {
        $basketItem = (new BasketItem())
            ->setBasketItemReferenceId($this->manager->getOrderInstance()->getOrderId() . '-voucher')
            ->setQuantity(1)
            ->setAmountPerUnitGross(0)
            ->setAmountDiscountPerUnitGross($this->getFinalPrice($couponData['value_inc_tax']))
            ->setTitle($couponData['title'])
            ->setType(BasketItemTypes::VOUCHER);

        $basket->addBasketItem($basketItem);
    }

    /**
     * Returns an instance of the Unzer class based on the configuration settings.
     *
     * @return Unzer
     */
    private function getUnzer()
    {
        $privateKey = $this->getUnzerEnvironmentMode() === 'sandbox'
            ? MODULE_PAYMENT_S360_UNZER_OSC4_SANDBOX_PRIVATE_KEY
            : MODULE_PAYMENT_S360_UNZER_OSC4_PRODUCTION_PRIVATE_KEY;

        if ($privateKey) {
            try {
                return new Unzer($privateKey, $this->getLocale());
            } catch (\Exception $exception) {
                $this->log($exception->getMessage());
            }
        }

        return false;
    }

    /**
     * Call webhooks based on the requested action
     *
     * @return boolean
     */
    public function call_webhooks()
    {
        $result = false;

        switch ($_GET['process']) {
            case 'return':
                $this->handleReturnWebhook();
                break;
            case 'notification':
                $this->handleNotification();
                break;
        }

        return $result;
    }


    /**
     * Payment was successful, clear basket, redirect to success page
     *
     * @return void
     */
    private function handleReturnWebhook()
    {
        $unzerPaymentId = $this->manager->get('unzer_payment_id');

        if ($unzerPaymentId) {
            try {
                $payment = $this->getUnzerPayment($unzerPaymentId);

                $label = $this->getMethodLabelByPayment($payment);
                $info = '';

                if ($payment->getPaymentType() instanceof Prepayment) {
                    $charge = $payment->getChargeByIndex(0);

                    /** @var \common\classes\Currencies $currencies */
                    $currencies = \Yii::$container->get('currencies');

                    $info = sprintf(
                        TranslationHelper::getTranslation('PREPAYMENT'),
                        $currencies->format($charge->getAmount(), false, $charge->getCurrency()),
                        $charge->getHolder(),
                        $charge->getIban(),
                        $charge->getBic(),
                        $charge->getDescriptor()
                    );
                }

                \Yii::$app->getDb()->createCommand()->update(
                    'orders',
                    [
                        'payment_method' => $label,
                        'payment_info' => $info,
                    ],
                    sprintf('orders_id = %d', $payment->getOrderId())
                )->execute();


                \Yii::$app->getDb()->createCommand()->update(
                    'orders_payment',
                    [
                        'orders_payment_transaction_commentary' => $info,
                    ],
                    sprintf('orders_payment_order_id = %d AND orders_payment_transaction_id = "%s"', $payment->getOrderId(), $unzerPaymentId)
                )->execute();

            } catch (\Exception $e) {

            }

            $this->manager->clearAfterProcess();
            tep_redirect(tep_href_link('checkout/success'));
        }
    }


    /**
     * Handle the notification from unzer
     *
     * @return void
     */
    private function handleNotification()
    {
        $json = file_get_contents('php://input');
        $notificationData = json_decode($json, true);

        if (array_key_exists('paymentId', $notificationData)) {

            $paymentId = $notificationData['paymentId'];
            $event = $notificationData['event'];

            if (in_array($event, ['payment.completed', 'authorize.succeeded', 'payment.partly', 'payment.chargeback'])) {
                if ($paymentId) {
                    $orderId = $this->getOrderByTransactionId($paymentId);

                    if ($orderId) {
                        $order = $this->manager->getOrderInstanceWithId('\common\classes\Order', $orderId);
                        $payment = $this->getUnzerPayment($paymentId);

                        if ($event === 'payment.completed') {
                            $this->handlePaymentCompleted($order, $payment);
                        }

                        if ($event === 'payment.partly') {
                            $this->handlePaymentPartly($order, $payment);
                        }

                        if ($event === 'authorize.succeeded') {
                            $this->handlePaymentAuthorized($order, $payment);
                        }
                        if ($event === 'payment.chargeback') {
                            $this->handleChargeback($order, $payment);
                        }
                    }
                }
            }
        }

        http_response_code(200);
    }


    /**
     * Removed sleep function from parent method
     *
     * @param $id
     * @return false|int|null
     */
    protected function getOrderByTransactionId($id)
    {
        $ret = null;

        $transaction = OrdersPayment::findOne(['orders_payment_module' => $this->code, 'orders_payment_transaction_id' => $id]);

        if (!empty($transaction)) {
            if (!empty($transaction->orders_payment_order_id)) {
                $ret = $transaction->orders_payment_order_id;
            } else {
                $ret = false;
            }
        }

        return $ret;
    }

    /**
     * Handle payment authorization for an order
     *
     * @param Order $order The order object
     * @param Payment $payment The payment object
     * @return void
     */
    private function handlePaymentAuthorized(Order $order, Payment $payment)
    {
        $this->updateStockAfterPayment($order);

        $orderStatusId = $this->getOrderStatusId('MODULE_PAYMENT_S360_UNZER_OSC4_AUTHORIZED_ORDER_STATUS_ID');

        OrderHelper::setStatus($order->order_id, $orderStatusId, [
            'comments' => '',
            'customer_notified' => 0,
        ]);

        $invoice_id = $this->manager->getOrderSplitter()->getInvoiceId();

        $parentTransaction = $this->searchRecord($payment->getId());

        $parentId = null;

        if ($parentTransaction) {
            $parentId = $parentTransaction->orders_payment_id;
        }

        $this->updatePaymentTransaction($payment->getId() . '/' . $payment->getAuthorization()->getId(), [
            'fulljson' => json_encode($payment),
            'status_code' => OrderPaymentHelper::OPYS_PENDING,
            'status' => $payment->getStateName(),
            'amount' => $payment->getAuthorization()->getAmount(),
            'suborder_id' => $invoice_id,
            'orders_id' => $order->order_id,
            'comments' => 'Short-Id: ' . $payment->getAuthorization()->getShortId(),
            'parent_id' => $parentId,
            'payment_method' => $this->getMethodLabelByPayment($payment)
        ]);
    }

    private function handlePaymentPartly(Order $order, Payment $payment)
    {
        $invoice_id = $this->manager->getOrderSplitter()->getInvoiceId();

        $orderStatusId = $this->getOrderStatusId('MODULE_PAYMENT_S360_UNZER_OSC4_PARTIALLY_PAID_ORDER_STATUS_ID');

        OrderHelper::setStatus($order->order_id, $orderStatusId, [
            'comments' => '',
            'customer_notified' => 0,
        ]);

        foreach ($payment->getCharges() as $charge) {
            $transactionData = [
                'fulljson' => json_encode($payment),
                'status_code' => OrderPaymentHelper::OPYS_SUCCESSFUL,
                'status' => $payment->getStateName(),
                'amount' => $charge->getAmount(),
                'suborder_id' => $invoice_id,
                'orders_id' => $order->order_id,
                'comments' => 'Short-Id: ' . $this->getUnzer()->fetchCharge($charge)->getShortId(),
                'payment_method' => $this->getMethodLabelByPayment($payment)
            ];

            $this->updatePaymentTransaction($payment->getId() . '/' . $charge->getId(), $transactionData);
        }

        $order->updatePaidTotals();
    }

    /**
     * Handle the event when a payment is completed for an order
     *
     * @param Order $order The order object
     * @param Payment $payment The payment object
     * @return void
     */
    private function handlePaymentCompleted(Order $order, Payment $payment)
    {
        $this->updateStockAfterPayment($order);

        $orderStatusId = $this->getOrderStatusId('MODULE_PAYMENT_S360_UNZER_OSC4_COMPLETED_ORDER_STATUS_ID');

        OrderHelper::setStatus($order->order_id, $orderStatusId, [
            'comments' => '',
            'customer_notified' => 0,
        ]);

        $order->update_piad_information(true);
        $order->save_details();

        $order->notify_customer($order->getProductsHtmlForEmail(), []);

        $invoice_id = $this->manager->getOrderSplitter()->getInvoiceId();

        foreach ($payment->getCharges() as $charge) {
            $this->updatePaymentTransaction($payment->getId() . '/' . $charge->getId(), [
                'fulljson' => json_encode($_POST),
                'status_code' => OrderPaymentHelper::OPYS_SUCCESSFUL,
                'status' => $payment->getStateName(),
                'amount' => $charge->getAmount(),
                'suborder_id' => $invoice_id,
                'orders_id' => $order->order_id,
                'comments' => 'Short-Id: ' . $this->getUnzer()->fetchCharge($charge)->getShortId(),
                'payment_method' => $this->getMethodLabelByPayment($payment)
            ]);
        }
    }

    private function handleChargeback(Order $order, Payment $payment)
    {
        if ($payment->isChargeBack()) {
            $orderStatusId = $this->getOrderStatusId('MODULE_PAYMENT_S360_UNZER_OSC4_CANCELLED_ORDER_STATUS_ID');

            OrderHelper::setStatus($order->order_id, $orderStatusId, [
                'comments' => '',
                'customer_notified' => 0,
            ]);
            /** @var \common\services\PaymentTransactionManager $tManager */

            $invoice_id = $this->manager->getOrderSplitter()->getInvoiceId();

            foreach ($payment->getChargebacks() as $chargeback) {
                $this->updatePaymentTransaction($payment->getId() . '/' . $chargeback->getId(), [
                    'fulljson' => json_encode($_POST),
                    'status_code' => OrderPaymentHelper::OPYS_CANCELLED,
                    'status' => $payment->getStateName(),
                    'amount' => $chargeback->getAmount(),
                    'suborder_id' => $invoice_id,
                    'orders_id' => $order->order_id,
                    'comments' => 'Short-Id: ' . $this->getUnzer()->fetchChargeback($chargeback)->getShortId(),
                    'payment_method' => $this->getMethodLabelByPayment($payment)
                ]);
            }
        }
    }


    private function getMethodLabelByPayment(Payment $payment): string
    {
        $paymentShortCode = explode('-', $payment->getPaymentType()->getId())[1];
        $label = TranslationHelper::getPaymentMethodLabelByShortcode($paymentShortCode);

        if ($label) {
            $label = $this->title . ' - ' . $label;
        } else {
            $label = $this->title;
        }
        return $label;
    }

    /**
     * Get the return URL for the payment callback webhook
     *
     * @return string The return URL
     */
    private function getReturnUrl(): string
    {
        return tep_href_link('callback/webhooks.payment.' . $this->code, 'process=return', 'SSL');
    }

    public function canRefund($transaction_id)
    {
        if (str_contains($transaction_id, 's-cnl')) return false;
        $transaction_id = explode('/', $transaction_id)[0];

        try {
            $payment = $this->getUnzer()->fetchPayment($transaction_id);
        } catch (\Exception $e) {
            return false;
        }

        if ($payment->getState() === PaymentState::STATE_COMPLETED) {
            return true;
        }
        return false;
    }

    public function refund($transaction_id, $amount = 0)
    {
        $transaction_id = explode('/', $transaction_id)[0];
        try {
            $payment = $this->getUnzer()->fetchPayment($transaction_id);
        } catch (\Exception $e) {
            $this->log($e);
            return $e->getClientMessage();
        }

        $orderId = $this->getOrderByTransactionId($transaction_id);
        $order = $this->manager->getOrderInstanceWithId('\common\classes\Order', $orderId);

        $maxRefundableAmount = $this->getFinalPrice($order->info['total_inc_tax'], $order->info['currency']) - $this->getFinalPrice($order->info['total_refund_inc_tax'], $order->info['currency']);

        if ($amount === $maxRefundableAmount) {
            $orderStatus = $this->getOrderStatusId('MODULE_PAYMENT_S360_UNZER_OSC4_CANCELLED_ORDER_STATUS_ID');
        } else if ($amount < $maxRefundableAmount) {
            $orderStatus = $this->getOrderStatusId('MODULE_PAYMENT_S360_UNZER_OSC4_PART_REFUNDED_ORDER_STATUS_ID');
        }
        try {
            $payment->cancelAmount($amount);
        } catch (\Exception $e) {
            $this->log($e);
            return $e->getClientMessage();
        }
        OrderHelper::setStatus($order->order_id, $orderStatus, [
            'comments' => '',
            'customer_notified' => 0,
        ]);

        $payment = $this->getUnzer()->fetchPayment($transaction_id);

        /** @var Charge $cancellation * */
        foreach ($payment->getCancellations() as $cancellation) {
            // get new status from unzer
            $payment = $this->getUnzerPayment($transaction_id);

            $parent = $cancellation->getParentResource();

            if ($parent instanceof Charge) {
                $parentResource = $this->getUnzer()->fetchCharge($parent);
            } else {
                $parentResource = $this->getUnzer()->fetchAuthorization($parent);
            }

            $cancellation = $parentResource->getCancellation($cancellation->getId());

            $this->updatePaymentTransaction($payment->getId() . '/' . $cancellation->getParentResource()->getId() . '/' . $cancellation->getId(), [
                'fulljson' => json_encode($payment),
                'status_code' => OrderPaymentHelper::OPYS_REFUNDED,
                'status' => $payment->getStateName(),
                'amount' => (float)$cancellation->getAmount(),
                'comments' => 'Short-Id: ' . $cancellation->getShortId(),
                'payment_class' => $this->code,
                'payment_method' => $this->getMethodLabelByPayment($payment),
                'parent_transaction_id' => $transaction_id,
                'orders_id' => 0,
            ]);
        }
        $order->updatePaidTotals();

        return true;
    }

    /**
     * Check if the transaction can be voided
     *
     * @param $transaction_id
     * @return bool
     */
    public function canVoid($transaction_id)
    {
        try {
            $payment = $this->getUnzer()->fetchPayment($transaction_id);
        } catch (\Exception $e) {
            $payment = null;
        }

        return $payment !== null && $payment->isPending();
    }

    /**
     * Void transaction
     *
     * @param $transaction_id
     * @return string|true
     */
    public function void($transaction_id)
    {
        try {
            $payment = $this->getUnzerPayment($transaction_id);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        $orderId = $this->getOrderByTransactionId($transaction_id);
        $order = $this->manager->getOrderInstanceWithId('\common\classes\Order', $orderId);

        try {
            $cancellations = $payment->cancelAmount();
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        OrderHelper::setStatus($order->order_id, $this->getOrderStatusId('MODULE_PAYMENT_S360_UNZER_OSC4_CANCELLED_ORDER_STATUS_ID'), [
            'comments' => '',
            'customer_notified' => 0,
        ]);

        foreach ($cancellations as $cancellation) {
            // get new status from unzer
            $payment = $this->getUnzerPayment($transaction_id);

            $this->updatePaymentTransaction($payment->getId() . '/' . $cancellation->getId(), [
                'status_code' => OrderPaymentHelper::OPYS_CANCELLED,
                'status' => $payment->getStateName(),
                'amount' => $this->getFinalPrice($order->info['total_inc_tax'], $order->info['currency']),
                'orders_id' => $order->order_id,
                'comments' => 'Short-Id: ' . $cancellation->getShortId(),
                'payment_method' => $this->getMethodLabelByPayment($payment)
            ]);

        }
        return true;
    }

    /**
     * Check if we can capture the transaction
     *
     * @param $transaction_id
     * @return bool
     */
    public function canCapture($transaction_id)
    {
        try {
            $payment = $this->getUnzer()->fetchPayment($transaction_id);

            if ($payment &&
                ($payment->getState() === PaymentState::STATE_PENDING || $payment->getState() === PaymentState::STATE_PARTLY) &&
                $payment->getAmount()->getRemaining() > 0
                &&
                !$payment->getPaymentType() instanceof Prepayment
            ) {
                return true;
            }
        } catch (\Exception $e) {
            // Handle exception here if required
            $this->log($e->getMessage());
        }

        return false;
    }

    private function updatePaymentTransaction($transactionId, $transactionData)
    {
        $tManager = $this->manager->getTransactionManager($this);

        $record = OrderPaymentHelper::searchRecord($this->code, $transactionId);

        // Do not update old transactions
        if (!$record->orders_payment_id) {
            $tManager->updatePaymentTransaction($transactionId, $transactionData);
        }
    }

    /**
     * Capture transaction
     *
     * @param $transaction_id
     * @param $amount
     * @return string|true
     */
    public function capture($transaction_id, $amount = 0)
    {
        try {
            $payment = $this->getUnzerPayment($transaction_id);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        try {
            $payment->charge($amount);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        // The callback from unzer takes some time, wait for the webhook to be finished
        sleep(5);

        return true;
    }

    public function canReauthorize($transaction_id)
    {
        return false;
    }


    /**
     * Add Unzer scripts to checkout
     *
     * This method adds the necessary CSS and JS files from Unzer to the checkout page.
     *
     * @return void
     */
    private function addUnzerScriptsToCheckout()
    {
        $view = $this->getView();

        $view->registerCssFile('https://static.unzer.com/v1/unzer.css');

        $view->registerCss(
            \Yii::$app->getView()->renderFile(
                \Yii::getAlias('@site_root/')
                .
                'lib/common/modules/orderPayment/lib/S360UnzerOsCommerce4/frontend/checkout.css'
            )
        );

        $this->registerCallback("{$this->code}Callback");

        $view->registerJs('
            let unzerCheckoutUrl = "' . $this->getCheckoutUrl([], self::PROCESS_PAGE) . '";
            let unzerLocale = "' . $this->getLocale() . '";    
        ');

        $view->registerJs(
            \Yii::$app->getView()->renderFile(
                \Yii::getAlias('@site_root/')
                .
                'lib/common/modules/orderPayment/lib/S360UnzerOsCommerce4/frontend/checkout.js'
            )
        );
    }


    /**
     * Get final price in the orders currency
     *
     * @param $number
     * @param $currency_code
     * @param $currency_value
     * @return float
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    function getFinalPrice($number, $currency_code = '', $currency_value = '')
    {
        $currencies = \Yii::$container->get('currencies');

        if (empty($currency_code) || !$currencies->is_set($currency_code)) {
            $currency_code = \Yii::$app->settings->get('currency');
        }

        if (empty($currency_value) || !is_numeric($currency_value)) {
            $currency_value = $currencies->currencies[$currency_code]['value'];
        }

        return floatval(self::round($number * $currency_value, $currencies->currencies[$currency_code]['decimal_places']));
    }


    /**
     * Get the environment mode for the Unzer payment gateway.
     *
     * @return string Returns the environment mode for the Unzer payment gateway.
     */
    private function getUnzerEnvironmentMode()
    {
        $mode = 'production';

        if (defined('MODULE_PAYMENT_S360_UNZER_OSC4_TEST_MODE')
            &&
            MODULE_PAYMENT_S360_UNZER_OSC4_TEST_MODE === 'True'
        ) {
            $mode = 'sandbox';
        }
        return $mode;
    }


    /**
     * Update the stock after payment for an order
     *
     * @param Order $order The order object
     * @return bool Whether the stock was updated or not
     */
    private function updateStockAfterPayment($order): bool
    {
        $stock_updated = false;

        if (MODULE_PAYMENT_S360_UNZER_OSC4_UPDATE_STOCK_BEFORE_PAYMENT == 'False' && !OrderHelper::is_stock_updated((int)$order->order_id)) {
            for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
                if (STOCK_LIMITED == 'true') {
                    Warehouses::update_stock_of_order($order->order_id, (strlen($order->products[$i]['template_uprid']) > 0 ? $order->products[$i]['template_uprid'] : $order->products[$i]['id']), $order->products[$i]['qty'], 0, 0, $order->info['platform_id']);
                    $stock_updated = true;
                }
            }
        }

        $sql_data_array = [];
        if ($stock_updated === true) {
            $sql_data_array['stock_updated'] = 1;
            tep_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id=' . $order->order_id);
        }
        return $stock_updated;
    }

    /**
     * Log the given variable using the Yii warning method
     *
     * @param mixed $var The variable to log
     * @return void
     */
    private function log($var)
    {
        if (defined('MODULE_PAYMENT_S360_UNZER_OSC4_ENABLE_LOG') && constant('MODULE_PAYMENT_S360_UNZER_OSC4_ENABLE_LOG') === 'True') {
            \Yii::warning(print_r($var, 1), 'UNZER');
        }
    }

    /**
     * Empty the configuration table for a given platform ID.
     *
     * @param int $platform_id The platform ID.
     * @throws \yii\db\Exception
     */
    private function emptyConfigurationTable($platform_id)
    {
        \Yii::$app->db->createCommand(
            sprintf('
                DELETE FROM `s360_unzer_oscommerce4_configuration`
                    WHERE `platform_id` = %d 
                      AND `key`', $platform_id))
            ->execute();
    }

    /**
     * Get the current application locale.
     *
     * @return string The locale code.
     */
    private function getLocale()
    {
        return \Yii::$app->language;
    }

    /**
     * Get the payment page styles
     *
     * @return array Returns an array of payment page styles.
     * The styles are organized by different areas on the payment page,
     * such as 'header', 'tagLine', and 'shopName'.
     *
     * Each area contains an array of CSS attributes and their corresponding values.
     * The CSS attributes are 'background-color', 'color', and 'font-size'.
     *
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    private function getPaymentPageStyles(): array
    {
        $styleMapping = [
            'header' => [
                'MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_BACKGROUND_COLOR' => 'background-color',
                'MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_FONT_COLOR' => 'color',
                'MODULE_PAYMENT_S360_UNZER_OSC4_HEADER_FONT_SIZE' => 'font-size'
            ],
            'tagLine' => [
                'MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_BACKGROUND_COLOR' => 'background-color',
                'MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_FONT_COLOR' => 'color',
                'MODULE_PAYMENT_S360_UNZER_OSC4_TAGLINE_FONT_SIZE' => 'font-size'
            ],
            'shopName' => [
                'MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_BACKGROUND_COLOR' => 'background-color',
                'MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_FONT_COLOR' => 'color',
                'MODULE_PAYMENT_S360_UNZER_OSC4_SHOP_NAME_FONT_SIZE' => 'font-size'
            ]
        ];

        $styles = [];

        $params = $this->get_extra_params($this->_getPlatformId());

        foreach ($styleMapping as $area => $elements) {
            foreach ($elements as $configKey => $attribute) {
                if (array_key_exists($configKey, $params) && $params[$configKey]) {
                    $styles[$area][] = $attribute . ': ' . $params[$configKey];
                }
            }
            if (!empty($styles[$area])) {
                $styles[$area] = implode('; ', $styles[$area]);
            }
        }

        return $styles;
    }


    /**
     * Get the platform ID
     *
     * Returns an integer representing the platform ID.
     *
     * @return int The platform ID
     */
    private function _getPlatformId()
    {
        $platform_id = (int)$this->manager->getPlatformId();
        if ($platform_id == 0 && defined('PLATFORM_ID')) {
            $platform_id = PLATFORM_ID;
        }
        return $platform_id;
    }

    /**
     * Get the order status ID based on the order state
     *
     * @param string $orderState The order state
     * @return int The order status ID
     */
    private function getOrderStatusId(string $orderState)
    {
        if (defined($orderState))
            return constant($orderState);
        return $this->getDefaultOrderStatusId();
    }

    /**
     * Get the Unzer payment object by payment ID
     *
     * @param string $paymentId The ID of the payment
     * @return \Payment The Unzer payment object
     */
    private function getUnzerPayment(string $paymentId): Payment
    {
        $unzer = $this->getUnzer();
        return $unzer->fetchPayment($paymentId);
    }

    /**
     * Retrieves the order totals
     *
     * Only takes the last element of total modules
     *
     * @return array An array containing the order totals
     */
    private function getOrderTotals(): array
    {
        $orderTotalInformation = $this->manager->getTotalCollection()->process();

        $totals = [];

        foreach ($orderTotalInformation as $orderTotal) {
            $totals[$orderTotal['code']] = $orderTotal;
        }

        return $totals;
    }


    public function getFields()
    {

    }

    public function search($queryParams)
    {

    }

    public function getTransactionDetails($transaction_id, PaymentTransactionManager $tManager = null)
    {

    }

    public function reauthorize($transaction_id, $amount = 0)
    {

    }

    /**
     * @return \yii\base\View|\yii\web\View
     */
    private function getView()
    {
        return \Yii::$app->getView();
    }

}