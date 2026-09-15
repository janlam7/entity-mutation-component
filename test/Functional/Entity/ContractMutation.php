<?php
/**
 * @copyright 2016-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Functional\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type')]
#[ORM\DiscriminatorMap([
    1 => HostingContractMutation::class,
    2 => DomainContractMutation::class,
    3 => ContractMutation::class,
])]
class ContractMutation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $identifier;

    #[ORM\Column(type: 'integer')]
    private int $status;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: Contract::class, inversedBy: 'mutations')]
        #[ORM\JoinColumn]
        private Contract $contract,
        Contract $original,
    ) {
        $this->absorb($original);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getContract(): Contract
    {
        return $this->contract;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    protected function absorb(Contract $original): void
    {
        $this->identifier = $original->getIdentifier();
        $this->status     = $original->getStatus();
    }
}
