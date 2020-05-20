<?php

namespace Kluseg\PaymentExtender\Model;

use Kluseg\PaymentExtender\Api\MetaManagerInterface;

use Magento\Framework\App\ObjectManager;
use Magento\Payment\Api\Data\PaymentAdditionalInfoInterface;
use Magento\Payment\Api\Data\PaymentAdditionalInfoInterfaceFactory;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Metadata;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class MetaManager implements MetaManagerInterface
{
    /**
       * @var QuoteIdMaskFactory
       */
    protected $quoteIdMaskFactory;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * @var PaymentAdditionalInfoFactory
     */
    private $paymentAdditionalInfoFactory;

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * Initialize dependencies.
     *
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        Metadata $metadata,
        PaymentAdditionalInfoInterfaceFactory $paymentAdditionalInfoFactory = null,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        OrderRepositoryInterface $orderRepository,
        JsonSerializer $serializer = null
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->orderRepository = $orderRepository;
        $this->metadata = $metadata;

        $this->paymentAdditionalInfoFactory = $paymentAdditionalInfoFactory ?: ObjectManager::getInstance()
            ->get(PaymentAdditionalInfoInterfaceFactory::class);
        $this->serializer = $serializer ?: ObjectManager::getInstance()
            ->get(JsonSerializer::class);
    }

    public function guestById($cartId)
    {
        $cartId = $this->getCartId($cartId);
        $entity = $this->metadata->getNewInstance()->load($cartId, 'quote_id');

        $this->setPaymentAdditionalInfo($entity);

        return $entity;
    }

    public function byId($cartId)
    {
        $entity = $this->metadata->getNewInstance()->load($cartId, 'quote_id');

        $this->setPaymentAdditionalInfo($entity);

        return $entity;
    }

    protected function getCartId($cartId)
    {
        return $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id')->getQuoteId();
    }

    /**
     * Set additional info to the order.
     *
     * @param OrderInterface $order
     * @return void
     */
    private function setPaymentAdditionalInfo(OrderInterface $order): void
    {
        $extensionAttributes = $order->getExtensionAttributes();
        $paymentAdditionalInformation = $order->getPayment()->getAdditionalInformation();

        $objects = [];
        foreach ($paymentAdditionalInformation as $key => $value) {
            /** @var PaymentAdditionalInfoInterface $additionalInformationObject */
            $additionalInformationObject = $this->paymentAdditionalInfoFactory->create();
            $additionalInformationObject->setKey($key);

            if (!is_string($value)) {
                $value = $this->serializer->serialize($value);
            }
            $additionalInformationObject->setValue($value);

            $objects[] = $additionalInformationObject;
        }
        $extensionAttributes->setPaymentAdditionalInfo($objects);
        $order->setExtensionAttributes($extensionAttributes);
    }
}
