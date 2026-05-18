<?php

declare(strict_types=1);

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

final class ReportDownloadService
{
    public function resolveFormat(Request $request): string
    {
        $format = strtolower(trim((string) $request->query('format', 'xlsx')));

        return match ($format) {
            'xlsx', 'excel' => 'xlsx',
            'pdf' => 'pdf',
            default => abort(422, 'Formato no válido. Use Excel (xlsx) o PDF.'),
        };
    }

    /**
     * @param  list<string>  $headers
     * @param  list<list<string|int|float|null>>  $rows
     */
    public function download(
        string $title,
        array $headers,
        array $rows,
        string $basename,
        string $format,
    ): BinaryFileResponse|Response {
        $safeBase = preg_replace('/[^\w\-]+/u', '_', $basename) ?: 'reporte';
        $timestamp = date('Y-m-d_His');

        return match ($format) {
            'xlsx' => $this->downloadXlsx($headers, $rows, "{$safeBase}_{$timestamp}.xlsx"),
            'pdf' => $this->downloadPdf($title, $headers, $rows, "{$safeBase}_{$timestamp}.pdf"),
            default => abort(422, 'Formato no soportado.'),
        };
    }

    /**
     * @param  list<string>  $headers
     * @param  list<list<string|int|float|null>>  $rows
     */
    private function downloadXlsx(array $headers, array $rows, string $filename): BinaryFileResponse
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'xlsx_');
        if ($tempPath === false) {
            abort(500, 'No se pudo generar el archivo Excel.');
        }

        $path = $tempPath.'.xlsx';
        rename($tempPath, $path);

        $writer = new Writer;
        $writer->openToFile($path);
        $writer->addRow(Row::fromValues($headers));

        foreach ($rows as $row) {
            $writer->addRow(Row::fromValues(array_map(
                fn ($cell) => $cell === null ? '' : (string) $cell,
                $row,
            )));
        }

        $writer->close();

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * @param  list<string>  $headers
     * @param  list<list<string|int|float|null>>  $rows
     */
    private function downloadPdf(string $title, array $headers, array $rows, string $filename): Response
    {
        $pdf = Pdf::loadView('exports.tabla-reporte', [
            'titulo' => $title,
            'generado' => now()->format('d/m/Y H:i'),
            'headers' => $headers,
            'rows' => $rows,
        ])->setPaper('a4', count($headers) > 6 ? 'landscape' : 'portrait');

        return $pdf->download($filename);
    }
}
