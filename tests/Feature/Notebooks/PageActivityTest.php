<?php

namespace Tests\Feature\Notebooks;

use App\Page;
use App\User;
use App\Notebook;
use Tests\TestCase;
use App\Organization;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PageActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_notebook_page_activity_can_be_retrieved()
    {
        $this->withoutExceptionHandling();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create([
            'organization_id' => $organization->id
        ]);
        $user->applyPermissions(['notebooks.pages' => true]);
        $user->save();
        $this->actingAs($user);
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
        ]);

        $response = $this->postJson(route('pages.store', $notebook->hashid), [
            'content' => 'Lorem Ipsum Text'
        ]);
        $pageId = $response->decodeResponseJson('data')['hashid'];
        $activity = Activity::where('subject_id', hashid($pageId))
            ->where('subject_type', 'page')
            ->first();

        $response = $this->get(route('pages.activity.show', [$notebook->hashid, $pageId]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            [
                'hashid' => hashid($activity->id),
                'user_id' => $user->hashid,
                'user_name' => $user->name,
                'description' => 'Created',
                'created_at' => $activity->created_at->toAtomString(),
                'since_created' => '1 second ago',
            ]
        ]);
    }
}
