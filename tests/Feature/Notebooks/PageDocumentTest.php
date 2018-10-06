<?php

namespace Tests\Feature\Notebooks;

use App\Page;
use App\User;
use App\Document;
use App\Notebook;
use Tests\TestCase;
use App\Organization;
use App\Events\DocumentCreated;
use App\Events\DocumentDeletion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PageDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_all_page_documents()
    {
        Storage::fake('s3');
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
        ]);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);
        $document = factory(Document::class)->create([
            'documentable_id' => $page->id
        ]);

        $response = $this->getJson(route('pages.documents.index', [$notebook->hashid, $page->hashid]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'hashid' => $document->hashid,
            'original' => Storage::disk('s3')->url($document->original),
            'large' => Storage::disk('s3')->url($document->large),
            'small' => Storage::disk('s3')->url($document->small)
        ]);
    }

    public function test_it_attaches_a_document_to_a_page()
    {
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
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);
        $file = UploadedFile::fake()->image('document.png');
        Storage::fake('s3');

        $response = $this->postJson(route('pages.documents.store', [$notebook->hashid, $page->hashid]), [
            'attachment' => $file
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'hashid',
                'original',
                'large',
                'small',
                'mimetype'
            ]
        ]);
        $document = Document::first();
        $this->assertEquals('image/png', $document->mimetype);
        Storage::disk('s3')->assertExists($document->original);
    }

    public function test_documents_can_only_be_created_from_images_and_pdfs()
    {
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
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);
        $file = UploadedFile::fake()->image('document.pmg');
        Storage::fake('s3');
        // Event::fake();

        $response = $this->postJson(route('pages.documents.store', [$notebook->hashid, $page->hashid]), [
            'attachment' => $file
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('attachment');
        $this->assertEquals(0, Document::count());
    }

    public function test_it_returns_a_single_page_document()
    {
        Storage::fake('s3');
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
        ]);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);
        $document = factory(Document::class)->create([
            'documentable_id' => $page->id
        ]);

        $response = $this->getJson(route('pages.documents.show', [$notebook->hashid, $page->hashid, $document->hashid]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'hashid' => $document->hashid,
            'original' => Storage::disk('s3')->url($document->original),
            'large' => Storage::disk('s3')->url($document->large),
            'small' => Storage::disk('s3')->url($document->small)
        ]);
    }

    public function test_it_does_not_return_documents_that_do_not_exist()
    {
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
        ]);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);

        $response = $this->getJson(route('pages.documents.show', [$notebook->hashid, $page->hashid, 'NOTREAL']));

        $response->assertStatus(404);
    }

    public function test_it_deletes_a_document()
    {
        Storage::fake('s3');
        $organization = factory(Organization::class)->create();
        $this->actingAs(factory(User::class)->create([
            'organization_id' => $organization->id
        ]));
        $notebook = factory(Notebook::class)->create([
            'organization_id' => $organization->id,
        ]);
        $page = factory(Page::class)->create([
            'notebook_id' => $notebook->id,
        ]);
        $document = factory(Document::class)->create([
            'documentable_id' => $page->id
        ]);
        Event::fake();

        $response = $this->deleteJson(route('pages.documents.delete', [$notebook->hashid, $page->hashid, $document->hashid]));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('documents', [
            'id' => $document->id
        ]);
        Event::assertDispatched(DocumentDeletion::class);
    }
}
