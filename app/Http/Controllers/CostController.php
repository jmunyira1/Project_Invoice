<?php

namespace App\Http\Controllers;

use App\Models\Cost;
use App\Models\Project;

class CostController extends Controller
{
    public function store(Project $project)
    {
        $this->authorise($project);

        $data = request()->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'incurred_on' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['project_id'] = $project->id;
        Cost::create($data);

        return back()->with('success', 'Cost recorded.');
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

    public function update(Project $project, Cost $cost)
    {
        $this->authorise($project);
        abort_if($cost->project_id !== $project->id, 403);

        $data = request()->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'incurred_on' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $cost->update($data);

        return back()->with('success', 'Cost updated.');
    }

    public function destroy(Project $project, Cost $cost)
    {
        $this->authorise($project);
        abort_if($cost->project_id !== $project->id, 403);

        $cost->delete();

        return back()->with('success', 'Cost deleted.');
    }
}
