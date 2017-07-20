<?php


namespace ClaaslocBundle\DependencyInjection\Compiler;


use ClaaslocBundle\Annotation\Normalize;
use ClaaslocBundle\Annotation\Uploadable;
use ClaaslocBundle\Entity\Machine;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EntitiesSchemasBuilderCompilerPass implements CompilerPassInterface
{
    /** @var FilesystemCache */
    private $cache;

    /** @var AnnotationReader */
    private $annotationReader;

    /** @var ClassMetadata */
    private $metadata;

    /** @var \ReflectionClass */
    private $reflectionClass;

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->cache = $container->get('ezorm.cache.compilation');
        $this->annotationReader = $container->get('annotation_reader');
        
        $this->metadata = $container->get('doctrine')->getManager()->getMetadataFactory()
            ->getMetadataFor(Machine::class);
        $this->reflectionClass = new \ReflectionClass(Machine::class);

        $this->cacheUploadableAnnotation();
        $this->cacheNormalizerAnnotation();
    }

    /**
     * Cache for properties with the Uploadable annotation
     */
    private function cacheUploadableAnnotation()
    {
        $uploadableFields = [];

        foreach ($this->reflectionClass->getProperties() as $prop) {
            $annotation = $this->annotationReader->getPropertyAnnotation($prop, Uploadable::class);
            if ($annotation instanceof Uploadable) {
                $uploadableFields[$this->metadata->columnNames[$prop->name]] = $this->metadata->columnNames[$prop->name];
            }
        }

        $this->cache->save('machine_uploadable_fields', $uploadableFields);
    }

    /**
     * Cache for the properties normalizers
     */
    private function cacheNormalizerAnnotation()
    {
        $fieldNormalizers = [];

        foreach ($this->reflectionClass->getProperties() as $prop) {
            $annotation = $this->annotationReader->getPropertyAnnotation($prop, Normalize::class);
            if ($annotation instanceof Normalize) {
                $fieldNormalizers[$prop->name] = $annotation->getCallback();
            }
        }

        $this->cache->save('machine_property_normalizers', $fieldNormalizers);
    }
}