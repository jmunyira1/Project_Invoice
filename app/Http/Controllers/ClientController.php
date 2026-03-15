<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $clients = $this->org()
            ->clients()
            ->withCount('projects')
            ->orderBy('name')
            ->paginate(20);

        return view('clients.index', compact('clients'));
    }

    private function org()
    {
        return auth()->user()->organisation;
    }

    public function store(StoreClientRequest $request)
    {
        $this->org()->clients()->create($request->validated());

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client created successfully.');
    }

    public function create()
    {
        return view('clients.create');
    }

    public function show(Client $client)
    {
        $this->authorise($client);

        $client->load([
            'projects' => fn($q) => $q->withCount('deliverables')->latest(),
        ]);

        $currency = $this->org()->currency;

        return view('clients.show', compact('client', 'currency'));
    }

    /**
     * Ensure the client belongs to the current organisation.
     */
    private function authorise(Client $client): void
    {
        if ($client->organisation_id !== $this->org()->id) {
            abort(403);
        }
    }

    public function edit(Client $client)
    {
        $this->authorise($client);

        return view('clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $this->authorise($client);

        $client->update($request->validated());

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Client updated successfully.');
    }

    // ── Private ────────────────────────────────────────────────────

    public function destroy(Client $client)
    {
        $this->authorise($client);

        // Prevent deletion if client has projects
        if ($client->projects()->exists()) {
            return back()->with('error', 'Cannot delete a client that has projects. Archive the projects first.');
        }

        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client deleted.');
    }
}
