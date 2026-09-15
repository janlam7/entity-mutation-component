<?php
/**
 * @copyright 2016-present Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\EntityMutation\Functional\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class ContactInfo
{
    public function __construct(
        #[ORM\Column(type: 'string')]
        private string $address_line,
        #[ORM\Column(type: 'string')]
        private string $name,
        #[ORM\Column(type: 'datetime')]
        private \DateTime $created_at,
    ) {
    }

    public function getAddressLine(): string
    {
        return $this->address_line;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setAddressLine(string $address_line): static
    {
        $this->address_line = $address_line;

        return $this;
    }
}
