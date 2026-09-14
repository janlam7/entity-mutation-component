<?php
/**
 * @copyright 2016-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityMutation\Resolver;

use Doctrine\ORM\EntityManagerInterface;
use Hostnet\Component\EntityMutation\Attributes\Mutation;
use Hostnet\Component\EntityTracker\Provider\EntityMetadataProvider;

class MutationResolver implements MutationResolverInterface
{
    public function __construct(private EntityMetadataProvider $provider)
    {
    }

    #[\Override]
    public function getMutationAttribute(EntityManagerInterface $em, object $entity): ?Mutation
    {
        return $this->provider->getAttributeFromEntity(Mutation::class, $em, $entity);
    }

    #[\Override]
    public function getMutationClassName(EntityManagerInterface $em, object $entity): string
    {
        return get_class($entity) . 'Mutation';
    }

    #[\Override]
    public function getMutatableFields(EntityManagerInterface $em, object $entity): array
    {
        $mutation_class = $this->getMutationClassName($em, $entity);
        $metadata       = $em->getClassMetadata(get_class($entity));
        $mutation_meta  = $em->getClassMetadata($mutation_class);

        return array_merge(
            array_values(array_intersect(
                $metadata->getFieldNames(),
                $mutation_meta->getFieldNames()
            )),
            array_values(array_intersect(
                $metadata->getAssociationNames(),
                $mutation_meta->getAssociationNames()
            ))
        );
    }
}
