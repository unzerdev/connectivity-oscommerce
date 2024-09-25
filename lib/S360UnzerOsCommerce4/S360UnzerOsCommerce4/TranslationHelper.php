<?php

namespace S360UnzerOsCommerce4;

class TranslationHelper
{
    const DEFAULT_LOCALE = 'en';
    const TRANSLATION_CONSTANT_PREFIX = 'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_';

    public static array $translationArray = [
        'de' => [
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_PUBLIC_TITLE' => 'Unzer Payments',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_DESCRIPTION' => 'Unzer bietet verschiedene Zahlungsmethoden, die schnell und einfach in Deinen Webshop integriert werden können. Dein Mix an Zahlungsmethoden für mehr Umsatz im Online-Shop.',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_HEADER_BACKGROUND_COLOR' => 'Hintergrundfarbe der Überschrift',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_HEADER_FONT_COLOR' => 'Schriftfarbe der Überschrift',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_HEADER_FONT_SIZE' => 'Schriftgröße der Überschrift',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_SHOP_NAME_BACKGROUND_COLOR' => 'Hintergrundfarbe des Shop-Namens',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_SHOP_NAME_FONT_COLOR' => 'Schriftfarbe des Shop-Namens',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_SHOP_NAME_FONT_SIZE' => 'Schriftgröße des Shop-Namens',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_TAGLINE_BACKGROUND_COLOR' => 'Hintergrundfarbe des Slogans',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_TAGLINE_FONT_COLOR' => 'Schriftfarbe des Slogans',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_TAGLINE_FONT_SIZE' => 'Schriftgröße des Slogans',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_MESSAGE_WEBHOOK_ERROR' => 'Der Webhook konnte nicht eingerichtet werden. Fehler: %s',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_MESSAGE_UNZER_UNAVAILABLE' => 'Unzer Payments ist aktuell nicht verfügbar.',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_NO_WEBHOOKS' => 'Es sind keine Webhooks für diesen Vertriebskanal registriert. Bitte überprüfen Sie Ihre Zugangsdaten und speichern Sie die Konfiguration erneut.',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_INIT_WEBHOOKS' => 'Webhooks Regristrieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_STATUS_TITLE' => 'Unzer-Zahlung aktivieren?',
            'MODULE_PAYMENT_S360_UNZER_OSC4_SORT_ORDER_TITLE' => 'Anzeigereihenfolge',
            'MODULE_PAYMENT_S360_UNZER_OSC4_SORT_ORDER_DESCRIPTION' => 'Anzeigereihenfolge. Die niedrigste wird zuerst angezeigt.',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEST_MODE_TITLE' => 'Testmodus (keine Gebühren)',
            'MODULE_PAYMENT_S360_UNZER_OSC4_SANDBOX_PUBLIC_KEY_TITLE' => 'Testmodus Public Key',
            'MODULE_PAYMENT_S360_UNZER_OSC4_SANDBOX_PRIVATE_KEY_TITLE' => 'Testmodus Private Key',
            'MODULE_PAYMENT_S360_UNZER_OSC4_PRODUCTION_PUBLIC_KEY_TITLE' => 'Produktionsmodus Public Key',
            'MODULE_PAYMENT_S360_UNZER_OSC4_PRODUCTION_PRIVATE_KEY_TITLE' => 'Produktionsmodus Private Key',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TRANSACTION_TYPE_TITLE' => 'Transaktionstyp',
            'MODULE_PAYMENT_S360_UNZER_OSC4_BEFORE_PAYMENT_ORDER_STATUS_ID_TITLE' => 'Vorzahlungsstatus',
            'MODULE_PAYMENT_S360_UNZER_OSC4_BEFORE_PAYMENT_ORDER_STATUS_ID_DESCRIPTION' => 'Definieren Sie den Status für Bestellungen vor der Weiterleitung zum Zahlungs-Gateway',
            'MODULE_PAYMENT_S360_UNZER_OSC4_AUTHORIZED_ORDER_STATUS_ID_TITLE' => 'Bestellstatus für autorisierte Zahlungen',
            'MODULE_PAYMENT_S360_UNZER_OSC4_AUTHORIZED_ORDER_STATUS_ID_DESCRIPTION' => 'Definieren Sie den Status für Bestellungen, die autorisiert wurden',
            'MODULE_PAYMENT_S360_UNZER_OSC4_COMPLETED_ORDER_STATUS_ID_TITLE' => 'Bestellstatus für erfolgreiche Zahlungen',
            'MODULE_PAYMENT_S360_UNZER_OSC4_COMPLETED_ORDER_STATUS_ID_DESCRIPTION' => 'Definieren Sie den Status für Bestellungen, die erfolgreich abgeschlossen wurden',
            'MODULE_PAYMENT_S360_UNZER_OSC4_CANCELLED_ORDER_STATUS_ID_TITLE' => 'Bestellstatus für stornierte Zahlungen',
            'MODULE_PAYMENT_S360_UNZER_OSC4_CANCELLED_ORDER_STATUS_ID_DESCRIPTION' => 'Definieren Sie den Status für Bestellungen, die storniert wurden',
            'MODULE_PAYMENT_S360_UNZER_OSC4_PART_REFUNDED_ORDER_STATUS_ID_TITLE' => 'Bestellstatus für teilweise zurückerstattete Zahlungen',
            'MODULE_PAYMENT_S360_UNZER_OSC4_PART_REFUNDED_ORDER_STATUS_ID_DESCRIPTION' => 'Definieren Sie den Status für Bestellungen, die teilweise zurückerstattet wurden',
            'MODULE_PAYMENT_S360_UNZER_OSC4_PARTIALLY_PAID_ORDER_STATUS_ID_TITLE' => 'Bestellstatus für teilweise bezahlte Zahlungen',
            'MODULE_PAYMENT_S360_UNZER_OSC4_PARTIALLY_PAID_ORDER_STATUS_ID_DESCRIPTION' => 'Definieren Sie den Status für Bestellungen, die teilweise bezahlt wurden',
            'MODULE_PAYMENT_S360_UNZER_OSC4_UPDATE_STOCK_BEFORE_PAYMENT_TITLE' => 'Inventar vor Zahlung aktualisieren?',
            'MODULE_PAYMENT_S360_UNZER_OSC4_UPDATE_STOCK_BEFORE_PAYMENT_DESCRIPTION' => 'Soll der Produktbestand aktualisiert werden, auch wenn die Zahlung nicht abgeschlossen ist?',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_NO_PAYMENT_METHODS' => 'Unzer wurde noch nicht eingerichet. Bitte hinterlegen Sie ihre Zugangsdaten.',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_ALIPAY_TITLE' => 'Alipay aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_APPLE_PAY_TITLE' => 'Apple Pay aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_CC_TITLE' => 'Karte aktivieren (Kreditkarte, Debitkarte)',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_EPS_TITLE' => 'EPS aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_GIROPAY_TITLE' => 'Giropay aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_GOOGLE_PAY_TITLE' => 'Google Pay aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_IDEAL_TITLE' => 'iDEAL aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_KLARNA_TITLE' => 'Klarna aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_PAYPAL_TITLE' => 'PayPal aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_PAYU_TITLE' => 'PayU aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_PRZELEWY24_TITLE' => 'Przelewy24 aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_SOFORT_TITLE' => 'Sofort aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_TWINT_TITLE' => 'TWINT aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_UDD_TITLE' => 'Unzer Lastschrift aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_DDS_TITLE' => 'Direkte Lastschrift sichern aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_UNZER_INSTALLMENT_TITLE' => 'Unzer Ratenzahlung aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_UNZER_INVOICE_TITLE' => 'Unzer Rechnung aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_UNZER_PREPAYMENT_TITLE' => 'Unzer Vorkasse aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_METHOD_WECHAT_PAY_TITLE' => 'WeChat Pay aktivieren',
            'MODULE_PAYMENT_S360_UNZER_OSC4_ENABLE_LOG_TITLE' => 'Debug-Log aktivieren?',
            'MODULE_PAYMENT_S360_UNZER_OSC4_ENABLE_LOG_DESCRIPTION' => 'Um das Logging zu aktivieren, bitte diese Option aktivieren.',
            'MODULE_PAYMENT_S360_UNZER_OSC4_TEXT_PREPAYMENT' => "Bitte überweisen Sie den Betrag von %s auf folgendes Konto:<br /><br />"
                . "Inhaber: %s<br/>"
                . "IBAN: %s<br/>"
                . "BIC: %s<br/><br/>"
                . "<i>Bitte verwenden Sie nur diese Identifikationsnummer als Verwendungszweck: </i><br/>"
                . "%s"
        ]
    ];


