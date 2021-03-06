<?php

namespace Emipro\Ticketsystem\Controller\Adminhtml\Ticket;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Filesystem;
use Emipro\Ticketsystem\Model\TicketSystemFactory;
use Emipro\Ticketsystem\Model\TicketAttachmentFactory;

class Checkattachmentlink extends Action {

    protected $_ticketsystem;
    protected $_attachment;
    protected $session;
    protected $_filesystem;

    public function __construct(
    Context $context, TicketSystemFactory $ticketFactory, Session $session, Filesystem $filesystem, TicketAttachmentFactory $attachmentFactory
    ) {
        $this->_ticketsystem = $ticketFactory;
        $this->_attachment = $attachmentFactory;
        $this->_filesystem = $filesystem;
        $this->session = $session;
        parent::__construct($context);
    }

    public function execute() {
        $ticketId = $this->getRequest()->getParam('ticket_id');
        $conversationId = $this->getRequest()->getParam('conversation_id');
        $attachmentId = $this->getRequest()->getParam('attach_id');
        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $UserId = $object_manager->get('\Magento\Backend\Model\Auth\Session')->getUser()->getUserId();
        if ($UserId) {
            try {
                $attachment = $this->_attachment->create()->getCollection();
                $attachment->addFieldToFilter("conversation_id", $conversationId)
                        ->addFieldToFilter("attachment_id", $attachmentId);
                $attachmentData = $attachment->getFirstItem();
                $file = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                                ->getAbsolutePath("ticketsystem/attachment/") . $attachmentData->getFile();
                if (file_exists($file)) {
                    $resourceType = \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE;
                    $helper = $this->_objectManager->create('\Magento\Downloadable\Helper\Download');
                    $helper->setResource('ticketsystem/attachment/' . $attachmentData->getFile());
                    $contentType = $helper->getContentType();


                    header('Cache-Control: public');
                    header('Content-Description: File Transfer');
                    header("Content-Disposition: attachment; filename={$attachmentData->getCurrentFileName()}");
                    header("Content-Type:{$contentType}");
                    header('Content-Transfer-Encoding: binary');
                    readfile($file);
                } else {
                    $this->messageManager->addError(__('Sorry, File not exists.'));
                    return $this->resultRedirectFactory->create()->setPath('emipro_ticketsystem/*/view', ['ticket_id' => $ticketId, '_current' => true]);
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError(__('You don\'t have permission to access this file.'));
                return $this->resultRedirectFactory->create()->setPath('emipro_ticketsystem/*/view', ['ticket_id' => $ticketId, '_current' => true]);
            }
        }
    }

}
