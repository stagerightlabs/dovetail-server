<?php

namespace Tests\Feature\Authentication;

use App\User;
use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use App\Http\Middleware\AddOrganizationToRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlockedUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_blocked_users_will_be_denied_access()
    {
        $user = $this->actingAs(factory(User::class)->create());

        $user->blocked_at = Carbon::now()->subDays(2);
        $user->save();

        $response = $this->getJson(route('user.show'));

        $response->assertStatus(401);
        $response->assertJsonFragment([]);
    }
}
