<?php

namespace S360UnzerOsCommerce4\Admin;

class Helper
{
    /**
     * get base path without admin
     */
    static public function getBase() {
        $path = \Yii::getAlias('@web');
        return str_replace('admin', '', $path);
    }

}