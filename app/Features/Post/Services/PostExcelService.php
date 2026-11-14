<?php

namespace App\Features\Post\Services;

use Illuminate\Database\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PostExcelService
{
    public function export(Collection $posts): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Judul');
        $sheet->setCellValue('C1', 'Kategori');
        $sheet->setCellValue('D1', 'Status Publikasi');
        $sheet->setCellValue('E1', 'Tanggal Dibuat');

        $row = 2;
        foreach ($posts as $post) {
            $sheet->setCellValue("A{$row}", $post->id);
            $sheet->setCellValue("B{$row}", $post->title);
            $sheet->setCellValue("C{$row}", $post->category);
            $sheet->setCellValue("D{$row}", $post->is_published ? 'Published' : 'Draft');
            $sheet->setCellValue("E{$row}", $post->created_at);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan-artikel-'.date('Y-m-d').'.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import($file): array
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $imported = [];
        // Lewati baris pertama (header)
        unset($rows[1]);

        foreach ($rows as $row) {
            if (! empty($row['B'])) { // Jika Judul ada
                $imported[] = [
                    'title' => $row['B'],
                    'category' => $row['C'] ?? 'Umum',
                    'content' => $row['D'] ?? 'Konten default dari impor Excel.',
                    'is_published' => false,
                ];
            }
        }

        return $imported;
    }
}
