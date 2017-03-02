# eZORMBundle

This bundle tries to provide an ORM-like PHP API to Query and Manage Content stored in eZ Platform. 
The main purpose is to ease the Symfony Developer's life by providing classic Symfony+Doctrine features like Entities, Forms, Validation... Here is a list of the main features:

* Fluent Query API
* Entity mapping to eZ Content
* Form builders
* ContentType migrations (like doctrine:schema:update)

Obviously this added abstraction layer isn't designed for performance. eZ, like any other Symfony application, requires proper HTTP caching and a decent environment configuration to perform at its best. We still consider performance as a key feature but we are aware that this ORM-thing will not perform as fast as raw eZ API usage, and you should be aware of this too. 

Also, the API is SQL-oriented for ease of use and fast learning curve. However, the Content model of eZ Platform isn't SQL and thus implies some limitations or differences:

* A Content (sql: row) can have different values with the same ID (versions, translations)
* A ContentType can be considered like a SQL table when doing a SELECT but writes are sometimes requiring a Location or a special state (drafts)
* "Persistence" is a lie (but no one really cares)


## Query example

Classic eZ Query:
```php 
use eZ\Publish\API\Repository\Values\Content\Query as eZQ;

$query = new \eZ\Publish\API\Repository\Values\Content\Query();
$query->limit = 5;
$query->offset = 10;
$query->filter = new eZQ\Criterion\LogicalAnd(array(
    new eZQ\Criterion\ContentTypeIdentifier('article'),
    new eZQ\Criterion\Visibility(eZQ\Criterion\Visibility::VISIBLE)
));
$query->sortClauses = array(new eZQ\SortClause\DateModified());

$results = $this->get('ezpublish.api.service.search')->findContent($query);

$articles = array();
foreach ($results->searchHits as $searchHit) {
    $articles[] = $searchHit->valueObject;
}
```

eZORM Query:
```php
use eZ\Publish\API\Repository\Values\Content\Query as eZQ;
use Nitronet\eZORMBundle\ORM\Query;

$query = new Query();
$query->select()
    ->where(new eZQ\Criterion\ContentTypeIdentifier('article'))
    ->andWhere(new eZQ\Criterion\Visibility(eZQ\Criterion\Visibility::VISIBLE))
    ->limit("10,5")
    ->orderBy(new eZQ\SortClause\DateModified())
;

$articles = $this->get('ezorm.connection')->execute($query, Query::FETCH_CONTENT, $lang = null);
```
