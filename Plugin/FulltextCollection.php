<?php

namespace Algolia\AlgoliaSearchElastic\Plugin;

use Algolia\AlgoliaSearch\Helper\AdapterHelper;
use Magento\Catalog\Model\ProductFactory;

class FulltextCollection
{
    /** @var AdapterHelper */
    private $adapterHelper;

    /** @var ProductFactory */
    private $productFactory;

    /** @var \Magento\Catalog\Model\Product */
    private $product;

    private $facets;

    /**
     * @param AdapterHelper $adapterHelper
     */
    public function __construct(
        AdapterHelper $adapterHelper,
        ProductFactory $productFactory
    ) {
        $this->adapterHelper = $adapterHelper;
        $this->productFactory = $productFactory;
    }

    public function replaceClient()
    {
        if (!$this->adapterHelper->isAllowed()
            || !$this->adapterHelper->isInstantEnabled()
            || !(
                $this->adapterHelper->isSearch() ||
                $this->adapterHelper->isReplaceCategory() ||
                $this->adapterHelper->isReplaceAdvancedSearch() ||
                $this->adapterHelper->isLandingPage()
            )
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $subject
     * @param $field
     * @param null $condition
     */
    public function beforeAddFieldToFilter(
        \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $subject,
        $field,
        $condition = null
    ) {
        if (!$condition || !$this->replaceClient()) {
            return [$field, $condition];
        }

        if (in_array($field, $this->getFacets())) {
            $condition = $this->getOptionIdByLabel($field, $condition);
        }

        return [$field, $condition];
    }

    /**
     * @param string $attributeCode
     * @param null $optionLabel
     * @return string
     */
    private function getOptionIdByLabel($attributeCode, $optionLabel = null)
    {
        if ($optionLabel && !is_array($optionLabel)) {
            $product = $this->getProduct();
            $isAttributeExist = $product->getResource()->getAttribute($attributeCode);
            if ($isAttributeExist && $isAttributeExist->usesSource()) {
                $optionLabel = $isAttributeExist->getSource()->getOptionId($optionLabel);
            }
        }

        return $optionLabel;
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->product) {
            $this->product = $this->productFactory->create();
        }

        return $this->product;
    }

    public function getFacets()
    {
        if (!$this->facets) {
            $facets = [];
            $configFacets = $this->adapterHelper->getFacets();
            if (is_array($configFacets) && count($configFacets)) {
                $facets = array_map(function ($facet) {
                    return $facet['attribute'];
                }, $configFacets);
            }

            $this->facets = $facets;
        }

        return $this->facets;
    }

}
