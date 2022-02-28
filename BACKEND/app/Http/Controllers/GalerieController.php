<?php

namespace App\Http\Controllers;

use App\Models\galerie;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class GalerieController extends Controller
{

    /**
     * @OA\Get(
     *     path="/admin/galeries/list",
     *     tags={"ADMINISTRATION__GALERIE"},
     *     summary="Liste des images de la galérie",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function index()
    {
        $galeries =  galerie::all();

        return $galeries;
    }

    /**
     * @OA\Post(
     *     path="/admin/galeries/store",
     *     tags={"ADMINISTRATION__GALERIE"},
     *     summary="Ajouter des images à la galérie",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *               @OA\Property(
     *                   property = "user",
     *                   type="integer",
     *                   example = 1
     *                  ),
     *                @OA\Property(
     *                   property = "images[]",
     *                   type="array",
     *                     @OA\Items(
     *                          type = "string",
     *                          format = "binary"
     *                      )
     *                  ),
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'user' => 'required|numeric',
            'images.*' => 'required|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        $images = $request->file('images');

        if (count($images) > 4) {
            return response([
                "message" => "Vous ne pouvez charger que quatre images à la fois",
                "visibility" => false
            ], 200);
        }

        if (!empty($images)) {

            for ($i = 0; $i < count($images); $i++) {
                $completeFileName = $images[$i]->getClientOriginalName();

                $completeFileName = $images[$i]->getClientOriginalName();
                $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
                $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

                $img = Image::make($images[$i]);

                $img->resize(500, 500);

                $img->save(public_path('storage/galerie/' . $compPic), 40);

                $galerie = new galerie();

                $galerie->chemin = $compPic;
                $galerie->user_id = $request->user;
                $galerie->save();
            }
        }

        return response(["message" => "Images enregistrées avec succes"], 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/galeries/update/image",
     *    tags={"ADMINISTRATION__GALERIE"},
     *     summary="Moofier une image dans la galérie",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "id",
     *                   type="integer",
     *                   example = 1
     *                  ),
     *                 @OA\Property(
     *                   property = "image",
     *                   type="file",
     *                  ),
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function update_img(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg|max:5120'
        ]);

        $img = galerie::find($request->id);

        if (!$img) {
            return response([
                "message" => "Image introuvable",
                "visibility" => false
            ], 200);
        }

        $image = $request->file('image');

        $completeFileName = $image->getClientOriginalName();
        $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
        $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

        $image = Image::make($image);

        $image->resize(500, 500);

        $image->save(public_path('storage/galerie/' . $compPic), 40);

        unlink(public_path('storage/galerie/' . $img->chemin));

        $img->chemin = $compPic;
        $img->save();

        return response([
            "message" => "Image modifiée avec succés",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/galeries/delete/image",
     *    tags={"ADMINISTRATION__GALERIE"},
     *     summary="Supprimer une photo liée à un évènement",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "id",
     *                   type="integer",
     *                   example = 1
     *                  )
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function delete_img(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $image = galerie::find($request->id);

        if (!$image) {
            return response([
                "message" => "Image introuvable",
                "visibility" => false
            ], 200);
        }

        unlink(public_path('storage/galerie/' . $image->chemin));

        $image->delete();

        return response([
            "message" => "Image supprimée avec succés",
            "visibility" => true
        ], 200);
    }
}
