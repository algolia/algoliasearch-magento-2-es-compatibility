<?php

namespace Algolia\AlgoliaSearchElastic\Adapter;

use Algolia\AlgoliaSearch\Helper\AdapterHelper;
use Algolia\AlgoliaSearchElastic\Helper\ElasticAdapterHelper;
use Magento\Elasticsearch\Elasticsearch5\SearchAdapter\Adapter as ElasticSearch5Adapter;
use Magento\Elasticsearch\Elasticsearch5\SearchAdapter\Mapper;
use Algolia\AlgoliaSearchElastic\Adapter\Aggregation\Builder as AggregationBuilder;
use Magento\Elasticsearch\SearchAdapter\ConnectionManager;
use Magento\Elasticsearch\SearchAdapter\ResponseFactory;
use Magento\Elasticsearch\SearchAdapter\QueryContainerFactory;
use Magento\Framework\Search\RequestInterface;
use Psr\Log\LoggerInterface;

class AlgoliaElasticSearch5Adapter extends ElasticSearch5Adapter
{
    /** @var AdapterHelper */
    private $adapterHelper;

    /** @var ElasticAdapterHelper */
    private $esAdapterHelper;

    /** @var QueryContainerFactory */
    private $queryContainerFactory;

    /**
     * AlgoliaElasticSearch5Adapter constructor.
     * @param ConnectionManager $connectionManager
     * @param Mapper $mapper
     * @param ResponseFactory $responseFactory
     * @param AggregationBuilder $aggregationBuilder
     * @param QueryContainerFactory $queryContainerFactory
     * @param LoggerInterface|null $logger
     * @param AdapterHelper $adapterHelper
     * @param ElasticAdapterHelper $esAdapterHelper
     */
    public function __construct(
        ConnectionManager $connectionManager,
        Mapper $mapper,
        ResponseFactory $responseFactory,
        AggregationBuilder $aggregationBuilder,
        QueryContainerFactory $queryContainerFactory,
        LoggerInterface $logger,
        AdapterHelper $adapterHelper,
        ElasticAdapterHelper $esAdapterHelper
    ) {

        parent::__construct($connectionManager, $mapper, $responseFactory, $aggregationBuilder, $queryContainerFactory,
            $logger);
        
        $this->adapterHelper = $adapterHelper;
        $this->esAdapterHelper = $esAdapterHelper;
        $this->queryContainerFactory = $queryContainerFactory;

    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\Search\Response\QueryResponse
     */
    public function query(RequestInterface $request)
    {
        if (!$this->esAdapterHelper->replaceElasticSearchResults()) {
            return parent::query($request);
        }

        $aggregationBuilder = $this->aggregationBuilder;
        $query = $this->mapper->buildQuery($request);

        $aggregationBuilder->setQuery($this->queryContainerFactory->create(['query' => $query]));

        $rawResponse = [];
        $totalHits = 0;
        $table = null;

        try {
            // If instant search is on, do not make a search query unless SEO request is set to 'Yes'
            if (!$this->adapterHelper->isInstantEnabled() || $this->adapterHelper->makeSeoRequest()) {
                list($rawResponse, $totalHits, $facets) = $this->adapterHelper->getDocumentsFromAlgolia();
                $rawResponse = $this->transformResponseForElastic($rawResponse);
            }

        } catch (AlgoliaConnectionException $e) {
            return parent::query($request);
        }

        $aggregationBuilder->setFacets($facets);
        $aggregations = $aggregationBuilder->build($request, $rawResponse);

        $response = [
            'documents' => $rawResponse,
            'aggregations' => $aggregations,
            'total' => $totalHits,
        ];

        return $this->responseFactory->create($response);
    }

    /**
     * @param array $rawResponse
     * @return array
     */
    private function transformResponseForElastic(array $rawResponse)
    {
        if (count($rawResponse) > 0) {
            foreach ($rawResponse as &$hit) {
                $hit['_id'] = $hit['entity_id'];
            }
        }

        $rawResponse['hits'] = ['hits' => $rawResponse];

        return $rawResponse;
    }
}
