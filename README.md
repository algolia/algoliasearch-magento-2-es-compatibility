# Algolia Search for Elastic Search Compatibility

Algolia Search Elastic Search Compatibility module for Magento 2 >=2.3.1 || >=2.2.8

As Magento has begun to fully support Elastic Search as the default Search Engine, this module was created to make the Algolia Magento 2 extension compatible with this search engine. We choose to make this a separate extension as not all versions of Magento will include Elastic Search by default. Please see the chart below for compatibility:

| Magento Version | Elastic Search | Is default? |
| :----: | :----: | :----: |
| \>= 2.3.1 | 6.x (5.x, 2.x) | Yes
| < 2.3.1 | 5.2 | No
| \>= Commerce 2.2.8 | 6.x (5.x, 2.x) | Yes |
| < Commerce 2.2.3 | 5.1 | No | 
  
You can read more on Elastic Search by reading the magento devdocs here: https://devdocs.magento.com/guides/v2.3/config-guide/elasticsearch/es-overview.html

  
**Please be advised.** If you are on a version that does not have Elastic Search enabled by default, it is not necessary to switch your Search Engine to Elastic Search and install this compatibility. 

Additionally, if you are using the configuration settings "Stores > Configuration > Algolia Search > Advance > Make a backend search query" set to "No", it is not required to utilize this module. 

---

### QA Test Plan
Configuration for "Stores > Configuration > Algolia Search > Advance > Make a backend search query" must be set to "Yes".

Results can be reviewed by going to view source and seeing if the HTML rendered results reflect Algolia Search returned results.

We will be testing the adapter on all 3 versions supported by Magento.

Following this devdoc on changing versions: https://devdocs.magento.com/guides/v2.3/config-guide/elasticsearch/es-downgrade.html
