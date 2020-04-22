<?php

namespace Kluseg\PaymentExtender\Model;

use Kluseg\PaymentExtender\Api\MetaManagerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\GuestCartRepositoryInterface;

class MetaManager extends ManagerBase implements MetaManagerInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var GuestCartRepositoryInterface
     */
    private $guestCartRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    public function __construct(OrderRepositoryInterface $orderRepository, GuestCartRepositoryInterface $guestCartRepository, CartRepositoryInterface $cartRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->guestCartRepository = $guestCartRepository;
        $this->cartRepository = $cartRepository;
    }

    public function guestById($cartId)
    {
        return $this->guestCartRepository->get($cartId);
    }

    public function byId($cartId)
    {
    }
}
