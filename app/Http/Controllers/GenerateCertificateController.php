<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\PdfParserException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateCertificateController extends Controller
{
    public function generate()
    {
        $me = Auth::user();

        $pdf = new FPDI;
        try {
            $pdf->setSourceFile(Storage::path('Template_Certificate.pdf'));
        } catch (PdfParserException $e) {
            error_log($e);
            dd($e->getMessage());
        }
        $uuid = Str::random(15);
        $qrcodePath = 'public/certificate_sign/' . $uuid . '.png';

        Storage::put($qrcodePath,
            QrCode::size(200)
                ->format('png')
                ->style('round')
                ->eye('circle')
                ->margin(1)
                ->errorCorrection('M')
                ->generate($uuid));

        $template = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($template);

        $pdf->AddPage($size['orientation'], array($size['width'], $size['height']));
        $pdf->useTemplate($template);
        $pdf->SetFont("Arial", "B", 30);
        $pdf->SetTextColor(0, 0, 0);

        $text_width = $pdf->GetStringWidth($me->name);
        $pdf->Text(($size['width'] - $text_width) / 2, $size['height'] / 2, $me->name);
        $pdf->Image(Storage::path($qrcodePath), ($size['width'] / 2) - 12.5, ($size['height'] / 2) + 35, 25, 25);

        $result_dir = '/public/certificate/';
        $filename = $uuid . '.pdf';
        $pdf->Output(Storage::path($result_dir . $filename), 'F');

        return Inertia::render('Certificate', [
            'src' => Storage::url('certificate/' . $filename)
        ]);
    }
}
