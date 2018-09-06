<?php

namespace App\Http\Controllers\Api;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Requests\Api\ImageRequest;
use App\Handlers\ImageUploadHandler;

class ImagesController extends Controller
{
    public function store(ImageRequest $request, ImageUploadHandler $uploader, Image $image)
    {
        $result = $uploader->save($request->image, $request->type, $request->type);

        $image->image = $request['path'];
        $image->save();

        return $this->response->array($result)->setStatusCode(201);
    }
}
