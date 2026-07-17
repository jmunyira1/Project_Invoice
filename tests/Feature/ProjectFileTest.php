<?php

namespace Tests\Feature;

use App\Models\ProjectFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectFileTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_upload_a_file_to_the_private_disk(): void
    {
        Storage::fake('local');
        $org = $this->makeOrganisation();
        $owner = $this->makeOwner($org);
        $project = $this->makeProject($org);

        $this->actingAs($owner)->post("/projects/{$project->id}/files", [
            'file' => UploadedFile::fake()->create('contract.pdf', 120, 'application/pdf'),
            'category' => 'contract',
        ])->assertRedirect();

        $file = ProjectFile::first();
        $this->assertNotNull($file);
        $this->assertSame('contract.pdf', $file->original_name);
        $this->assertSame('contract', $file->category);
        Storage::disk('local')->assertExists($file->path);
    }

    public function test_file_download_is_scoped_to_the_owning_organisation(): void
    {
        Storage::fake('local');
        $org = $this->makeOrganisation();
        $owner = $this->makeOwner($org);
        $project = $this->makeProject($org);

        $this->actingAs($owner)->post("/projects/{$project->id}/files", [
            'file' => UploadedFile::fake()->create('brief.pdf', 50, 'application/pdf'),
            'category' => 'brief',
        ]);
        $file = ProjectFile::first();

        // Owner can download
        $this->actingAs($owner)
            ->get("/projects/{$project->id}/files/{$file->id}/download")
            ->assertOk();

        // A user from a different organisation cannot
        $otherOwner = $this->makeOwner();
        $this->actingAs($otherOwner)
            ->get("/projects/{$project->id}/files/{$file->id}/download")
            ->assertForbidden();
    }

    public function test_owner_can_delete_a_file(): void
    {
        Storage::fake('local');
        $org = $this->makeOrganisation();
        $owner = $this->makeOwner($org);
        $project = $this->makeProject($org);

        $this->actingAs($owner)->post("/projects/{$project->id}/files", [
            'file' => UploadedFile::fake()->create('old.pdf', 10, 'application/pdf'),
            'category' => 'other',
        ]);
        $file = ProjectFile::first();

        $this->actingAs($owner)
            ->delete("/projects/{$project->id}/files/{$file->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('project_files', ['id' => $file->id]);
        Storage::disk('local')->assertMissing($file->path);
    }
}
