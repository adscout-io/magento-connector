<?php
declare(strict_types=1);

namespace AdScout\Connector\Observer;

use AdScout\Connector\Model\AdScoutApiClient;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\AbstractModel;
use Psr\Log\LoggerInterface;

class OrderComplete implements ObserverInterface
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

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order instanceof AbstractModel) {
            if ($order->getState() == 'complete') {
                $this->logger->info('Send API call to AdScout order complete.');
                $this->adScoutApiClient->changeOrderStatus($order);
                //Your code here
            }
        }
        return $this;
    }
}
