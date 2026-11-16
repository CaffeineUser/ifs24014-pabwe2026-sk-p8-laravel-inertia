<?php

namespace Tests\Feature\Post\Services;

use App\Features\Post\Models\Post;
use App\Features\Post\Services\PostExcelService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\TestCase;

class PostExcelServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_generates_an_xlsx_file_for_published_and_draft_posts(): void
    {
        $user = User::factory()->create();
        $published = Post::factory()->for($user)->create(['is_published' => true]);
        $draft = Post::factory()->for($user)->create(['is_published' => false]);
        $response = (new PostExcelService)->export(collect([$published, $draft]));

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringStartsWith('PK', $content);
        $this->assertSame(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type')
        );
    }

    public function test_import_reads_article_rows_and_uses_defaults_for_optional_columns(): void
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['ID', 'Judul', 'Kategori', 'Konten'],
            ['', 'First Article', 'Pendidikan', 'First body'],
            ['', 'Second Article', null, null],
            ['', null, 'Ignored category', 'Ignored body'],
        ]);
        $path = tempnam(sys_get_temp_dir(), 'post-import-');
        (new Xlsx($spreadsheet))->save($path);
        $file = new UploadedFile($path, 'articles.xlsx', null, null, true);

        try {
            $imported = (new PostExcelService)->import($file);
        } finally {
            unlink($path);
        }

        $this->assertSame([
            [
                'title' => 'First Article',
                'category' => 'Pendidikan',
                'content' => 'First body',
                'is_published' => false,
            ],
            [
                'title' => 'Second Article',
                'category' => 'Umum',
                'content' => 'Konten default dari impor Excel.',
                'is_published' => false,
            ],
        ], $imported);
    }
}
