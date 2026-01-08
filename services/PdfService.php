<?php
declare(strict_types=1);

namespace services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    public function stream(string $html, string $filename = 'recu.pdf'): void
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true); 

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream($filename, ['Attachment' => true]);
    }
}
