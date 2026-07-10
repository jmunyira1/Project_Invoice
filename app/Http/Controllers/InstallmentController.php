<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesProjectCards;
use App\Models\Installment;
use App\Models\Project;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    use HandlesProjectCards;

    public function store(Request $request, Project $project)
    {
        $this->authoriseProject($project);

        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'due_date' => ['nullable', 'date'],
            'document_id' => ['nullable', 'integer', 'exists:documents,id'],
        ]);

        $data['organisation_id'] = $this->org()->id;
        $data['project_id'] = $project->id;
        $data['sort_order'] = ($project->installments()->max('sort_order') ?? 0) + 1;

        $installment = Installment::create($data);
        $installment->syncStatus();

        return $this->projectBodyResponse($request, $project, "Installment “{$installment->label}” added.");
    }

    public function update(Request $request, Project $project, Installment $installment)
    {
        $this->authoriseProject($project);
        abort_if($installment->project_id !== $project->id, 403);

        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'due_date' => ['nullable', 'date'],
            'document_id' => ['nullable', 'integer', 'exists:documents,id'],
        ]);

        $installment->update($data);
        $installment->syncStatus();

        return $this->projectBodyResponse($request, $project, 'Installment updated.');
    }

    public function destroy(Request $request, Project $project, Installment $installment)
    {
        $this->authoriseProject($project);
        abort_if($installment->project_id !== $project->id, 403);

        // Detach any payments so they are kept but no longer tied to a plan row.
        $installment->payments()->update(['installment_id' => null]);
        $installment->delete();

        return $this->projectBodyResponse($request, $project, 'Installment removed.');
    }
}
