<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MultiTaskController extends Controller
{
    /**
     * Upload files (images) to the server.
     *
     * @return ['uploaded'=>true]
     */
    public function uploadFile(Request $req) 
    {
        sleep(1);//Need it for not overwrite some contents
        $resize = $req->resize ? (array) json_decode($req->resize) : false;

        $file = $this->upload_file( $req->file('file'), $req->path, $req->rename, $resize );

        if (! $file ) { return response(['msg' => 'not uploaded', 'status' => 'error'], 200); }
        
        return response(['msg' => 'uploaded', 'status' => 'ok'], 200);
    }
}
