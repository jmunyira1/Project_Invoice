<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesProjectCards;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use HandlesProjectCards;

    public function index()
    {
        $projects = $this->org()
            ->projects()
            ->with('client')
            ->withCount('deliverables')
            ->latest()
            ->paginate(20);

        $statusCounts = $this->org()
            ->projects()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('projects.index', compact('projects', 'statusCounts'));
    }

    private function org()
    {
        return auth()->user()->organisation;
    }

    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();
        $data['organisation_id'] = $this->org()->id;
        $data['status'] = $data['status'] ?? 'draft';

        // Ensure client belongs to this org
        $this->authoriseClient($data['client_id']);

        $project = Project::create($data);

        if ($request->header('HX-Request')) {
            return response()->noContent()
                ->header('HX-Redirect', route('projects.show', $project));
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project created.');
    }

    private function authoriseClient(int $clientId): void
    {
        $exists = $this->org()->clients()->where('id', $clientId)->exists();
        if (!$exists) {
            abort(403, 'Client does not belong to your organisation.');
        }
    }

    public function create()
    {
        $clients = $this->org()->clients()->orderBy('name')->get();
        $selectedClientId = request('client_id');

        if (request()->header('HX-Request')) {
            return view('projects.partials.form_modal', [
                'project' => null,
                'clients' => $clients,
                'selectedClientId' => $selectedClientId,
            ]);
        }

        return view('projects.create', compact('clients', 'selectedClientId'));
    }

    public function show(Project $project)
    {
        $this->authorise($project);

        $project->load([
            'client',
            'deliverables',
            'costs' => fn($q) => $q->orderBy('incurred_on', 'desc'),
            'documents' => fn($q) => $q->latest(),
            'payments' => fn($q) => $q->latest(),
            'installments',
            'files',
        ]);

        $currency = $this->org()->currency;

        return view('projects.show', compact('project', 'currency'));
    }

    private function authorise(Project $project): void
    {
        if ($project->organisation_id !== $this->org()->id) {
            abort(403);
        }
    }

    public function edit(Project $project)
    {
        $this->authorise($project);

        $clients = $this->org()->clients()->orderBy('name')->get();

        if (request()->header('HX-Request')) {
            return view('projects.partials.form_modal', [
                'project' => $project,
                'clients' => $clients,
                'selectedClientId' => null,
            ]);
        }

        return view('projects.edit', compact('project', 'clients'));
    }

    public function destroy(Project $project)
    {
        $this->authorise($project);

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project deleted.');
    }

    // ── Private ────────────────────────────────────────────────────

    /**
     * PATCH /projects/{project}/status
     * Moves project through the workflow.
     */
    public function updateStatus(Request $request, Project $project)
    {
        $this->authorise($project);

        $newStatus = $request->validate([
            'status' => ['required', 'string'],
        ])['status'];

        if (!in_array($newStatus, $project->allowed_transitions)) {
            return $this->projectBodyResponse(
                $request,
                $project,
                "Cannot move project from [$project->status] to [$newStatus].",
                'error'
            );
        }

        $project->update(['status' => $newStatus]);

        return $this->projectBodyResponse($request, $project, 'Project status updated.');
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorise($project);
        $this->authoriseClient($request->client_id);

        $project->update($request->validated());

        if ($request->header('HX-Request')) {
            return response()->noContent()
                ->header('HX-Redirect', route('projects.show', $project));
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project updated.');
    }
}
