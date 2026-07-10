<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesProjectCards;
use App\Models\Cost;
use App\Models\Project;
use Illuminate\Http\Request;

class CostController extends Controller
{
    use HandlesProjectCards;

    public function store(Request $request, Project $project)
    {
        $this->authoriseProject($project);

        $data = $this->validateData($request);
        $data['project_id'] = $project->id;
        Cost::create($data);

        return $this->projectBodyResponse($request, $project, 'Cost recorded.');
    }

    public function update(Request $request, Project $project, Cost $cost)
    {
        $this->authoriseProject($project);
        abort_if($cost->project_id !== $project->id, 403);

        $cost->update($this->validateData($request));

        return $this->projectBodyResponse($request, $project, 'Cost updated.');
    }

    public function destroy(Request $request, Project $project, Cost $cost)
    {
        $this->authoriseProject($project);
        abort_if($cost->project_id !== $project->id, 403);

        $cost->delete();

        return $this->projectBodyResponse($request, $project, 'Cost deleted.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'incurred_on' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
