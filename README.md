# eZORMBundle

This bundle tries to provide an ORM-like PHP API to Query and Manage Content stored in eZ Platform. 
The main purpose is to ease the Symfony Developer's life by providing classic Symfony+Doctrine features like Entities, Forms, Validation... Here is a list of the main features:

* Fluent Query API
* Entity mapping to eZ Content
* Form builders
* ContentType migrations (like doctrine:schema:update)

Obviously this added abstraction layer isn't designed for performance. eZ, like any other Symfony application, requires proper HTTP caching and a decent environment configuration to perform at its best. We still consider performance as a key feature but we are aware that this ORM-thing will not perform as fast as raw eZ API usage, and you should be aware of this too. 

This bundle is just a handy toolkit when building Forms, querying the API and manage ContentTypes. The API is SQL-oriented for ease of use and fast learning curve but the Content model of eZ Platform isn't SQL which leads to some limitations or differences:

* A Content (sql: row) can have different values with the same ID (versions, translations)
* A ContentType can be considered like a SQL table when doing a SELECT but writes are sometime requiring a Location (INSERT) and/or a special state (UPDATE)
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

$articles = $this->get('ezorm.connection')->execute($query);
```

## ORM Magic

The above example will return ```stdClass``` instances.
To return custom entities ("Content Types") we just have to map them just like we commonly do with Doctrine.

```php
<?php
namespace Acme\ExampleBundle\Entity;

use Nitronet\eZORMBundle\ORM\Mapping as eZORM;


/**
 * Article
 * eZ's "article" content-type
 *
 * @eZORM\Entity()
 * @eZORM\ContentType(
 *     identifier="article",
 *     mainLanguageCode="eng-GB",
 *     urlAlias="<short_title|title>",
 *     container=true,
 *     description="Blog Article"
 * )
 */
class Article
{
    /**
     * @eZORM\Field(
     *     name="Title",
     *     identifier="title",
     *     type="ezstring",
     *     description="Title of article",
     *     container=false,
     *     translatable=true,
     *     searchable=true,
     *     position=1,
     *     settings={"maxLength": 0}
     * )
     * @var string
     */
    public $title;

    /**
     * @eZORM\Field(
     *     name="short_title",
     *     identifier="short_title",
     *     type="ezstring",
     *     description="Short title of article",
     *     container=false,
     *     translatable=true,
     *     searchable=true,
     *     position=2,
     *     settings={"maxLength": 255}
     * )
     * @var string
     */
    public $shortTitle;

    /**
     * @eZORM\Field(
     *     name="author",
     *     identifier="author",
     *     type="ezauthor",
     *     description="Title of article",
     *     container=false,
     *     translatable=true,
     *     searchable=true,
     *     position=3,
     *     settings={"maxLength": 0}
     * )
     * @var string
     */
    public $author;

    /**
     * @eZORM\Field(
     *     name="intro",
     *     identifier="intro",
     *     type="ezrichtext",
     *     description="Intro of article",
     *     container=false,
     *     translatable=true,
     *     searchable=true,
     *     position=4
     * )
     * @var string
     */
    public $intro;

    /**
     * @eZORM\Field(
     *     name="body",
     *     identifier="body",
     *     type="ezrichtext",
     *     description="Body of article",
     *     container=false,
     *     translatable=true,
     *     searchable=true,
     *     position=5
     * )
     * @var string
     */
    public $body;

    /**
     * @eZORM\Field(
     *     name="image",
     *     identifier="image",
     *     type="ezobjectrelation",
     *     description="Image of article",
     *     container=false,
     *     translatable=true,
     *     searchable=true,
     *     position=6
     * )
     * @var string
     */
    public $image = null;

    /**
     * @eZORM\Field(
     *     name="enable comments",
     *     identifier="enable_comments",
     *     type="ezboolean",
     *     description="Enable commentse",
     *     container=false,
     *     translatable=false,
     *     searchable=true,
     *     position=7
     * )
     * @var string
     */
    public $enableComments = false;

    /**
     * @eZORM\MetaField(service="ezorm.metafield.content_id")
     * @var int
     */
    public $_contentId;
}
```

Tada! Our previous example will now return ``Àrticle`` instances.