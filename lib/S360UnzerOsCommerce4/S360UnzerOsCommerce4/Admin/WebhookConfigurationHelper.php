<?php

namespace S360UnzerOsCommerce4\Admin;

use UnzerSDK\Constants\ApiResponseCodes;
use UnzerSDK\Constants\WebhookEvents;
use UnzerSDK\Resources\Webhook;
use UnzerSDK\Unzer;
use common\modules\orderPayment\s360_unzer_oscommerce4;
use S360UnzerOsCommerce4\TranslationHelper;

class WebhookConfigurationHelper extends UnzerAdminHelper
{
    public function getConfiguration(): array
    {
        $configuration = [];
        $webhooks = $this->unzer->fetchAllWebhooks();

        /** @var Webhook $webhook */
        foreach ($webhooks as $webhook) {
            $configuration[] = [
                'webhook_id' => $webhook->getId(),
                'event' => $webhook->getEvent(),
                'url' => $webhook->getUrl(),
                'assignedToPlatform' => $this->getWebhookUrl($webhook->getEvent()) === $webhook->getUrl(),
            ];
        }

        return $configuration;
    }


    public function registerWebhooks()
    {
        $events = [
            WebhookEvents::ALL
        ];

        foreach ($events as $event) {
            $this->registerWebhook($event);
        }
    }


    /**
     * Registers a webhook with Unzer
     *
     * @param Unzer $unzer The Unzer object used for API requests
     * @param string $event The event for which the webhook should trigger
     * @param mixed $platformId (optional) The platform ID, defaults to the post value of 'platform_id'
     *
     * @return void
     */
    public function registerWebhook(string $event)
    {
        $webhookUrl = $this->getWebhookUrl($event);

        try {
            $webhook = $this->getUnzer()->createWebhook($webhookUrl, $event);
        } catch (\Exception $e) {
            if ($e->getCode() === ApiResponseCodes::API_ERROR_WEBHOOK_EVENT_ALREADY_REGISTERED) {
                $this->getMessageStack()->add_session(TranslationHelper::getTranslation('webhook_exists'), 'success');
            } else {
                $this->getMessageStack()->add_session(TranslationHelper::getTranslation('webhook_error', $e->getMessage()));
            }
        }

        if ($webhook) {
            $this->getMessageStack()->add_session(TranslationHelper::getTranslation('webhook_added', $webhook->getId()), 'success');
        }
    }

    public function deleteWebhook(string $webhookId)
    {
        try {
            $webhook = $this->getUnzer()->fetchWebhook($webhookId);
            $this->getUnzer()->deleteWebhook($webhook);
        } catch (\Exception $e) {
            $this->getMessageStack()->add_session(TranslationHelper::getTranslation('webhook_deleted', $webhook->getId()), 'success');
        }
    }


    /**
     * Get the URL for initializing webhooks
     *
     * @return string
     */
    public function getInitUrl() : string
    {
        $urlParams = [
            'modules/edit',
            'platform_id' => $this->getPlatformId(),
            'set' => 'payment',
            'module' => s360_unzer_oscommerce4::CODE,
            'action' => 'initWebhooks'
        ];

        return \Yii::$app->urlManager->createAbsoluteUrl($urlParams);
    }

    public function getDeleteUrl() : string
    {
        $urlParams = [
            'modules/edit',
            'platform_id' => $this->getPlatformId(),
            'set' => 'payment',
            'module' => s360_unzer_oscommerce4::CODE,
            'action' => 'deleteWebhook'
        ];

        return \Yii::$app->urlManager->createAbsoluteUrl($urlParams);
    }

    /**
     * Get the payment notification webhook URL
     *
     * @param string $event
     */
    private function getWebhookUrl(string $event): string
    {
        return tep_catalog_href_link('callback/webhooks.payment.' . s360_unzer_oscommerce4::CODE,
            sprintf('process=notification&platform=%d&event=%s', $this->getPlatformId(), $event),
            'SSL',
            $this->getPlatformId()
        );
    }

}