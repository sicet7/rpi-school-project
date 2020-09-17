<?php

declare(strict_types=1);

namespace App\Database;

use App\Exceptions\EntityMapperException;
use App\Interfaces\EntityInterface;
use App\Interfaces\FinderFactoryInterface;
use App\Interfaces\TransientEntityInterface;
use App\Utility\ConfigManager;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadata as WritableClassMetadata;
use Doctrine\Persistence\Mapping\Driver\MappingDriver as MappingDriverInterface;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class EntityMapper
 * @package App\Database
 */
class EntityMapper implements MappingDriverInterface
{
    /**
     * @var ConfigManager
     */
    private ConfigManager $configManager;

    /**
     * @var FinderFactoryInterface
     */
    private FinderFactoryInterface $finderFactory;

    /**
     * @var string[]
     */
    private array $classnameCache;

    /**
     * EntityMapper constructor.
     * @param ConfigManager $configManager
     * @param FinderFactoryInterface $finderFactory
     */
    public function __construct(ConfigManager $configManager, FinderFactoryInterface $finderFactory)
    {
        $this->configManager = $configManager;
        $this->finderFactory = $finderFactory;
    }

    /**
     * @param string $className
     * @param ClassMetadata $metadata
     * @throws EntityMapperException
     */
    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        if (!is_subclass_of($className, EntityInterface::class, true)) {
            throw new EntityMapperException(
                'Could not load metadata for: "' . $className .
                '" as it is not an instance of "' . EntityInterface::class . '".'
            );
        }
        if ($metadata instanceof WritableClassMetadata) {
            $className::loadMetadata($metadata);
            return;
        }
        throw new EntityMapperException(
            'Could not load metadata for "' . $className . '"' .
            'The passed metadata object must be an instance of: "' . WritableClassMetadata::class . '"'
        );
    }

    /**
     * @return string[]
     */
    public function getAllClassNames()
    {
        if (!isset($this->classnameCache)) {
            $namespace = trim($this->configManager->get('database.entities.namespace'), "\\ \t\n\r\0\x0B");
            $directory = trim($this->configManager->get('database.entities.directory'));
            $finder = $this->finderFactory->create();
            $finder->files()->in($directory)->name('*.php');
            $classFqns = [];
            if ($finder->hasResults()) {
                foreach ($finder as $fileInfo) {
                    /** @var SplFileInfo */
                    $relativeFqn = str_replace('/', '\\', trim(substr(
                        $fileInfo->getRelativePathname(),
                        0,
                        (strlen($fileInfo->getRelativePathname())-4)
                    ), '/'));
                    $globalFqn = $namespace . '\\' . $relativeFqn;
                    if (is_subclass_of($globalFqn, EntityInterface::class, true) ||
                        is_subclass_of($globalFqn, TransientEntityInterface::class, true)
                    ) {
                        $classFqns[] = $globalFqn;
                    }
                }
            }
            $this->classnameCache = $classFqns;
        }
        return $this->classnameCache;
    }

    /**
     * @param string $className
     * @return bool
     */
    public function isTransient($className)
    {
        return is_subclass_of($className, TransientEntityInterface::class, true);
    }
}
