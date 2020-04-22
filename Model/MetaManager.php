<?php

namespace Kluseg\PaymentExtender\Model;

use Kluseg\PaymentExtender\Api\MetaManagerInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\GuestCartRepositoryInterface;

class MetaManager extends ManagerBase implements MetaManagerInterface
{
    /**
     * @var GuestCartRepositoryInterface
     */
    private $guestCartRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    public function __construct(GuestCartRepositoryInterface $guestCartRepository, CartRepositoryInterface $cartRepository)
    {
        $this->guestCartRepository = $guestCartRepository;
        $this->cartRepository = $cartRepository;
    }

    public function guestById($cartId)
    {
        $cart = $this->guestCartRepository->get($cartId);

        return Mage::getSingleton('sales/quote')
            ->load($cart['reserved_order_id'], 'reserved_order_id');
    }

    public function byId($cartId)
    {
    }
}
