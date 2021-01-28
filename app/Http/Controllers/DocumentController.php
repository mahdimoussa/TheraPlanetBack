<?php

namespace App\Http\Controllers;

use App\Document;
use App\Http\Controllers\Controller;
use App\Post;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::where('approved', false)->get();
        foreach ($documents as $document) {
            $first_name = $document->user()->first_name;
            $last_name = $document->user()->last_name;
            $document->fullname = $first_name . " " . $last_name;
        }
        return $documents;
    }

    public function store(Request $request)
    {


        $validator = $request->validate([
            'id_photo' => ['required'],
            'license_photo' => ['required']
        ]);
        if ($request->hasFile('id_photo') && $request->hasFile('license_photo')) {

            // Get filename with the extension
            $filenameWithExt = $request->file('id_photo')->getClientOriginalName();
            $filenameWithExt2 = $request->file('license_photo')->getClientOriginalName();
            //Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $filename2 = pathinfo($filenameWithExt2, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('id_photo')->getClientOriginalExtension();
            $extension2 = $request->file('license_photo')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $fileNameToStore2 = $filename2 . '_' . time() . '.' . $extension2;
            // Upload Image
            $path = $request->file('id_photo')->storeAs('public/media', $fileNameToStore);
            $path2 = $request->file('license_photo')->storeAs('public/media', $fileNameToStore2);

            $document = new Document($validator);
            $document->user_id = $request->user()->id;
            $document->id_photo = $fileNameToStore;
            $document->license_photo = $fileNameToStore2;
            $document->type = $request->type;
            $document->approved = 0;
            $document->save();

            return response()->json('Documents were uploaded successfully', 200);

        } else {
            return Response("Error Documents Missing");
        }
    }

    public function approve(Document $document)
    {
        $document->approved = true;
        $document->save();
        return response()->json('Accepted');
    }

    public function destroy(Document $document)
    {
        # TODO: remove images from Storage
        # Storage::delete($document->id_photo);
        # Storage::delete($document->license_photo);

        $document->delete();
        return response()->json('Documents were deleted successfully');
    }


    public function checkApproved(Request $request)
    {

        $document = Document::where('user_id', '=', $request->user()->id)->get();
//        $document = Document::all();
        return $document;
    }

    public function unApprovedDocuments(){
        $documents = Document::where('approved','=','0')->orderBy('created_at','desc')->get();
        return $documents;
    }

    public function accept(Request $request){
        $document = Document::findOrFail($request->id);
        $document->approved = 1;
        $document->save();
        return response()->json('Document accepted successfully');
    }
}
