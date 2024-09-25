<?php

namespace S360UnzerOsCommerce4;

use UnzerSDK\Unzer;
use yii\db\Query;

class PaymentMethodHelper
{
    const PAYMENT_METHOD_TABLE_NAME = 's360_unzer_oscommerce4_payment_method';

    const STATE_AUTHORIZE = 1;
    const STATE_CHARGE = 2;

    const STATE_ENABLED = 1;
    const STATE_DISABLED = 0;

    const METHOD_AVAILABILITY = [
        'alipay' => [
            'countries' => [],
            'currencies' => ['EUR', 'GBP', 'USD', 'CAD', 'AUD', 'HKD', 'SGD']
        ],
        'applepay' => [
            'countries' => [],
            'currencies' => []
        ],
        'bancontact' => [
            'countries' => ['BE'],
            'currencies' => ['EUR']
        ],
        'card' => [
            'countries' => [],
            'currencies' => []
        ],
        'EPS' => [
            'countries' => ['AT'],
            'currencies' => ['EUR']
        ],
        'giropay' => [
            'countries' => ['DE'],
            'currencies' => ['EUR']
        ],
        'googlepay' => [
            'countries' => [],
            'currencies' => []
        ],
        'ideal' => [
            'countries' => ['NL'],
            'currencies' => ['EUR']
        ],
        'klarna' => [
            'countries' => ['DK', 'FI', 'NO', 'SE'],
            'currencies' => ['EUR', 'SEK', 'DKK', 'NOK']
        ],
        'paypal' => [
            'countries' => [],
            'currencies' => []
        ],
        'payu' => [
            'countries' => ['CZ', 'PL'],
            'currencies' => ['CZK', 'PLN']
        ],
        'postfinance_card' => [
            'countries' => ['CH'],
            'currencies' => ['CHF']
        ],
        'postfinance_efinance' => [
            'countries' => ['CH'],
            'currencies' => ['CHF']
        ],
        'przelewy24' => [
            'countries' => ['PL'],
            'currencies' => ['EUR', 'PLN']
        ],
        'sofort' => [
            'countries' => ['AT', 'BE', 'DE', 'IT', 'NL', 'PL', 'ES', 'CH'],
            'currencies' => ['EUR', 'CHF']
        ],
        'twint' => [
            'countries' => ['CH'],
            'currencies' => ['CHF']
        ],
        'unzer_direct_debit' => [
            'countries' => ['AT', 'BE', 'CY', 'FI', 'FR', 'DE', 'GR', 'IE', 'IT', 'LI', 'LV', 'LT', 'LU', 'MT', 'NL', 'PT', 'SI', 'SK', 'ES'],
            'currencies' => ['EUR']
        ],
        'direct_debit_secured' => [
            'countries' => ['AT', 'DE'],
            'currencies' => ['EUR']
        ],
        'installment' => [
            'countries' => ['AT', 'DE', 'CH'],
            'currencies' => ['EUR', 'CHF']
        ],
        'invoice' => [
            'countries' => ['AT', 'DE', 'CH', 'NL'],
            'currencies' => ['EUR', 'CHF']
        ],
        'prepayment' => [
            'countries' => ['AT', 'BE', 'CY', 'FI', 'FR', 'DE', 'GR', 'IE', 'IT', 'LI', 'LV', 'LT', 'LU', 'MT', 'NL', 'PT', 'SI', 'SK', 'ES'],
            'currencies' => ['EUR']
        ],
        'wechatpay' => [
            'countries' => [],
            'currencies' => ['EUR', 'GBP', 'USD', 'CAD', 'AUD', 'HKD', 'SGD']
        ],
    ];

    public static function getMethodConfig(string $type, int $platform_id): array
    {
        $query = new Query();
        $result = $query->from(self::PAYMENT_METHOD_TABLE_NAME)
            ->where(sprintf('platform_id = %d AND `method` = "%s"', $platform_id, $type))
            ->one();

        if ($result) {
            return $result;
        }

        // we don't have a configuration record yet, create one.
        $data = [
            'platform_id' => $platform_id,
            'method' => $type,
            'state' => self::STATE_DISABLED,
            'transaction_mode' => self::STATE_CHARGE,
        ];

        \Yii::$app->db->createCommand()->upsert(PaymentMethodHelper::PAYMENT_METHOD_TABLE_NAME, $data, ['state' => $data['state'], 'transaction_mode' => $data['transaction_mode']])->execute();

        return $data;
    }

    public static function getActiveMethods(int $platform_id, string $currency, string $country): array
    {
        $query = new Query();
        $results = $query->from(self::PAYMENT_METHOD_TABLE_NAME)
            ->where('platform_id = :platform_id AND state = :state')
            ->params([
                ':platform_id' => $platform_id,
                ':state' => self::STATE_ENABLED,
            ])
            ->all();

        $methodKeyedResults = [];
        foreach ($results as $row) {
            if (self::methodIsAvailableForBasket($row['method'], $currency, $country)) {
                $methodKeyedResults[$row['method']] = $row;
            }
        }

        return $methodKeyedResults;
    }

    /**
     * Check method availability against currency and country
     *
     * @param string $method
     * @param string $currency
     * @param string|null $country
     * @return bool
     */
    private static function methodIsAvailableForBasket(string $method, string $currency, string $country): bool
    {
        $available = true;
        $methodCountryAvailability = self::METHOD_AVAILABILITY[$method]['countries'];
        $methodCurrencyAvailability = self::METHOD_AVAILABILITY[$method]['currencies'];

        if($currency && !empty($methodCurrencyAvailability) && !in_array($currency, $methodCurrencyAvailability)) {
            $available = false;
        }

        if($country && !empty($methodCountryAvailability) && !in_array($country, $methodCountryAvailability)) {
            $available = false;
        }

        return $available;
    }

    public static function getAllPaymentMethods(Unzer $unzer)
    {
        $config = $unzer->fetchKeypair(true);
        return $config->getAvailablePaymentTypes();
    }

    public static function getExcludeTypes(Unzer $unzer, string $selectedPaymentMethod): array
    {

        $allMethods = self::getAllPaymentMethods($unzer);

        $excludeTypes = array_map(function ($method) {
            return strtolower($method->type);
        }, $allMethods);

        return array_values(array_diff($excludeTypes, [strtolower($selectedPaymentMethod)]));
    }

    public static function getTransactionMode(int $platform_id, string $method): int
    {
        $query = new Query();
        $result = $query->from(self::PAYMENT_METHOD_TABLE_NAME)
            ->where(sprintf('platform_id = %d AND `method` = "%s"', $platform_id, $method))
            ->one();

        return (int)$result['transaction_mode'];
    }

}
