<?php
/**
 * @copyright 2014-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityMutation\Resolver;

use Doctrine\ORM\EntityManagerInterface;
use Hostnet\Component\EntityMutation\Attributes\Mutation;

interface MutationResolverInterface
{
    public function getMutationAttribute(EntityManagerInterface $em, $entity): ?Mutation;

    /**
     * Return the mutation class name
     */
    public function getMutationClassName(EntityManagerInterface $em, $entity): string;

    /**
     * Return list of mutatable fields
     *
     * @return string[]
     */
    public function getMutatableFields(EntityManagerInterface $em, $entity): array;
}
