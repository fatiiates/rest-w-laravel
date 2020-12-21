<?php

namespace App\Http\Controllers\API\USER;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Validator;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public $successStatus = 200;
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function upload(Request $request)
    {   
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimetypes:application/x-python-code,text/x-python,text/plain|max:10485760'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'err_code' => 401,
                'description' => $request->file('file')->getMimeType(),
            ], 401);
        }
        
        $uploadFolder = '/app/files/uploads/'.$user['directory_id'];
        $filename = $request->file('file')->getClientOriginalName();
        if(file_exists(storage_path($uploadFolder.'/'.$filename)))
            return response()->json([
                'ok' => false,
                'err_code' => 401,
                'description' => 'Aynı isimde mevcut bir dosyanız bulunuyor.',
            ], 401);
        $file = $request->file('file')->move(storage_path($uploadFolder), $filename);
        return response()->json([
            'ok' => true,
            'result' => [
                'message' => 'Dosya yüklemesi başarılı.'
            ]
        ], $this->successStatus);
        
        //return sendCustomResponse('File Uploaded Successfully', 'success',   200, $uploadedImageResponse);
    }

    public function uploadedFiles(Request $request)
    {   
        $user = Auth::user();
        
        $uploadFolder = '/app/files/uploads/'.$user['directory_id'];
        if(file_exists(storage_path($uploadFolder))){
            $scan = scandir(storage_path($uploadFolder));
            $allFiles = array();
            foreach($scan as $file) {
                if (!is_dir("myFolder/".$file) && $file != '.' && $file != '..') 
                    array_push($allFiles, $file);            
            }
            return response()->json([
                'ok' => true,
                'result' => [
                    'message' => 'Dosya isimler başarıyla getirildi.',
                    'files' => $allFiles
                ]
            ], $this->successStatus);
        }
        else
            return response()->json([
                'ok' => false,
                'err_code' => 404,
                'description' => 'Daha önce bir yükleme yapılmamış.',
            ], 404);   
    }
    public function download(Request $request)
    {   
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'targetFile' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'err_code' => 401,
                'description' => $validator->messages()->first(),
            ], 401);
        }
        $input = $request->all();
        $uploadFolder = '/app/files/uploads/'.$user['directory_id'];
        $filename = $input['targetFile'];
        if(file_exists(storage_path($uploadFolder.'/'.$filename)))
            return response()->download(storage_path($uploadFolder.'/'.$filename));
        else
            return response()->json([
                'ok' => false,
                'err_code' => 404,
                'description' => 'Bir sorun oluştur, dosya indirilemiyor.',
            ], 404);   
    }
}
