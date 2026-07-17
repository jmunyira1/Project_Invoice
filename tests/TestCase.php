<?php

namespace Tests;

use App\Models\Client;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Create an organisation with a default template and return it.
     */
    protected function makeOrganisation(array $attrs = []): Organisation
    {
        $template = Template::firstOrCreate(
            ['slug' => 'template-001'],
            ['name' => 'Classic', 'description' => 'Test template']
        );

        return Organisation::factory()->create(array_merge(
            ['default_template_id' => $template->id],
            $attrs
        ));
    }

    /**
     * Create an owner user for the given (or a fresh) organisation.
     */
    protected function makeOwner(?Organisation $org = null): User
    {
        $org ??= $this->makeOrganisation();

        return User::factory()->create([
            'organisation_id' => $org->id,
            'role' => 'owner',
        ]);
    }

    protected function makeClient(Organisation $org, array $attrs = []): Client
    {
        return Client::factory()->create(array_merge(['organisation_id' => $org->id], $attrs));
    }

    protected function makeProject(Organisation $org, ?Client $client = null, array $attrs = []): Project
    {
        $client ??= $this->makeClient($org);

        return Project::factory()->create(array_merge([
            'organisation_id' => $org->id,
            'client_id' => $client->id,
        ], $attrs));
    }
}
