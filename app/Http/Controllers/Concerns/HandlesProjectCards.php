<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Project;
use Illuminate\Http\Request;

/**
 * Shared behaviour for the controllers that mutate a project's cards
 * (deliverables, costs, installments, payments, files, status).
 *
 * On an HTMX request the whole "#project-body" fragment is re-rendered and
 * swapped in place (simple + always consistent); otherwise we fall back to a
 * normal redirect-back with a flash message (progressive enhancement).
 */
trait HandlesProjectCards
{
    protected function org()
    {
        return auth()->user()->organisation;
    }

    protected function authoriseProject(Project $project): void
    {
        if ($project->organisation_id !== $this->org()->id) {
            abort(403);
        }
    }

    protected function projectBodyResponse(Request $request, Project $project, string $message, string $level = 'success')
    {
        if ($request->header('HX-Request')) {
            $project->load([
                'client',
                'deliverables',
                'costs' => fn ($q) => $q->orderBy('incurred_on', 'desc'),
                'documents' => fn ($q) => $q->latest(),
                'payments' => fn ($q) => $q->latest(),
                'installments',
                'files',
            ]);

            $currency = $this->org()->currency;

            return response()
                ->view('projects.partials._body', compact('project', 'currency'))
                ->header('HX-Trigger', json_encode([
                    'flash' => ['level' => $level, 'message' => $message],
                ]));
        }

        return back()->with($level === 'success' ? 'success' : 'error', $message);
    }
}
