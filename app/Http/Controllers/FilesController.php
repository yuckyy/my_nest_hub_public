<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\File;
use Intervention\Image\ImageManagerStatic as Image;

class FilesController extends Controller
{

    public function profilePhotoUpload(Request $request)
    {
        //[, , $filename] = preg_split('/\//', $request->file('profile_photo')->store('public/profile'));

        $image = $request->file('profile_photo');
        $filename  = time() . rand(100000000,999999999) . '.' . $image->getClientOriginalExtension();
        $path = storage_path('app/public/profile/' . $filename);
        Image::make($image->getRealPath())->fit(400)->save($path);

        $user = Auth::user();
        if($user->photo_file_id){
            $oldPhoto = File::find($user->photo_file_id);
            Storage::delete('public/profile/' . $oldPhoto->filename);
            $oldPhoto->delete();
        }
        $photoFile = new File();
        $photoFile->filename = $filename;
        $photoFile->save();

        $user->photo_file_id = $photoFile->id;
        $user->save();

        $photo_file_id = $photoFile->id;
        $target_file_path_output[] = [
            'url' => url('storage/profile/' . $filename),
            'id' => $photo_file_id
        ];
        $output = ['uploaded' => $target_file_path_output];
        return response()->json($output);
    }

    public function profilePhotoDelete(Request $request)
    {
        $user = Auth::user();

        $photo = File::find($user->photo_file_id);
        Storage::delete('public/profile/' . $photo->filename);
        $photo->delete();

        $user->photo_file_id = null;
        $user->save();

        $output = ['success' => 'Processed'];
        return response()->json($output);
    }

    ##################################
    #  Property
    ##################################

    public function propertyImageUpload(Request $request)
    {
        $user = Auth::user();
        $property = Property::find($request->property_id);

        [, , , $filename] = preg_split('/\//', $request->file('property_image')->store('public/property/' . $property->id));

        if($property->img){
            $oldImage = $property->image;
            Storage::delete('public/property/' . $property->id . '/' . $oldImage->filename);
            $oldImage->delete();
        }
        $imageFile = new File();
        $imageFile->filename = $filename;
        $imageFile->save();

        $property->img = $imageFile->id;
        $property->save();

        $target_file_path_output[] = [
            'url' => url('storage/property/' . $property->id . '/' . $filename),
            'id' => $imageFile->id
        ];
        $output = ['uploaded' => $target_file_path_output];
        return response()->json($output);
    }

    public function propertyImageDelete(Request $request)
    {
        $user = Auth::user();
        $property = Property::find($request->property_id);

        $image = $property->image;
        Storage::delete('public/property/' . $property->id . '/' . $image->filename);
        $image->delete();

        $property->img = null;
        $property->save();

        $output = ['success' => 'Processed'];
        return response()->json($output);
    }

    public function propertyGalleryUpload(Request $request)
    {
        $user = Auth::user();
        $property = Property::find($request->property_id);

        if (!$request->has('property_images')) {
            return response()->json(['error' => 'No files found for upload.']);
        }

        $image_files = $request->file('property_images', []);
        $target_file_path_output = [];

        foreach ($image_files as $image_file) {
            [, , , , $filename] = preg_split('/\//', $image_file->store('public/property/' . $property->id . '/gallery'));
            $imageFile = new File();
            $imageFile->filename = $filename;
            $imageFile->save();

            DB::table('image_gallery')->insert([
                'property_id' => $property->id,
                'file_id' => $imageFile->id,
            ]);

            $target_file_path_output[] = [
                'url' => url('storage/property/' . $property->id . '/gallery/' . $filename),
                'id' => $imageFile->id
            ];

        }

        $output = ['uploaded' => $target_file_path_output];
        return response()->json($output);
    }

    public function propertyGalleryDelete(Request $request)
    {
        $user = Auth::user();
        $property = Property::find($request->property_id);
        $image = File::find($request->image_id);
        $image_id = $image->id;

        Storage::delete('public/property/' . $property->id . '/gallery/' . $image->filename);
        $image->delete();

        DB::statement(
            'DELETE `image_gallery` FROM `image_gallery` '.
            'WHERE `image_gallery`.`file_id` =:file_id',
            ['file_id' => $image_id]
        );

        $output = ['success' => 'Processed', 'image_id' => $image_id];
        return response()->json($output);
    }

