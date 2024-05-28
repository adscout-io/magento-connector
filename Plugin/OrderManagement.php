<?php
declare(strict_types=1);

namespace AdScout\Connector\Plugin;

use AdScout\Connector\Model\AdScoutApiClient;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Psr\Log\LoggerInterface;

/**
 * Class OrderManagement
 */
class OrderManagement
{
    private LoggerInterface $logger;
    private AdScoutApiClient $adScoutApiClient;

    public function __construct(
        AdScoutApiClient $adScoutApiClient,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->adScoutApiClient = $adScoutApiClient;
    }

    /**
     * @param OrderManagementInterface $subject
     * @param OrderInterface $order
     *
     * @return OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterPlace(
        OrderManagementInterface $subject,
        OrderInterface $result
    ) {
        $orderId = $result->getIncrementId();
        if ($orderId) {
            $this->logger->info('Send API call to AdScout');
            $this->adScoutApiClient->trackOrder($result);
        }
        return $result;
    }
}
