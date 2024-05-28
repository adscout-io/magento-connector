<?php

namespace AdScout\Connector\Model;

use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class AdScoutApiClient
{
    public const TRACK_ORDER_URI = 'https://adscout.io/api/track-order';
    public const CHANGE_ORDER_STATUS = 'https://adscout.io/api/change-order-status';
    private ClientFactory $clientFactory;
    private ScopeConfigInterface $scopeConfig;
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        ClientFactory $clientFactory
    ) {
        $this->clientFactory = $clientFactory;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    public function trackOrder(OrderInterface $order)
    {
        if (isset($_COOKIE['ScoutSRef'])) {
            $adRef = $_COOKIE['ScoutSRef'];

        } else {
            return false;
        }

        /** @var Client $client */
        $client = $this->clientFactory->create();

        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $this->getAccessToken()
        ];

        $products = [];
        foreach ($order->getAllItems() as $item) {
            $products[] = [
                "id"  => "" . $item->getItemId(),
                "sku" => $item->getSku(),
                "qty" => $item->getQtyOrdered(),
                "p"   => "" . $item->getPrice(),
                "sp"  => "" . $item->getPrice()
            ];
        }

        $body = [
            "adsRef"      => "" . $adRef,
            "amount"      => $order->getGrandTotal() - $order->getShippingAmount(),
            "currency"    => "" . $order->getOrderCurrencyCode(),
            "orderNumber" => $order->getIncrementId(),
            "orderType"   => "n",
            "orderUpdate" => "n",
            "products"    => $products
        ];
//$this->logger->info('AdScout API response body: ' . print_r(json_encode($body), true));
        try {
            $this->logger->info('AdScout API start ');
            $response = $client->post(self::TRACK_ORDER_URI, [
                'body'    => json_encode($body), // Send data as JSON
                'headers' => $headers,
            ]);

            $this->logger->info('AdScout API response track order: ' . $response->getStatusCode());

            $response = $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            $this->logger->info('AdScout API response error: ' . $e->getMessage());
        }

        return true;
    }

    private function getAccessToken()
    {
        return $this->scopeConfig->getValue(
            'ad_scout_general/general/api_token',
            ScopeInterface::SCOPE_STORE,
        );
    }

    public function changeOrderStatus(AbstractModel $order)
    {
        /** @var Client $client */
        $client = $this->clientFactory->create();

        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $this->getAccessToken()
        ];

        $body = [
            "orderNumber" => $order->getIncrementId(),
            "status"      => "complete",
        ];
        $this->logger->info('AdScout API response body: ' . print_r(json_encode($body), true));
        try {
            $this->logger->info('AdScout API start ');
            $response = $client->post(self::CHANGE_ORDER_STATUS, [
                'body'    => json_encode($body), // Send data as JSON
                'headers' => $headers,
            ]);

            $this->logger->info('AdScout API response change order status: ' . $response->getStatusCode());

            $response = $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            $this->logger->info('AdScout API response error: ' . $e->getMessage());
        }
    }
}
