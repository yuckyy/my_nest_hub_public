<?php

namespace App\Http\Controllers\Properties;

use App\Models\Document;
use App\Models\Expenses;
use App\Models\Payment;
use App\Models\Property;
use App\Models\PropertyType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Array_;
use Storage;

class DocumentsController extends Controller
{
    public function index($property_id, Request $request) {

        $property = Property::find($property_id);

        if (!$property) {
            abort(404);
        }
        $user = Auth::user();
        $units = $property->units;
        $documents = Array();
        $allDocuments = Array();
        foreach ($units as $unit){
            $query2 = DB::table('documents')
                ->where('documents.unit_id' ,'=' ,$unit->id)
//                ->join('leases', 'documents.unit_id', '=', 'leases.unit_id')
//                ->where('leases.deleted_at' ,'=' ,null)
                ->get();
//            $query3 = DB::table('leases')
//                ->where('unit_id' ,'=' ,$unit->id)
//                ->where('deleted_at' ,'=' ,null)
//                ->get();

//            var_dump($query2);
//            die;
        array_push($documents, $query2);
        }

        foreach ($documents as $doc){
            foreach ($doc as $do){
                array_push($allDocuments, $do);
            }
        }
//        var_dump($allDocuments);
//        die;
        $sql = '';//for testing
        return view(
            'properties.documents',
            [
                'types' => PropertyType::all(),
                'user' => $user,
                'property' => $property,
                'units' => $units,
                'sql' => $sql,
                'documents' => $allDocuments,
                'countDocuments' => count($allDocuments)
            ]
        );
    }
    public function documentDelete(Request $request)
    {
        $user = Auth::user();
        $document = Document::where(['id' => $request->document_id])->first();

        if(!empty($document)) {
            $document_id = $document->id;

            \Illuminate\Support\Facades\Storage::delete('public/' . $document->filepath);
            if(!empty($document->thumbnailpath)){
                Storage::delete('public/' . $document->thumbnailpath);
            }
            $document->delete();

            $output = ['success' => 'Processed', 'document_id' => $document_id];
            return response()->json($output);
        };

    }

//    public function categoryFilter(Request $request)
//    {
//        $user = Auth::user();
//        $document = Document::where(['id' => $request->document_id])->first();
//
//        if(!empty($document)) {
//            $document_id = $document->id;
//
//            \Illuminate\Support\Facades\Storage::delete('public/' . $document->filepath);
//            if(!empty($document->thumbnailpath)){
//                Storage::delete('public/' . $document->thumbnailpath);
//            }
//            $document->delete();
//
//            $output = ['success' => 'Processed', 'document_id' => $document_id];
//            return response()->json($output);
//        };
//
//    }
    public function documentUpload(Request $request)
    {
        $user = Auth::user();

        $allowed_extensions = ['doc', 'docx', 'pdf', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'xls', 'xlsx', 'csv'];

        if (!$request->has('documents')) {
            return response()->json(['error' => 'No files found for upload.']);
        }

        $document_files = $request->file('documents', []);
        $target_file_path_output = [];

        foreach ($document_files as $document_file) {
            if(in_array(strtolower($document_file->getClientOriginalExtension()), $allowed_extensions)){

                $filePath = 'public/documents/' . $user->id . '/' . $request->unit_id;
                [, $filepath] = preg_split('/\//', $document_file->store($filePath), 2);
                $document = new Document();
                $document->user_id = $user->id;
//                $document->lease_id = empty($request->lease_id) ? null : $request->lease_id;
                $document->unit_id = $request->unit_id;
                $document->document_category = $request->document_category;
                $document->filepath = $filepath;
                $document->document_type = 'shared_document';
                $document->name = $document_file->getClientOriginalName();
                $document->extension = $document_file->getClientOriginalExtension();
                $document->mime = $document_file->getMimeType();
                $document->save();

                $target_file_path_output[] = [
                    'url' => url('storage/' . $filepath),
                    'name' => $document->name,
                    'icon' => $document->icon(),
                    'id' => $document->id
                ];

            } else {
                $target_file_path_output[] = [
                    'error' => 'File type not allowed',
                    'name' => $document_file->getClientOriginalName(),
                    'icon' => '<i class="fal fa-file"></i>',
                    'id' => '0'
                ];
            }

        }

        $output = ['uploaded' => $target_file_path_output];
        return response()->json($output);
    }
}
