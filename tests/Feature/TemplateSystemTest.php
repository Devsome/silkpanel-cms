<?php

namespace Tests\Feature;

use App\Models\Template;
use App\Services\TemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class TemplateSystemTest extends TestCase
{
    use RefreshDatabase;

    protected TemplateService $templateService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->templateService = app(TemplateService::class);
        
        // Ensure templates directory exists
        $templatesPath = storage_path('app/templates');
        if (!File::isDirectory($templatesPath)) {
            File::makeDirectory($templatesPath, 0755, true);
        }
    }

    protected function tearDown(): void
    {
        // Clean up any test templates
        $templatesPath = storage_path('app/templates');
        if (File::isDirectory($templatesPath)) {
            File::cleanDirectory($templatesPath);
        }
        
        parent::tearDown();
    }

    /** @test */
    public function it_can_list_templates()
    {
        $response = $this->get(route('templates.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('templates.index');
    }

    /** @test */
    public function it_can_show_upload_form()
    {
        $response = $this->get(route('templates.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('templates.create');
    }

    /** @test */
    public function it_can_upload_and_extract_template_zip()
    {
        // Create a test ZIP file
        $zipPath = $this->createTestZip();
        $file = new UploadedFile($zipPath, 'test-template.zip', 'application/zip', null, true);

        $response = $this->post(route('templates.store'), [
            'name' => 'test-template',
            'template_file' => $file,
        ]);

        $response->assertRedirect(route('templates.index'));
        $response->assertSessionHas('success');

        // Check database
        $this->assertDatabaseHas('templates', [
            'name' => 'test-template',
            'path' => 'test-template',
        ]);

        // Check files were extracted
        $templatePath = storage_path('app/templates/test-template');
        $this->assertTrue(File::isDirectory($templatePath));
        $this->assertTrue(File::exists($templatePath . '/views/welcome.blade.php'));
    }

    /** @test */
    public function it_validates_template_name()
    {
        $zipPath = $this->createTestZip();
        $file = new UploadedFile($zipPath, 'test-template.zip', 'application/zip', null, true);

        $response = $this->post(route('templates.store'), [
            'name' => 'invalid name with spaces',
            'template_file' => $file,
        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function it_requires_zip_file()
    {
        $response = $this->post(route('templates.store'), [
            'name' => 'test-template',
            'template_file' => UploadedFile::fake()->create('notazip.txt'),
        ]);

        $response->assertSessionHasErrors('template_file');
    }

    /** @test */
    public function it_can_activate_template()
    {
        $template = Template::create([
            'name' => 'test-template',
            'path' => 'test-template',
            'is_active' => false,
        ]);

        $response = $this->post(route('templates.activate', $template));

        $response->assertRedirect(route('templates.index'));
        $response->assertSessionHas('success');

        $this->assertTrue($template->fresh()->is_active);
    }

    /** @test */
    public function it_deactivates_other_templates_when_activating()
    {
        $template1 = Template::create([
            'name' => 'template-1',
            'path' => 'template-1',
            'is_active' => true,
        ]);

        $template2 = Template::create([
            'name' => 'template-2',
            'path' => 'template-2',
            'is_active' => false,
        ]);

        $this->post(route('templates.activate', $template2));

        $this->assertFalse($template1->fresh()->is_active);
        $this->assertTrue($template2->fresh()->is_active);
    }

    /** @test */
    public function it_can_deactivate_all_templates()
    {
        $template = Template::create([
            'name' => 'test-template',
            'path' => 'test-template',
            'is_active' => true,
        ]);

        $response = $this->post(route('templates.deactivate'));

        $response->assertRedirect(route('templates.index'));
        $this->assertFalse($template->fresh()->is_active);
    }

    /** @test */
    public function it_can_delete_template()
    {
        // Create template directory
        $templatePath = storage_path('app/templates/test-template');
        File::makeDirectory($templatePath, 0755, true);
        File::put($templatePath . '/test.txt', 'test');

        $template = Template::create([
            'name' => 'test-template',
            'path' => 'test-template',
            'is_active' => false,
        ]);

        $response = $this->delete(route('templates.destroy', $template));

        $response->assertRedirect(route('templates.index'));
        $this->assertDatabaseMissing('templates', ['id' => $template->id]);
        $this->assertFalse(File::isDirectory($templatePath));
    }

    /**
     * Create a test ZIP file with a simple template structure.
     */
    protected function createTestZip(): string
    {
        $tempDir = sys_get_temp_dir() . '/test-template-' . uniqid();
        $viewsDir = $tempDir . '/views';
        
        File::makeDirectory($viewsDir, 0755, true);
        File::put($viewsDir . '/welcome.blade.php', '<h1>Test Template</h1>');

        $zipPath = sys_get_temp_dir() . '/test-template-' . uniqid() . '.zip';
        $zip = new ZipArchive();
        
        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            $files = File::allFiles($tempDir);
            foreach ($files as $file) {
                $relativePath = str_replace($tempDir . '/', '', $file->getPathname());
                $zip->addFile($file->getPathname(), $relativePath);
            }
            $zip->close();
        }

        File::deleteDirectory($tempDir);

        return $zipPath;
    }
}
