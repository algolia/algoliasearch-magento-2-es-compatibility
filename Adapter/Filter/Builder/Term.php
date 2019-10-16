<?php

namespace Algolia\AlgoliaSearchElastic\Adapter\Filter\Builder;

use Algolia\AlgoliaSearch\Helper\AdapterHelper;
use Magento\Catalog\Model\ProductFactory;
use Magento\Elasticsearch\Model\Adapter\FieldMapperInterface;
use Magento\Elasticsearch\SearchAdapter\Filter\Builder\Term as ElasticsearchTerm;
use Magento\Framework\Search\Request\Filter\Term as TermFilterRequest;
use Magento\Framework\Search\Request\FilterInterface as RequestFilterInterface;
use function PHPSTORM_META\type;

class Term extends ElasticsearchTerm
{
    /** @var AdapterHelper */
    protected $adapterHelper;

    /** @var ProductFactory */
    protected $productFactory;

    /**
     * @param FieldMapperInterface $fieldMapper
     */
    public function __construct(
        FieldMapperInterface $fieldMapper,
        AdapterHelper $adapterHelper,
        ProductFactory $productFactory
    ) {
        parent::__construct($fieldMapper);

        $this->adapterHelper = $adapterHelper;
        $this->productFactory = $productFactory;
    }

    /**
     * @param RequestFilterInterface|TermFilterRequest $filter
     * @return array
     */
    public function buildFilter(RequestFilterInterface $filter)
    {
        if (!$this->adapterHelper->isAllowed()
            || !(
                $this->adapterHelper->isSearch() ||
                $this->adapterHelper->isReplaceCategory() ||
                $this->adapterHelper->isReplaceAdvancedSearch() ||
                $this->adapterHelper->isLandingPage()
            )
        ) {
            return parent::buildFilter($filter);
        }

        $filterQuery = [];
        if ($filter->getValue()) {
            $operator = is_array($filter->getValue()) ? 'terms' : 'term';
            $fieldName = $this->fieldMapper->getFieldName($filter->getField());
            $fieldValue = $this->getFilterValue($fieldName, $filter->getValue());

            $filterQuery []= [
                $operator => [
                    $fieldName => $fieldValue,
                ],
            ];
        }
        return $filterQuery;
    }

    private function getFilterValue($attribute, $value)
    {
        $facets = $this->adapterHelper->getFacets();
        $facetAttributes = array_map(function($facet) {
            return $facet['attribute'];
        }, $facets);

        if (in_array($attribute, $facetAttributes)) {
            if (!is_array($value)) {
                // only take the first value for now
                $value = explode('~', $value)[0];
                return $this->getOptionIdByLabel($attribute, $value);
            }
        }
        return $value;
    }

    private function getOptionIdByLabel($attributeCode, $optionLabel)
    {
        $product = $this->productFactory->create();
        $attribute = $product->getResource()->getAttribute($attributeCode);
        $optionId = '';

        if ($attribute && $attribute->usesSource()) {
            $optionId = $attribute->getSource()->getOptionId($optionLabel);
        }

        return $optionId ? $optionId : $optionLabel;
    }

}
