<?php
/**
 * @copyright 2014-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityMutation\Mocked;

use Hostnet\Component\EntityMutation\Attributes\Mutation;
use Hostnet\Component\EntityMutation\MutationAwareInterface;

#[Mutation(strategy: Mutation::STRATEGY_COPY_CURRENT)]
class MockMutationEntityAttribute implements MutationAwareInterface
{
    public ?int $id = null;

    public array $mutations = [];

    #[\Override]
    public function addMutation(object $mutation): void
    {
        $this->mutations[] = $mutation;
    }

    #[\Override]
    public function getMutations(): array
    {
        return $this->mutations;
    }

    #[\Override]
    public function getPreviousMutation(): ?object
    {
        return current($this->mutations) ?: null;
    }
}
