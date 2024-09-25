<?php

namespace S360UnzerOsCommerce4\Admin;

use S360UnzerOsCommerce4\PaymentMethodHelper;
use S360UnzerOsCommerce4\TranslationHelper;

use S360UnzerOsCommerce4\Admin\UnzerAdminHelper;

/**
 *  This class handles the payment method configuration for the admin area
 */
class PaymentMethodConfigurationHelper extends UnzerAdminHelper
{

    private array $unsupportedPaymentTypes = [
        'PIS',
        'giropay',
        'postfinance_card',
        'bancontact',
        'postfinance_efinance'
    ];

    public function getConfiguration(): array
    {
        $list = [];
        $methods = PaymentMethodHelper::getAllPaymentMethods($this->getUnzer());

        foreach ($methods as $method) {
            if ($this->methodIsSupported($method->type)) {

                $list[] = [
                    'label' => TranslationHelper::getPaymentMethodLabel($method->type),
                    'type' => $method->type,
                    'canChangeTransactionMode' => $this->canMethodAuthorize($method->type),
                    'config' => PaymentMethodHelper::getMethodConfig($method->type, $this->getPlatformId()),
                ];
            }
        }

        return $list;
    }

    /**
     * Check if the method can be authorized
     * @param $type
     * @return bool
     */
    protected function canMethodAuthorize(string $type): bool
    {
        $class = $this->getBaseClass($type);

        if (method_exists($class, "authorize")) {
            return true;
        }

        return false;
    }

    /**
     * For the type get the class name of the unzer sdk
     *
     * @param $type
     * @return string|void
     */
    protected function getClassNameByTypeNew($type)
    {
        $class = "UnzerSDK\Resources\PaymentTypes\\" . self::getClassNameByType($type);
        if (class_exists($class)) {
            if (method_exists($class, "authorize")) {
                $reflectionClass = new \ReflectionClass($class);
                return $reflectionClass->getName();
            }
        }
    }

    /**
     * Save configuration values from admin area
     *
     * @param array $postData
     * @return bool
     * @throws \yii\db\Exception
     */
    public function saveConfiguration(array $postData): bool
    {
        $methods = $postData['unzerPaymentMethod'];

        foreach ($methods as $typeId => $method) {
            $transactionMode = PaymentMethodHelper::STATE_CHARGE;

            if (array_key_exists('transaction_mode', $method)) {
                $transactionMode = $method['transaction_mode'];
            }

            $data = [
                'platform_id' => $this->getPlatformId(),
                'method' => $typeId,
                'state' => (int) $method['state'],
                'transaction_mode' => $transactionMode,
            ];
            \Yii::$app->db->createCommand()->upsert(PaymentMethodHelper::PAYMENT_METHOD_TABLE_NAME, $data, ['state' => $method['state'], 'transaction_mode' => $transactionMode])->execute();
        }

        return true;
    }

    /**
     * Check if the base class exists
     *
     * @param $type
     * @return false|string
     */
    protected function getBaseClass($type)
    {
        $class = "UnzerSDK\Resources\PaymentTypes\\" . self::getClassNameByType($type);
        if (class_exists($class)) {
            return $class;
        }
        return false;
    }

    /**
     * Checks if the payment method is supported
     *
     * i.E. PaymentClass exists & it is not deprecated & and it is not in the array of unsupported types
     * for the embedded payment page
     *
     * @param $type
     * @return bool
     */
    protected function methodIsSupported($type): bool
    {
        if ($this->getBaseClass($type) && !$this->methodIsDeprecated($type) && !in_array($type, $this->unsupportedPaymentTypes)) {
            return true;
        }
        return false;
    }

    /**
     * Checks if the payment method has a deprecated flag
     *
     * @param string $type
     * @return bool
     * @throws \ReflectionException
     */
    protected function methodIsDeprecated(string $type): bool
    {
        if (($baseClass = $this->getBaseClass($type))) {
            $reflectionClass = new \ReflectionClass($baseClass);
            if (str_contains($reflectionClass->getDocComment(), '@deprecated')) {
                return true;
            }
        } else {
            return true;
        }
        return false;
    }

    private static function getClassNameByType(string $type): string
    {
        return ucfirst(str_replace('-', '_', ucwords($type, '-')));
    }

}