<?php
declare(strict_types=1);

namespace AdScout\Connector\Cron;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Category\Tree;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class GenerateCsvCronjob
{
    private ProductRepositoryInterface $productRepository;
    private LoggerInterface $logger;
    private SearchCriteriaInterface $searchCriteria;
    private FilterGroup $filterGroup;
    private Status $productStatus;
    private FilterBuilder $filterBuilder;
    private Visibility $productVisibility;
    private StoreManagerInterface $storemanager;
    private Tree $tree;
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        Tree $tree,
        StoreManagerInterface $storemanager,
        \Magento\Framework\File\Csv $csvProcessor,
        DirectoryList $directoryList,
        \Magento\Framework\Filesystem $filesystem,
        SearchCriteriaInterface $criteria,
        FilterGroup $filterGroup,
        FilterBuilder $filterBuilder,
        Status $productStatus,
        Visibility $productVisibility,
        ProductRepositoryInterface $productRepository,
        LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
        $this->directoryList = $directoryList;
        $this->csvProcessor = $csvProcessor;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->searchCriteria = $criteria;
        $this->filterGroup = $filterGroup;
        $this->filterBuilder = $filterBuilder;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->storemanager = $storemanager;
        $this->tree = $tree;
        $this->categoryRepository = $categoryRepository;
    }

    private function getProductData()
    {

        $this->filterGroup->setFilters([
            $this->filterBuilder
                ->setField('status')
                ->setConditionType('in')
                ->setValue($this->productStatus->getVisibleStatusIds())
                ->create(),
            $this->filterBuilder
                ->setField('visibility')
                ->setConditionType('in')
                ->setValue($this->productVisibility->getVisibleInSiteIds())
                ->create(),
        ]);

        $this->searchCriteria->setFilterGroups([$this->filterGroup]);
        $products = $this->productRepository->getList($this->searchCriteria);

        return $products->getItems();
    }

    /**
     * Cronjob Description
     *
     * @return void
     */
    public function execute(): void
    {
        $this->logger->info('Cron AdScout Csv Start');
        $fileDirectoryPath = $this->directoryList->getPath(DirectoryList::PUB);

        $fileName = 'adscout.csv';
        $filePath = $fileDirectoryPath . '/' . $fileName;

        $data = $this->getProductData();
        $csvData = [];
        $csvData[] = [
            'title',
            'sku',
            'product_id',
            'link',
            'image',
            'category',
            'brand',
            'price',
            'sale_price',
            'related_products'
        ];

        /** @var Product $product */
        foreach ($data as $product) {
            $productImageUrl = $this->storemanager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) .
                'catalog/product' . $product->getImage();
            $csvData[] = [
                'title'            => $product->getName(),
                'sku'              => $product->getSku(),
                'product_id'       => $product->getId(),
                'link'             => $product->getProductUrl(),
                'image'            => $productImageUrl,
                'category'         => $this->getCategoryTree($product->getCategoryIds()),
                'brand'            => '',
                'price'            => $product->getPrice(),
                'sale_price'       => $product->getSpecialPrice(),
                'related_products' => $this->getRelatedProductsSku($product->getRelatedProducts())
            ];
        }
        /* pass data array to write in csv file */
        $this->csvProcessor
            ->setEnclosure('"')
            ->setDelimiter(',')
            ->appendData($filePath, $csvData);

        $this->logger->info('Cron AdScout Csv End');
    }

    /**
     * @param array $relatedProducts
     * @return string
     */
    private function getRelatedProductsSku(array $relatedProducts)
    {
        $skus = [];
        foreach ($relatedProducts as $product) {
            $skus[] = $product->getSku();
        }
        if (count($skus)) {
            return implode(',', $skus);
        }

        return '';
    }

    private function getCategoryTree($categoryIds)
    {
        $categoriesTree = '';
        $storeId = $this->storemanager->getStore()->getId();

        foreach ($categoryIds as $categoryId) {
            $category = $this->categoryRepository->get($categoryId, $storeId);
            $categoryTree = $this->tree->setStoreId($storeId)->loadBreadcrumbsArray($category->getPath());

            $categoryTreePath = '';
            foreach ($categoryTree as $eachCategory) {
                $categoryTreePath = $categoryTreePath . '/' . $eachCategory['name'];
            }
            $categoriesTree = $categoryTreePath . ';';
        }

        return $categoriesTree;
    }
}
