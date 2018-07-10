<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Order;

use Sylius\Behat\Page\SymfonyPage;
use Behat\Mink\Element\NodeElement;

final class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_account_order_show';
    }

    public function downloadFirstInvoice(): void
    {
        $invoice = $this->getFirstInvoice();
        $invoice->clickLink('Download');
    }

    public function isPdfFileDownloaded(): bool
    {
        $session = $this->getSession();
        $headers = $session->getResponseHeaders();

        return
            200 === $session->getStatusCode() &&
            'application/pdf' === $headers['content-type'][0]
        ;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'invoices' => '#order-invoices',
        ]);
    }

    private function getFirstInvoice(): NodeElement
    {
        return $this->getInvoicesList()[1];
    }

    private function getInvoicesList(): array
    {
        return $this->getElement('invoices')->findAll('css', 'tr');
    }
}