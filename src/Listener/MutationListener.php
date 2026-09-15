<?php
/**
 * @copyright 2016-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityMutation\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Hostnet\Component\EntityMutation\Attributes\Mutation;
use Hostnet\Component\EntityMutation\MutationAwareInterface;
use Hostnet\Component\EntityMutation\Resolver\MutationResolverInterface;
use Hostnet\Component\EntityTracker\Event\EntityChangedEvent;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class MutationListener
{
    public function __construct(
        private MutationResolverInterface $resolver,
        private CacheItemPoolInterface $is_mutation_cache = new ArrayAdapter()
    ) {
    }

    public function entityChanged(EntityChangedEvent $event): void
    {
        $em     = $event->getEntityManager();
        $entity = $event->getCurrentEntity();

        if (false === $strategy = $this->getMutationStrategy($em, $entity)) {
            return;
        }

        $fields = array_intersect($event->getMutatedFields(), $this->resolver->getMutatableFields($em, $entity));

        if (empty($fields)) {
            return;
        }

        if ($strategy === Mutation::STRATEGY_COPY_PREVIOUS && null === $event->getOriginalEntity()) {
            return;
        }

        $mutation_source = match ($strategy) {
            Mutation::STRATEGY_COPY_CURRENT => $entity,
            Mutation::STRATEGY_COPY_PREVIOUS => $event->getOriginalEntity(),
            default => throw new \RuntimeException(sprintf("Unknown strategy '%s'.", $strategy)),
        };

        $mutation = $em
            ->getClassMetadata($this->resolver->getMutationClassName($em, $entity))
            ->getReflectionClass()
            ->newInstance($entity, $mutation_source);

        $em->persist($mutation);

        if ($entity instanceof MutationAwareInterface) {
            $entity->addMutation($mutation);
        }
    }

    private function getMutationStrategy(EntityManagerInterface $em, object $entity): false|string
    {
        $cache_key   = base64_encode('MUTATION-' . get_class($entity));
        $cached_item = $this->is_mutation_cache->getItem($cache_key);

        if ($cached_item->isHit()) {
            return $cached_item->get();
        }

        if (null !== $attribute = $this->resolver->getMutationAttribute($em, $entity)) {
            return $this->save($cached_item, $attribute->getStrategy());
        }

        return $this->save($cached_item, false);
    }

    private function save(CacheItemInterface $item, false|string $value): false|string
    {
        $item->set($value);
        $this->is_mutation_cache->save($item);

        return $value;
    }
}
