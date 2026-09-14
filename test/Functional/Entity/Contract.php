<?php
/**
 * @copyright 2016-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Functional\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hostnet\Component\EntityMutation\Attributes\Mutation;
use Hostnet\Component\EntityMutation\MutationAwareInterface;

#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type')]
#[ORM\DiscriminatorMap([1 => HostingContract::class, 2 => DomainContract::class, 3 => Contract::class])]
#[Mutation(strategy: Mutation::STRATEGY_COPY_CURRENT)]
class Contract implements MutationAwareInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    /**
     * The history of this object.
     */
    #[ORM\OneToMany(targetEntity: ContractMutation::class, mappedBy: 'contract')]
    #[ORM\OrderBy(['id' => 'DESC'])]
    private Collection $mutations;

    public function __construct(
        #[ORM\Column(type: 'string')]
        private string $identifier,
        #[ORM\Column(type: 'integer')]
        private int $status,
    ) {
        $this->mutations = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    #[\Override]
    public function addMutation(object $mutation): void
    {
        // $this->mutations is sorted by id descending, so we should add new
        // items at the start of the Collection. Doctrine collections don't
        // allow this right now, so we add it to the end. This is fixed in
        // getMutations.
        $this->mutations->add($mutation);
    }

    /**
     * @return ContractMutation[]
     */
    #[\Override]
    public function getMutations(): array
    {
        $mutations = $this->mutations->toArray();
        usort($mutations, fn(ContractMutation $ma, ContractMutation $mb) => $mb->getId() <=> $ma->getId());
        return $mutations;
    }

    #[\Override]
    public function getPreviousMutation(): DomainContractMutation
    {
        throw new \BadMethodCallException(__METHOD__ . ' is not implemented.');
    }
}
