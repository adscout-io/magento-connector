<?php
declare(strict_types=1);

namespace AdScout\Connector\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class IntegrationTypeList implements OptionSourceInterface
{
    const INTEGRATION_WITH_DNS = '1';
    const INTEGRATION_WITHOUT_DNS = '2';

    public function toOptionArray()
    {
        $integrations = [
            [
                'value' => self::INTEGRATION_WITH_DNS,
                'label' => __("Integration with DNS record"),
            ],
            [
                'value' => self::INTEGRATION_WITHOUT_DNS,
                'label' => __("Integration without DNS record"),
            ],
        ];

        return $integrations;
    }
}
