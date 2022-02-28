<?php

namespace App\Http\Controllers;

use App\Http\Resources\StafResource;
use App\Models\staf;
use App\Models\staf_image;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class StafController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/stafs/list",
     *     tags={"ADMINISTRATION__STAFS"},
     *     summary="Liste des stafs",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function index()
    {
        $stafs =  staf::all();

        return StafResource::collection($stafs);
    }

    /**
     * @OA\Post(
     *     path="/admin/stafs/store",
     *     tags={"ADMINISTRATION__STAFS"},
     *     summary="Ajouter un staf",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "nom",
     *                   type="string",
     *                   example = "Mulamba"
     *                  ),
     *                @OA\Property(
     *                   property = "prenom",
     *                   type="string",
     *                   example = "Enock"
     *                  ),
     *                @OA\Property(
     *                   property = "postnom",
     *                   type="string",
     *                   example = "Kabamba"
     *                  ),
     *                @OA\Property(
     *                   property = "date_naissance",
     *                   type="date",
     *                   example = "1998-02-18"
     *                  ),
     *                @OA\Property(
     *                   property = "lieu_naissance",
     *                   type="string",
     *                   example = "Mbuji-mayi"
     *                  ),
     *                @OA\Property(
     *                   property = "date_debut_carriere",
     *                   type="date",
     *                   example = "2010-08-10"
     *                  ),
     *                @OA\Property(
     *                   property = "date_signature",
     *                   type="date",
     *                   example = "2020-09-04"
     *                  ),
     *                @OA\Property(
     *                   property = "nationalite",
     *                   type="string",
     *                   example = "Congolaise"
     *                  ),
     *                @OA\Property(
     *                   property = "attribution",
     *                   type="integer",
     *                   example = 2
     *                  ),
     *                 @OA\Property(
     *                   property = "descriptions",
     *                   type="string",
     *                   example = "Une description"
     *                  ),
     *                 @OA\Property(
     *                   property = "cover",
     *                   type="file",
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
            "nom" => "required",
            "prenom" => "required",
            "postnom" => "required",
            "date_naissance" => "required|date",
            "lieu_naissance" => "required",
            "date_debut_carriere" => "required|date",
            "date_signature" => "required|date",
            "nationalite" => "required",
            "attribution" => "required|numeric",
            "descriptions" => "required",
            "cover" => "required|mimes:jpeg,png,jpg,webp|max:5120",
            'images.*' => 'nullable|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        $staf = new staf();

        $staf->nom = $request->nom;
        $staf->prenom = $request->prenom;
        $staf->postnom = $request->postnom;
        $staf->date_naiss = $request->date_naissance;
        $staf->lieu_naiss = $request->lieu_naissance;
        $staf->date_deb_car = $request->date_debut_carriere;
        $staf->date_deb_equipe = $request->date_signature;
        $staf->nationality = $request->nationalite;
        $staf->attribution_id = $request->attribution;
        $staf->descriptions = $request->descriptions;
        $staf->save();

        /** Ajout de l'image de couverture */

        $cover = $request->file('cover');

        $completeFileName = $cover->getClientOriginalName();

        $completeFileName = $cover->getClientOriginalName();
        $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
        $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

        $img = Image::make($cover);

        $img->resize(500, 500);

        $img->save(public_path('storage/stafs/' . $compPic), 40);

        $staf_image = new staf_image();

        $staf_image->chemin = $compPic;
        $staf_image->staf_id = $staf->id;
        $staf_image->covert = 1;
        $staf_image->save();

        /** Ajout de l'image de couverture */


        $images = $request->file('images');

        if (!empty($images)) {

            for ($i = 0; $i < count($images); $i++) {
                $completeFileName = $images[$i]->getClientOriginalName();

                $completeFileName = $images[$i]->getClientOriginalName();
                $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
                $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

                $img = Image::make($images[$i]);

                $img->resize(500, 500);

                $img->save(public_path('storage/stafs/' . $compPic), 40);

                $staf_image = new staf_image();

                $staf_image->chemin = $compPic;
                $staf_image->staf_id = $staf->id;
                $staf_image->covert = 0;
                $staf_image->save();
            }
        }

        return response(["message" => "staf enregistré avec succes"], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/stafs/show/{id}",
     *     tags={"ADMINISTRATION__STAFS"},
     *     summary="Renvoie les informations d'un staf par son ID",
     *      @OA\Parameter(
     *          name = "id",
     *          required = true,
     *          in = "path",
     *          example = 18,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function show($id)
    {
        $staf = staf::where('id', $id)->first();

        if (!$staf) {
            return response([
                "message" => "staf introuvable",
                "type" => "danger",
                "visibility" => true
            ], 404);
        }

        return stafResource::make($staf);
    }

    /**
     * @OA\Post(
     *     path="/admin/stafs/update",
     *     tags={"ADMINISTRATION__STAFS"},
     *     summary="Editer les informations d'un staf",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *               @OA\Property(
     *                   property = "id",
     *                   type="integer",
     *                   example = 1
     *                  ),
     *                @OA\Property(
     *                   property = "nom",
     *                   type="string",
     *                   example = "Mulamba"
     *                  ),
     *                @OA\Property(
     *                   property = "prenom",
     *                   type="string",
     *                   example = "Enock"
     *                  ),
     *                @OA\Property(
     *                   property = "postnom",
     *                   type="string",
     *                   example = "Kabamba"
     *                  ),
     *               @OA\Property(
     *                   property = "date_naissance",
     *                   type="date",
     *                   example = "1998-02-18"
     *                  ),
     *                @OA\Property(
     *                   property = "lieu_naissance",
     *                   type="string",
     *                   example = "Mbuji-mayi"
     *                  ),
     *               @OA\Property(
     *                   property = "date_debut_carriere",
     *                   type="date",
     *                   example = "2010-08-10"
     *                  ),
     *                @OA\Property(
     *                   property = "date_signature",
     *                   type="date",
     *                   example = "2020-09-04"
     *                  ),
     *                @OA\Property(
     *                   property = "nationalite",
     *                   type="string",
     *                   example = "Congolaise"
     *                  ),
     *                 @OA\Property(
     *                   property = "attribution",
     *                   type="integer",
     *                   example = 4
     *                  ),
     *                 @OA\Property(
     *                   property = "descriptions",
     *                   type="string",
     *                   example = "Une modif de la description"
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
    public function update(Request $request)
    {
        $request->validate([
            "id" => "required",
            "nom" => "required",
            "prenom" => "required",
            "postnom" => "required",
            "date_naissance" => "required|date",
            "lieu_naissance" => "required",
            "date_debut_carriere" => "required|date",
            "date_signature" => "required|date",
            "nationalite" => "required",
            "attribution" => "required|numeric",
            "descriptions" => "required",
        ]);

        $staf = staf::where('id', $request->id)->first();

        if (!$staf) {
            return response([
                "message" => "staf introuvable",
                "type" => "danger",
                "visibility" => true
            ], 404);
        }

        $staf->nom = $request->nom;
        $staf->prenom = $request->prenom;
        $staf->postnom = $request->postnom;
        $staf->date_naiss = $request->date_naissance;
        $staf->lieu_naiss = $request->lieu_naissance;
        $staf->date_deb_car = $request->date_debut_carriere;
        $staf->date_deb_equipe = $request->date_signature;
        $staf->nationality = $request->nationalite;
        $staf->attribution_id = $request->attribution;
        $staf->descriptions = $request->descriptions;
        $staf->save();

        return response([
            "message" => "staf modifié avec succés",
            "type" => "success",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/stafs/update/image",
     *    tags={"ADMINISTRATION__STAFS"},
     *     summary="Moofier une photo liée à un évènement",
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
     *                @OA\Property(
     *                   property = "staf_id",
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
            'staf_id' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg|max:5120'
        ]);

        $img = staf_image::find($request->id);

        if (!$img) {
            return response([
                "message" => "staf introuvable",
                "visibility" => false
            ], 200);
        }

        $image = $request->file('image');

        $completeFileName = $image->getClientOriginalName();
        $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
        $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

        $image = Image::make($image);

        $image->resize(500, 500);

        $image->save(public_path('storage/stafs/' . $compPic), 40);

        unlink(public_path('storage/stafs/' . $img->chemin));

        $img->chemin = $compPic;
        $img->staf_id = $request->staf_id;

        if ($img->covert == 1) {
            $img->covert = 1;
        }

        $img->save();

        return response([
            "message" => "Image modifiée avec succés",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/stafs/delete/image",
     *    tags={"ADMINISTRATION__STAFS"},
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

        $image = staf_image::find($request->id);

        if (!$image) {
            return response([
                "message" => "Image introuvable",
                "visibility" => false
            ], 200);
        }

        if ($image->covert == 1) {
            return response([
                "message" => "Impossible de supprimer une image de couverture",
                "visibility" => false
            ], 200);
        }

        unlink(public_path('storage/stafs/' . $image->chemin));

        $image->delete();

        return response([
            "message" => "Image supprimée avec succés",
            "visibility" => true
        ], 200);
    }
}
