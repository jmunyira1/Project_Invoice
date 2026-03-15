<?php

namespace App\Http\Controllers;

use App\Models\Deliverable;
use App\Models\Project;

class DeliverableController extends Controller
{
    public function store(Project $project)
    {
        $this->authorise($project);

        $data = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $data['project_id'] = $project->id;
        Deliverable::create($data);

        return back()->with('success', 'Deliverable added.');
    }

    private function authorise(Project $project): void
    {
        if ($project->organisation_id !== $this->org()->id) {
            abort(403);
        }
    }

    private function org()
    {
        return auth()->user()->organisation;
    }

    public function update(Project $project, Deliverable $deliverable)
    {
        $this->authorise($project);
        abort_if($deliverable->project_id !== $project->id, 403);

        $data = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $deliverable->update($data);

        return back()->with('success', 'Deliverable updated.');
    }

    public function destroy(Project $project, Deliverable $deliverable)
    {
        $this->authorise($project);
        abort_if($deliverable->project_id !== $project->id, 403);

        $deliverable->delete();

        return back()->with('success', 'Deliverable removed.');
    }
}
