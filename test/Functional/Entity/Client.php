<?php
/**
 * @copyright 2016-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityMutation\Functional\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hostnet\Component\EntityMutation\Attributes\Mutation;
use Hostnet\Component\EntityMutation\MutationAwareInterface;

#[ORM\Entity]
#[Mutation(strategy: Mutation::STRATEGY_COPY_CURRENT)]
class Client implements MutationAwareInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    /**
     * The history of this object.
     */
    #[ORM\OneToMany(targetEntity: ClientMutation::class, mappedBy: 'client')]
    #[ORM\OrderBy(['id' => 'DESC'])]
    private Collection $mutations;

    public function __construct(
        #[ORM\Embedded(class: ContactInfo::class)]
        private ContactInfo $contact_info,
    ) {
        $this->mutations = new ArrayCollection();
    }

    public function getContactInfo(): ContactInfo
    {
        return $this->contact_info;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setContactInfo(ContactInfo $contact_info): static
    {
        $this->contact_info = $contact_info;

        return $this;
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
     * @return ClientMutation[]
     */
    #[\Override]
    public function getMutations(): array
    {
        $mutations = $this->mutations->toArray();
        usort($mutations, fn(ClientMutation $ma, ClientMutation $mb) => $mb->getId() <=> $ma->getId());
        return $mutations;
    }

    #[\Override]
    public function getPreviousMutation(): ClientMutation
    {
        throw new \BadMethodCallException(__METHOD__ . ' is not implemented.');
    }
}
