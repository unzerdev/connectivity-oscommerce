<?php


namespace S360UnzerOsCommerce4\Admin;

use common\classes\MessageStack;
use UnzerSDK\Unzer;

abstract class UnzerAdminHelper
{
    protected Unzer $unzer;

    protected int $platformId;

    public function __construct(Unzer $unzer, $platformId)
    {
        $this->unzer = $unzer;
        $this->platformId = (int) $platformId;
    }

    protected function getPlatformId(): int
    {
        return $this->platformId;
    }

    protected function getUnzer() : Unzer
    {
        return $this->unzer;
    }

    protected function getMessageStack(): MessageStack
    {
        return \Yii::$container->get('message_stack');
    }
}