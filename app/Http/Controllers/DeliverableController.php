<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesProjectCards;
use App\Models\Deliverable;
use App\Models\Project;
use Illuminate\Http\Request;

class DeliverableController extends Controller
{
    use HandlesProjectCards;

    public function store(Request $request, Project $project)
    {
        $this->authoriseProject($project);

        $data = $this->validateData($request);
        $data['project_id'] = $project->id;
        Deliverable::create($data);

        return $this->projectBodyResponse($request, $project, 'Deliverable added.');
    }

    public function update(Request $request, Project $project, Deliverable $deliverable)
    {
        $this->authoriseProject($project);
        abort_if($deliverable->project_id !== $project->id, 403);

        $deliverable->update($this->validateData($request));

        return $this->projectBodyResponse($request, $project, 'Deliverable updated.');
    }

    public function destroy(Request $request, Project $project, Deliverable $deliverable)
    {
        $this->authoriseProject($project);
        abort_if($deliverable->project_id !== $project->id, 403);

        $deliverable->delete();

        return $this->projectBodyResponse($request, $project, 'Deliverable removed.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);
    }
}
