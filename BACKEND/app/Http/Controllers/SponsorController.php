<?php

namespace App\Http\Controllers;

use App\Models\sponsor;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class SponsorController extends Controller
{

    /**
     * @OA\Get(
     *     path="/admin/sponsors/list",
     *     tags={"sponsorADMINISTRATION__SPONSORS"},
     *     summary="Liste des sponsors",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function index()
    {
        $sponsors =  sponsor::all();

        return $sponsors;
    }

    /**
     * @OA\Post(
     *     path="/admin/sponsors/store",
     *     tags={"sponsorADMINISTRATION__SPONSORS"},
     *     summary="Ajouter une sponsor",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "libelle",
     *                   type="string",
     *                   example = "Adidas"
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
            "libelle" => "required|unique:sponsors",
            'chemin' => 'required|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $sponsor = new sponsor();

        $sponsor->libelle = $request->libelle;

        $chemin = $request->file('chemin');

        $completeFileName = $chemin->getClientOriginalName();

        $completeFileName = $chemin->getClientOriginalName();
        $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
        $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

        $img = Image::make($chemin);

        $img->resize(500, 500);

        $img->save(public_path('storage/sponsors/' . $compPic), 40);

        $sponsor->chemin = $compPic;

        $sponsor->save();

        return response(["message" => "Sponsor enregistré avec succes"], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/sponsors/show/{id}",
     *     tags={"sponsorADMINISTRATION__SPONSORS"},
     *     summary="Renvoie les informations d'une sponsor par son ID",
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
        $sponsor = sponsor::where('id', $id)->first();

        if (!$sponsor) {
            return response([
                "message" => "Sponsor introuvable",
                "type" => "danger",
                "visibility" => true
            ], 404);
        }

        return $sponsor;
    }

    /**
     * @OA\Post(
     *     path="/admin/sponsors/update",
     *     tags={"sponsorADMINISTRATION__SPONSORS"},
     *     summary="Edite les informations d'une sponsor",
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

        $sponsor = sponsor::where('id', $request->id)->first();

        if (!$sponsor) {
            return response([
                "message" => "Sponsor introuvable",
                "visibility" => false
            ], 404);
        }

        $exist = sponsor::where('libelle', $request->libelle)->first();

        if ($exist) {
            return response([
                "message" => "Un sponsor avec le même nom existe déjà",
                "visibility" => false
            ], 404);
        }

        $sponsor->libelle = $request->libelle;

        $chemin = $request->file('chemin');

        unlink(public_path('storage/sponsors/' . $sponsor->chemin));

        if (!empty($chemin)) {
            $completeFileName = $chemin->getClientOriginalName();

            $completeFileName = $chemin->getClientOriginalName();
            $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
            $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

            $img = Image::make($chemin);

            $img->resize(500, 500);

            $img->save(public_path('storage/sponsors/' . $compPic), 40);

            $sponsor->chemin = $compPic;
        }

        $sponsor->save();

        return response([
            "message" => "Sponsor modifié avec succés",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/sponsors/delete/{id}",
     *    tags={"sponsorADMINISTRATION__SPONSORS"},
     *     summary="Supprime une sponsor par son ID",
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
        $sponsor = sponsor::where('id', $id)->first();

        if (!$sponsor) {
            return response([
                "message" => "sponsor introuvable",
                "visibility" => false
            ], 404);
        }

        unlink(public_path('storage/sponsors/' . $sponsor->chemin));

        $sponsor->delete();

        return response([
            "message" => "sponsor supprimée",
            "visibility" => true
        ], 200);
    }
}
