<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Iksula\MyAccount\Block\Order;

use Magento\Sales\Model\Order\Address;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;

/**
 * Invoice view  comments form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'order/info.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @var AddressRenderer
     */
    protected $addressRenderer;

    /**
     * @param TemplateContext $context
     * @param Registry $registry
     * @param PaymentHelper $paymentHelper
     * @param AddressRenderer $addressRenderer
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Registry $registry,
        PaymentHelper $paymentHelper,
        AddressRenderer $addressRenderer,
        array $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->paymentHelper = $paymentHelper;
        $this->coreRegistry = $registry;
        $this->_isScopePrivate = true;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('MY ORDERS'));
        $infoBlock = $this->paymentHelper->getInfoBlock($this->getOrder()->getPayment(), $this->getLayout());
        $this->setChild('payment_info', $infoBlock);
    }

    public function getRealOrderId()
    {
        return $this->getOrder()->getRealOrderId();
    }

    /**
     * @return string
     */
    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     * Returns string with formatted address
     *
     * @param Address $address
     * @return null|string
     */
    public function getFormattedAddress(Address $address)
    {
        return $this->addressRenderer->format($address, 'html');
    }
}
