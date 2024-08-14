<?php
declare(strict_types=1);

namespace AdScout\Connector\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class AdScoutViewModel implements ArgumentInterface
{
    private ScopeConfigInterface $scopeConfig;
    private RequestInterface $request;
    private ProductMetadataInterface $productMetadata;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @return mixed
     */
    public function getApiCode()
    {
        return $this->scopeConfig->getValue(
            'ad_scout_general/general/api_code',
            ScopeInterface::SCOPE_STORE,
        );
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getDomain()
    {
        return $this->request->getHttpHost();
    }

    public function getIntegrationType()
    {
        return $this->scopeConfig->getValue(
            'ad_scout_general/general/integration',
            ScopeInterface::SCOPE_STORE,
        );
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            'ad_scout_general/general/enable',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isMagentoVersionBelow24()
    {
        $version = $this->productMetadata->getVersion();

        return version_compare($version, '2.4', '<');
    }
}