    private static array $unzerShortCodeMapping = [
        'ali' => 'alipay',
        'apl' => 'applepay',
        'bct' => 'bancontact',
        'crd' => 'card',
        'eps' => 'eps',
        'gro' => 'giropay',
        'gop' => 'googlepay',
        'ctp' => 'clicktopay',
        'hdd' => 'installment-secured',
        'idl' => 'ideal',
        'ins' => 'installment-secured',
        'ivc' => 'invoice',
        'ivf' => 'invoice-factoring',
        'ivg' => 'invoice-guaranteed',
        'ivs' => 'invoice-secured',
        'kla' => 'klarna',
        'ppl' => 'paypal',
        'pyu' => 'payu',
        'pfc' => 'post-finance-card',
        'pfe' => 'post-finance-efinance',
        'pis' => 'pis',
        'ppy' => 'prepayment',
        'p24' => 'przelewy24',
        'sdd' => 'sepa-direct-debit',
        'ddg' => 'sepa-direct-debit-guaranteed',
        'dds' => 'sepa-direct-debit-secured',
        'sft' => 'sofort',
        'twt' => 'twint',
        'wcp' => 'wechatpay'
    ];


    private static array $paymentMethodLabels = [
        'de' => [
            'applepay' => 'Apple Pay',
            'twint' => 'Twint',
            'installment-secured' => 'Installment Secured',
            'bancontact' => 'Bancontact',
            'invoice-secured' => 'Invoice Secured',
            'invoice-guaranteed' => 'Invoice Secured',
            'invoice-factoring' => 'Invoice Secured',
            'ideal' => 'Ideal',
            'sepa-direct-debit' => 'Sepa Direct Debit',
            'post-finance-efinance' => 'Post Finance Efinance',
            'post-finance-card' => 'Post Finance Card',
            'pis' => 'Unzer Bank Transfer',
            'wechatpay' => 'WeChatPay',
            'invoice' => 'Invoice',
            'googlepay' => 'Google Pay',
            'EPS' => 'EPS',
            'eps' => 'EPS',
            'sepa-direct-debit-secured' => 'Sepa Direct Debit Secured',
            'sepa-direct-debit-guaranteed' => 'Sepa Direct Debit Guarenteed',
            'przelewy24' => 'Przelewy24',
            'giropay' => 'Giropay',
            'prepayment' => 'Prepayment',
            'paypal' => 'PayPal',
            'card' => 'Card',
            'alipay' => 'Alipay',
            'sofort' => 'Sofort',
            'klarna' => 'Klarna',
            'clicktopay' => 'Click To pay',
            'payu' => 'Payu',
        ],
        'en' => [
            'applepay' => 'Apple Pay',
            'twint' => 'Twint',
            'installment-secured' => 'Installment Secured',
            'bancontact' => 'Bancontact',
            'invoice-secured' => 'Invoice Secured',
            'invoice-guaranteed' => 'Invoice Secured',
            'invoice-factoring' => 'Invoice Secured',
            'ideal' => 'Ideal',
            'sepa-direct-debit' => 'Sepa Direct Debit',
            'post-finance-efinance' => 'Post Finance Efinance',
            'post-finance-card' => 'Post Finance Card',
            'pis' => 'Unzer Bank Transfer',
            'wechatpay' => 'WeChatPay',
            'invoice' => 'Invoice',
            'googlepay' => 'Google Pay',
            'EPS' => 'EPS',
            'eps' => 'EPS',
            'sepa-direct-debit-secured' => 'Sepa Direct Debit Secured',
            'sepa-direct-debit-guaranteed' => 'Sepa Direct Debit Guarenteed',
            'przelewy24' => 'Przelewy24',
            'giropay' => 'Giropay',
            'prepayment' => 'Prepayment',
            'paypal' => 'PayPal',
            'card' => 'Card',
            'alipay' => 'Alipay',
            'sofort' => 'Sofort',
            'klarna' => 'Klarna',
            'clicktopay' => 'Click To pay',
            'payu' => 'Payu',
        ]
    ];

    public static function getPaymentMethodLabel(string $type, $locale = null): string
    {
        if (!$locale) {
            $locale = self::getLocale();

            if (!array_key_exists($locale, self::$paymentMethodLabels)) {
                $locale = self::DEFAULT_LOCALE;
            }
        }
        return self::$paymentMethodLabels[$locale][$type] ?? 'n-a';
    }

    public static function getPaymentMethodLabelByShortcode(string $shortCode, $locale = null): string
    {
        $longCode = self::$unzerShortCodeMapping[$shortCode];
        return self::getPaymentMethodLabel($longCode, $locale);
    }

    public static function getTranslation(string $key, ...$args): ?string
    {
        $constant = self::getTranslationConstantKey($key);

        $translation = '';

        if (defined($constant)) {
            $translation = constant($constant);
            if ($args) {
                $translation = vsprintf(constant($constant), $args);
            }
        }
        return $translation;
    }

    private static function getTranslationConstantKey(string $key): string
    {

        return self::TRANSLATION_CONSTANT_PREFIX . strtoupper($key);
    }

    /**
     * Get base language code based on current locale
     *
     * @return mixed|string
     */
    public static function getLocale()
    {
        return explode('-', \Yii::$app->language)[0];
    }

}