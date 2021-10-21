# Algolia Search for Elastic Search Compatibility

**/!\ This module is experimental and no longer in active development / maintenance.**

Algolia Search Elastic Search Compatibility module for Magento 2 >=2.3.1 || >=2.2.8

As Magento has begun to fully support Elastic Search as the default Search Engine, this module was created to make the Algolia Magento 2 extension compatible with this search engine. We choose to make this a separate extension as not all versions of Magento will include Elastic Search by default. Please see the chart below for compatibility:

| Magento Version | Elastic Search | Is default? |
| :----: | :----: | :----: |
| \>= 2.3.1 | 6.x | Yes
| < 2.3.1 | 5.2 | No
| \>= Commerce 2.2.8 | 6.x | Yes |
| < Commerce 2.2.3 | 5.1 | No | 
  
You can read more on Elastic Search by reading the magento devdocs here: https://devdocs.magento.com/guides/v2.3/config-guide/elasticsearch/es-overview.html

  
**Please be advised.** If you are on a version that does not have Elastic Search enabled by default, it is not necessary to switch your Search Engine to Elastic Search and install this compatibility. 

Additionally, if you are using the configuration settings "Stores > Configuration > Algolia Search > Advance > Make a backend search query" set to "No", it is not required to utilize this module. 

---

### Features dependent on this compatibility
#### Backend Search 
Algolia will replace the results returned by the search adapter for Elastic Search. 

You can test for backend search by changing your configuration for "Stores > Configuration > Algolia Search > Advance > Make a backend search query" to "Yes".

Results can be reviewed by going to view source and seeing if the HTML rendered results reflect Algolia Search returned results.


#### Backend Facet Rendering 
If instantsearch is disabled, and you have enabled *Enable Backend Facet Rendering*, the filtering on the catalogsearch results page will be replaced by facets returned by Algolia. 

This feature is currently not compatible with this external module and is still Work In Progress. 