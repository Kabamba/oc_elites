<?php

namespace App\Http\Controllers;

use App\Models\Jersey;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class JerseyController extends Controller
{

    /**
     * @OA\Get(
     *     path="/admin/jerseys/list",
     *     tags={"ADMINISTRATION__MAILLOTS"},
     *     summary="Liste des maillots",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function index()
    {
        $jerseys =  Jersey::all();

        return $jerseys;
    }

    /**
     * @OA\Post(
     *     path="/admin/jerseys/store",
     *     tags={"ADMINISTRATION__MAILLOTS"},
     *     summary="Ajouter un maillot",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "libelle",
     *                   type="string",
     *                   example = "Maillot domicile"
     *                  ),
     *                @OA\Property(
     *                   property = "chemin",
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
    public function store(Request $request)
    {
        $request->validate([
            "libelle" => "required",
            'chemin' => 'required|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $jersey = new jersey();

        $jersey->titre = $request->libelle;

        $chemin = $request->file('chemin');

        $completeFileName = $chemin->getClientOriginalName();

        $completeFileName = $chemin->getClientOriginalName();
        $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
        $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

        $img = Image::make($chemin);

        $img->resize(500, 500);

        $img->save(public_path('storage/jerseys/' . $compPic), 40);

        $jersey->chemin = $compPic;

        $jersey->save();

        return response(["message" => "Maillot enregistré avec succes"], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/jerseys/show/{id}",
     *     tags={"ADMINISTRATION__MAILLOTS"},
     *     summary="Renvoie les informations d'une jersey par son ID",
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
        $jersey = jersey::where('id', $id)->first();

        if (!$jersey) {
            return response([
                "message" => "Maillot introuvable",
                "type" => "danger",
                "visibility" => true
            ], 404);
        }

        return $jersey;
    }

    /**
     * @OA\Post(
     *     path="/admin/jerseys/update",
     *     tags={"ADMINISTRATION__MAILLOTS"},
     *     summary="Edite les informations d'une jersey",
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
     *                   property = "libelle",
     *                   type="string",
     *                   example = "Nike"
     *                  ),
     *                @OA\Property(
     *                   property = "chemin",
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
    public function update(Request $request)
    {
        $request->validate([
            "id" => "required|numeric",
            "libelle" => "required",
            'chemin' => 'nullable|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $jersey = jersey::where('id', $request->id)->first();

        if (!$jersey) {
            return response([
                "message" => "Maillot introuvable",
                "visibility" => false
            ], 404);
        }

        $exist = jersey::where('titre', $request->libelle)->first();

        if ($exist) {
            return response([
                "message" => "Un jersey avec le même nom existe déjà",
                "visibility" => false
            ], 404);
        }

        $jersey->titre = $request->libelle;

        $chemin = $request->file('chemin');

        unlink(public_path('storage/jerseys/' . $jersey->chemin));

        if (!empty($chemin)) {
            $completeFileName = $chemin->getClientOriginalName();

            $completeFileName = $chemin->getClientOriginalName();
            $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
            $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

            $img = Image::make($chemin);

            $img->resize(500, 500);

            $img->save(public_path('storage/jerseys/' . $compPic), 40);

            $jersey->chemin = $compPic;
        }

        $jersey->save();

        return response([
            "message" => "Maillot modifié avec succés",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/jerseys/delete/{id}",
     *    tags={"ADMINISTRATION__MAILLOTS"},
     *     summary="Supprime une jersey par son ID",
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
    public function delete($id)
    {
        $jersey = jersey::where('id', $id)->first();

        if (!$jersey) {
            return response([
                "message" => "jersey introuvable",
                "visibility" => false
            ], 404);
        }

        unlink(public_path('storage/jerseys/' . $jersey->chemin));

        $jersey->delete();


        return response([
            "message" => "jersey supprimé",
            "visibility" => true
        ], 200);
    }
}
