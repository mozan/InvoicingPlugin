<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Ui\Action;

use Knp\Snappy\GeneratorInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DownloadInvoiceAction
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    /** @var EngineInterface */
    private $templatingEngine;

    /** @var GeneratorInterface */
    private $pdfGenerator;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        EngineInterface $templatingEngine,
        GeneratorInterface $pdfGenerator,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->templatingEngine = $templatingEngine;
        $this->pdfGenerator = $pdfGenerator;
        $this->channelRepository = $channelRepository;
    }

    public function __invoke(Request $request, string $id): Response
    {
        $invoice = $this->invoiceRepository->get($id);
        $filename = str_replace('/', '_', $invoice->number());

        $channel = $this->channelRepository->findOneByCode($invoice->channel()->getCode());

        $response = new Response($this->pdfGenerator->getOutputFromHtml(
            $this->templatingEngine->render('@SyliusInvoicingPlugin/Resources/views/Invoice/Download/pdf.html.twig', [
                'invoice' => $invoice,
                'channel' => $channel,
            ])
        ));

        $response->headers->add(['Content-Type' => 'application/pdf']);
        $response->headers->add(['Content-Disposition' => $response->headers->makeDisposition('attachment', $filename . '.pdf')]);

        return $response;
    }
}
