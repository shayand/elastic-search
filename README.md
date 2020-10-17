# PHP Elastic Search with latest Client version

This project is created to work with Elastic Search and have sample for creating index and types and populate and search with php client 

## Summary


* Main Goal: **`To guide new users of elastic search `**
* Includes:
  * `Elastic Search` DB with single node or multi node cluster
  * `Silex` Symfony based PHP micro-framework
  * `Guzzle` Rest API client
  * `Elasticsearch library for php` formal package for working with elastic search
  
## Getting Started
For creating index with Rest API (Parameter `-i` select index name)
~~~ 
php bin\console index:create-rest -i newtest
~~~

For creating index with PHP library (Parameter `-i` select index name)

~~~ 
php bin\console index:create-client -i newtest
~~~ 

For inserting document (row) in index and type (Parameter `-i` select index name)
~~~ 
php bin\console index:populate -i newtest
~~~ 

For getting document (row) in index and type (Parameter `-i` select index name)
~~~ 
php bin\console index:get -i newtest
~~~ 

For updating a document (Parameter `-i` select index name)
~~~ 
php bin\console index:update -i newtest
~~~  