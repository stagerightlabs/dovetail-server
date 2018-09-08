<?php

namespace Tests\Feature\Invitations;

use App\Invitation;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_existent_invitations_are_invalid()
    {
        $response = $this->getJson(route('invitations.confirm', 'FAKECODE'));

        $response->assertStatus(404);
    }

    public function test_revoked_invitations_are_invalid()
    {
        $invitation = factory(Invitation::class)->states('revoked')->create();

        $response = $this->getJson(route('invitations.confirm', $invitation->code));

        $response->assertStatus(404);
    }

    public function test_completed_invitations_are_invalid()
    {
        $invitation = factory(Invitation::class)->states('completed')->create();

        $response = $this->getJson(route('invitations.confirm', $invitation->code));

        $response->assertStatus(404);
    }

    public function test_it_confirms_invitations()
    {
        $invitation = factory(Invitation::class)->create();

        $response = $this->getJson(route('invitations.confirm', $invitation->code));

        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'email' => $invitation->email,
                'code' => $invitation->code
            ]
        ]);
    }
}
