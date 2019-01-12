<?php

namespace App\Http\Controllers\Notebooks;

use App\Logo;
use App\Page;
use App\Document;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;

class PageDocumentController extends Controller
{
    /**
     * Fetch a list of available documents
     *
     * @return JsonResponse
     */
    public function index($notebook, $page)
    {
        $page = Page::with('notebook')->findOrFail(hashid($page));

        return DocumentResource::collection($page->documents);
    }

    /**
     * Store a new document
     *
     * @return JsonResponse
     */
    public function store($notebook, $page)
    {
        request()->validate([
            'attachment' => 'required|mimes:jpeg,png,gif,pdf'
        ]);

        // Check user permissions
        $this->requirePermission('notebooks.pages');

        // Fetch the page
        $page = Page::with('notebook')->findOrFail(hashid($page));

        // Storage Path
        $path = request()->organization()->slug . '/documents';

        // Create Document
        $document = Document::create([
            'documentable_id' => $page->id,
            'documentable_type' => 'page',
            'original' => request('attachment')->store($path, 's3'),
            'filename' => request('attachment')->getClientOriginalName(),
            'mimetype' => request('attachment')->getClientMimeType(),
        ]);

        return new DocumentResource($document);
    }

    /**
     * Return a single document
     *
     * @param string $notebook
     * @param string $page
     * @param string $document
     * @return JsonResponse
     */
    public function show($notebook, $page, $document)
    {
        $page = Page::with('notebook')->findOrFail(hashid($page));

        return new DocumentResource(
            $page->documents()->findOrFail(hashid($document))
        );
    }

    /**
     * Remove a document from storage.
     *
     * @param string $notebook
     * @param string $page
     * @param string $document
     * @return JsonResponse
     */
    public function delete($notebook, $page, $document)
    {
        $page = Page::with('notebook')->findOrFail(hashid($page));
        $document = $page->documents()->findOrFail(hashid($document));

        $document->delete();

        return response()->json([], 204);
    }
}
