<?php
declare(strict_types=1);

namespace AdScout\Connector\Model\Config;

use Magento\Framework\App\Config\Value;
use Magento\Store\Model\StoreManagerInterface;

class CsvUrl extends Value
{
    public function __construct(
        private StoreManagerInterface $storeManager
    ) {
    }

    public function beforeSave()
    {
        //$this->setValue($this->storeManager->getStore()->getBaseUrl());
        //return parent::beforeSave();
    }

}