    public function propertyGallerySort(Request $request)
    {
        $user = Auth::user();
        $property = Property::find($request->property_id);
        $imageIds = $request->sort;
        $imageIdsArray = explode(',',$imageIds);
        $sort = 0;
        foreach($imageIdsArray as $imageId){
            DB::table('image_gallery')
                ->where('file_id', $imageId)
                ->update(['sort' => $sort]);
            $sort++;
        }

        $output = ['success' => 'Processed','test' => $imageIds];
        return response()->json($output);
    }

    #############################
    # Unit
    #############################

    public function unitImageUpload(Request $request)
    {
        $user = Auth::user();
        $property = Property::find($request->property_id);
        $unit = Unit::find($request->unit_id);

        [, , , , $filename] = preg_split('/\//', $request->file('unit_image')->store('public/property/' . $property->id . '/' . $unit->id));

        if($unit->img){
            $oldImage = $unit->image;
            Storage::delete('public/property/' . $property->id . '/' . $unit->id . '/' . $oldImage->filename);
            $oldImage->delete();
        }
        $imageFile = new File();
        $imageFile->filename = $filename;
        $imageFile->save();

        $unit->img = $imageFile->id;
        $unit->save();

        $target_file_path_output[] = [
            'url' => url('storage/property/' . $property->id . '/' . $unit->id . '/' . $filename),
            'id' => $imageFile->id
        ];
        $output = ['uploaded' => $target_file_path_output];
        return response()->json($output);
    }

    public function unitImageDelete(Request $request)
    {
        $user = Auth::user();
        $property = Property::find($request->property_id);
        $unit = Unit::find($request->unit_id);

        $image = $unit->image;
        Storage::delete('public/property/' . $property->id . '/' . $unit->id . '/' . $image->filename);
        $image->delete();

        $unit->img = null;
        $unit->save();

        $output = ['success' => 'Processed'];
        return response()->json($output);
    }

    public function unitGalleryUpload(Request $request)
    {
        $user = Auth::user();
        $property = Property::find($request->property_id);
        $unit = Unit::find($request->unit_id);

        if (!$request->has('property_images')) {
            return response()->json(['error' => 'No files found for upload.']);
        }

        $image_files = $request->file('property_images', []);
        $target_file_path_output = [];

        foreach ($image_files as $image_file) {

//            $newName = 'public/property/' . $property->id . '/' . $unit->id . '/gallery';
//            $image_file->move('images/client', $newName);


            $store = $image_file->store('public/property/' . $property->id . '/' . $unit->id . '/gallery');

            [, , , , , $filename] = preg_split('/\//', $store);
            $imageFile = new File();
            $imageFile->filename = $filename;
            $imageFile->save();

            DB::table('unit_image_gallery')->insert([
                'unit_id' => $unit->id,
                'file_id' => $imageFile->id,
            ]);

            $target_file_path_output[] = [
                'url' => url('storage/property/' . $property->id . '/' . $unit->id . '/gallery/' . $filename),
                'id' => $imageFile->id
            ];
        }

        $output = ['uploaded' => $target_file_path_output];
        return response()->json($output);
    }

    public function unitGalleryDelete(Request $request)
    {
        $user = Auth::user();
        $property = Property::find($request->property_id);
        $unit = Unit::find($request->unit_id);
        $image = File::find($request->image_id);
        $image_id = $image->id;

        Storage::delete('public/property/' . $property->id . '/' . $unit->id . '/gallery/' . $image->filename);
        $image->delete();

        DB::statement(
            'DELETE `unit_image_gallery` FROM `unit_image_gallery` '.
            'WHERE `unit_image_gallery`.`file_id` =:file_id',
            ['file_id' => $image_id]
        );

        $output = ['success' => 'Processed', 'image_id' => $image_id];
        return response()->json($output);
    }

    public function unitGallerySort(Request $request)
    {
        $user = Auth::user();
        $property = Property::find($request->property_id);
        $unit = Unit::find($request->unit_id);
        $imageIds = $request->sort;
        $imageIdsArray = explode(',',$imageIds);
        $sort = 0;
        foreach($imageIdsArray as $imageId){
            DB::table('unit_image_gallery')
                ->where('file_id', $imageId)
                ->update(['sort' => $sort]);
            $sort++;
        }

        $output = ['success' => 'Processed','test' => $imageIds];
        return response()->json($output);
    }

}
