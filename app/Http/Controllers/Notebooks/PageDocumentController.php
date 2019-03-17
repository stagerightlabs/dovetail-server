<?php

namespace App\Http\Controllers\Notebooks;

use App\Logo;
use App\Page;
use App\Document;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Http\Requests\PageDocumentCreation;

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
     * @param  PageDocumentCreation $request
     * @param  string $notebook
     * @param  string $page
     * @return JsonResponse
     */
    public function store(PageDocumentCreation $request, $notebook, $page)
    {
        // Fetch the page
        $page = Page::with('notebook')->findOrFail(hashid($page));

        // Storage Path
        $path = $request->organization()->slug . '/documents';

        // Retrieve the file from the request
        $attachment = $request->file('attachment');

        // Create Document
        $document = Document::create([
            'documentable_id' => $page->id,
            'documentable_type' => 'page',
            'original' => $attachment->store($path, 's3'),
            'filename' => $attachment->getClientOriginalName(),
            'mimetype' => $attachment->getClientMimeType(),
            'created_by' => $request->user()->id,
        ]);

        // Log the document creation
        activity()
            ->on($page)
            ->log("New document:  " . $attachment->getClientOriginalName());

        // All set
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
    public function destroy($notebook, $page, $document)
    {
        // Fetch the page
        $page = Page::with('notebook')->findOrFail(hashid($page));

        // Fetch the document
        $document = $page->documents()->findOrFail(hashid($document));

        // Log the document creation
        activity()->on($page)->log("Removed document:  " . $document->filename);

        // Delete the document from the database and S3
        $document->delete();

        // All set
        return response()->json([], 204);
    }
}
