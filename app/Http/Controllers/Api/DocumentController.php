<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Resources\DocumentResource;
use Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'document' => 'required|mimes:pdf,png,jpg|max:9999',
        ]);

        $base_location = 'user_documents';

       
        if($request->hasFile('document')) {              
            
            $documentPath = $request->file('document')->store($base_location, 's3');
          
        } else {
            return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
        }
    
        //We save new path
        $document = new Document();
        $document->path = $documentPath;
        $document->name = $request->name;
        $document->save();
       
        return response()->json(['success' => true, 'message' => 'Document successfully uploaded', 'document' => new DocumentResource($document)], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $document = Document::find($id);

        if(empty($document)){
            return response()->json(['success' => false, 'message' => 'Document not found'], 404);
        }

        //We remove existing document
        if(!empty($document))
        {
            Storage::disk('s3')->delete($document->path);
            $document->delete();
            return response()->json(['success' => true, 'message' => 'Document deleted'], 200);
        }

        return response()->json(['success' => false, 'message' => 'Unable to delete the document. Please try again later.'], 400);
    }
}
