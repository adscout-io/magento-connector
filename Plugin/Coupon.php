<?php

namespace AdScout\Connector\Plugin;

use AdScout\Connector\Model\AdScoutApiClient;
use Magento\Quote\Model\CouponManagement;
use Psr\Log\LoggerInterface;

class Coupon
{
    private LoggerInterface $logger;
    private AdScoutApiClient $adScoutApiClient;

    public function __construct(
        AdScoutApiClient $adScoutApiClient,
        LoggerInterface $logger,
    ) {
        $this->logger = $logger;
        $this->adScoutApiClient = $adScoutApiClient;
    }

    /**
     * @param CouponManagement $subject
     * @param $result
     * @param int $cartId
     * @param string $couponCode
     */
    public function afterSet(CouponManagement $subject, $result, $cartId, $couponCode)
    {
        if ($result) {
            $this->logger->info('AdScout coupon applied');
            $this->adScoutApiClient->getParamPromoCodeRef($couponCode);
        }

        $this->logger->info('AdScout coupon not applied');

        return $result;
    }
}
