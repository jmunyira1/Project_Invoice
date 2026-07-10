<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesProjectCards;
use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectFileController extends Controller
{
    use HandlesProjectCards;

    /**
     * Private disk that backs project file storage.
     */
    private const DISK = 'local';

    public function store(Request $request, Project $project)
    {
        $this->authoriseProject($project);

        $validated = $request->validate([
            'file' => [
                'required', 'file', 'max:20480', // 20 MB
                'mimes:pdf,doc,docx,xls,xlsx,csv,ppt,pptx,txt,png,jpg,jpeg,gif,webp,zip',
            ],
            'category' => ['required', 'in:contract,brief,reference,signed,other'],
        ]);

        $file = $validated['file'];
        $path = $file->store("project-files/{$project->id}", self::DISK);

        ProjectFile::create([
            'organisation_id' => $this->org()->id,
            'project_id' => $project->id,
            'uploaded_by' => auth()->id(),
            'category' => $validated['category'],
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        return $this->projectBodyResponse($request, $project, 'File uploaded.');
    }

    public function download(Project $project, ProjectFile $file): StreamedResponse
    {
        $this->authoriseProject($project);
        abort_if($file->project_id !== $project->id, 403);

        abort_unless(Storage::disk(self::DISK)->exists($file->path), 404);

        return Storage::disk(self::DISK)->download($file->path, $file->original_name);
    }

    public function destroy(Request $request, Project $project, ProjectFile $file)
    {
        $this->authoriseProject($project);
        abort_if($file->project_id !== $project->id, 403);

        Storage::disk(self::DISK)->delete($file->path);
        $file->delete();

        return $this->projectBodyResponse($request, $project, 'File deleted.');
    }
}
