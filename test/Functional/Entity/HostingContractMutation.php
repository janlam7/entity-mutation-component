<?php
/**
 * @copyright 2016-2017 Hostnet B.V.
 */
namespace Functional\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class HostingContractMutation extends ContractMutation
{
    /**
     * @ORM\Column(type="string")
     */
    private $service;

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param Contract $original
     */
    protected function absorb(Contract $original)
    {
        if (!($original instanceof HostingContract)) {
            throw new \InvalidArgumentException(sprintf(
                'HostingContractMutation can only be created from HostingContract entities, "%s" given',
                get_class($original)
            ));
        }

        parent::absorb($original);

        $this->service = $original->getService();
    }
}
