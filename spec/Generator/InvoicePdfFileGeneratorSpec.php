<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Generator;

use Knp\Snappy\GeneratorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGenerator;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

final class InvoicePdfFileGeneratorSpec extends ObjectBehavior
{
    function let(
        InvoiceRepository $invoiceRepository,
        EngineInterface $twig,
        GeneratorInterface $pdfGenerator
    ): void {
        $this->beConstructedWith(
            $invoiceRepository,
            $twig,
            $pdfGenerator,
            'invoiceTemplate.html.twig'
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(InvoicePdfFileGenerator::class);
    }

    function it_implements_invoice_pdf_file_generator_interface(): void
    {
        $this->shouldImplement(InvoicePdfFileGeneratorInterface::class);
    }

    function it_creates_credit_memo_pdf_with_generated_content_and_filename_basing_on_credit_memo_number(
        InvoiceRepository $invoiceRepository,
        EngineInterface $twig,
        GeneratorInterface $pdfGenerator,
        InvoiceInterface $invoice
    ): void {
        $invoiceRepository->get(1)->willReturn($invoice);
        $invoice->number()->willReturn('2015/05/00004444');

        $twig
            ->render('invoiceTemplate.html.twig', ['invoice' => $invoice])
            ->willReturn('<html>I am an invoice pdf file content</html>')
        ;

        $pdfGenerator->getOutputFromHtml('<html>I am an invoice pdf file content</html>')->willReturn('PDF FILE');

        $this->generate(1)->shouldBeLike(new InvoicePdf('2015_05_00004444.pdf', 'PDF FILE'));
    }
}
