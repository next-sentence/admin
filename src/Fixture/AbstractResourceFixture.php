<?php

declare(strict_types=1);

namespace App\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use App\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractResourceFixture implements FixtureInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var ExampleFactoryInterface
     */
    protected $exampleFactory;
    /**
     * @var OptionsResolver
     */
    protected $optionsResolver;
    /**
     * @var CsvReader
     */
    private $csvReader;
    /**
     * @var null|string
     */
    private $path;

    /**
     * AbstractResourceFixture constructor.
     * @param ObjectManager $objectManager
     * @param ExampleFactoryInterface $exampleFactory
     * @param null|string $path
     * @throws \Exception
     */
    public function __construct(
        ObjectManager $objectManager,
        ExampleFactoryInterface $exampleFactory,
        ?string $path = ''
    )
    {
        $this->objectManager = $objectManager;
        $this->exampleFactory = $exampleFactory;
        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('random', 0)
                ->setAllowedTypes('random', 'int')
                ->setDefault('prototype', [])
                ->setAllowedTypes('prototype', 'array')
                ->setDefault('custom', [])
                ->setAllowedTypes('custom', 'array')
                ->setNormalizer('custom', function (Options $options, array $custom) {
                    if ($options['random'] <= 0) {
                        return $custom;
                    }
                    return array_merge($custom, array_fill(0, $options['random'], $options['prototype']));
                })
        ;

        $this->path = sprintf('%s/%s.csv', $path, $this->getName());
        if (file_exists($this->path)) {
            $file = new \SplFileObject($this->path);
            $this->csvReader = new CsvReader($file);
            $this->csvReader->setHeaderRowNumber(0, CsvReader::DUPLICATE_HEADERS_MERGE);
        }
    }

    /**
     * @param array $options
     */
    public function load(array $options): void
    {
        $this->objectManager->getConnection()->getConfiguration()->setSQLLogger(null);

        if ($this->csvReader) {
            $resources = $this->csvReader;
        } else {
            $options = $this->optionsResolver->resolve($options);
            $resources = $options['custom'];
        }

        $i = 0;
        foreach ($resources as $resourceOptions) {
            $resource = $this->exampleFactory->create($resourceOptions);
            $this->objectManager->persist($resource);
            ++$i;
            if (0 === ($i % 10)) {
                $this->objectManager->flush();
                $this->objectManager->clear();
            }
        }
        $this->objectManager->flush();
        $this->objectManager->clear();
    }

    /**
     * {@inheritdoc}
     */
    final public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $optionsNode = $treeBuilder->root($this->getName());
        $optionsNode->children()->integerNode('random')->min(0)->defaultValue(0);
        /** @var ArrayNodeDefinition $resourcesNode */
        $resourcesNode = $optionsNode->children()->arrayNode('custom');
        /** @var ArrayNodeDefinition $resourceNode */
        $resourceNode = $resourcesNode->requiresAtLeastOneElement()->prototype('array');
        $this->configureResourceNode($resourceNode);
        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $resourceNode
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        // empty
    }
}