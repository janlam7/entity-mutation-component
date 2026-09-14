<?php
/**
 * @copyright 2016-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Functional\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hostnet\Component\EntityMutation\Attributes\Mutation;

#[ORM\Entity]
#[Mutation(strategy: Mutation::STRATEGY_COPY_CURRENT)]
class HostingContract extends Contract
{
    public function __construct(
        string $identifier,
        int $status,
        #[ORM\Column(type: 'string')]
        private string $service,
    ) {
        parent::__construct($identifier, $status);
    }

    public function getService(): string
    {
        return $this->service;
    }
}
