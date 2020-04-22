<?php

namespace Kluseg\PaymentExtender\Api;

interface MetaManagerInterface
{
    /**
     * @param string $cartId
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function guestById($cartId);

    /**
     * @param string $cartId
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function byId($cartId);
}
