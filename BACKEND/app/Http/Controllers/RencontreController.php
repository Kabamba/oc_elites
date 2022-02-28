<?php

namespace App\Http\Controllers;

use App\Models\rencontre;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class RencontreController extends Controller
{

    /**
     * @OA\Get(
     *     path="/admin/rencontres/list",
     *     tags={"ADMINISTRATION__RENCONTRES"},
     *     summary="Liste des rencontres",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function index()
    {
        $rencontres =  rencontre::all();

        return $rencontres;
    }

    /**
     * @OA\Post(
     *     path="/admin/rencontres/store",
     *     tags={"ADMINISTRATION__RENCONTRES"},
     *     summary="Ajouter une rencontre",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "team_home_name",
     *                   type="string",
     *                   example = "Real madrid"
     *                  ),
     *                @OA\Property(
     *                   property = "team_away_name",
     *                   type="string",
     *                   example = "FC barcelone"
     *                  ),
     *                @OA\Property(
     *                   property = "team_home_logo",
     *                   type="file",
     *                  ),
     *                @OA\Property(
     *                   property = "team_away_logo",
     *                   type="file",
     *                  ),
     *                @OA\Property(
     *                   property = "team_home_score",
     *                   type="integer",
     *                   example = 1
     *                  ),
     *                @OA\Property(
     *                   property = "team_away_score",
     *                   type="integer",
     *                   example = 1
     *                  ),
     *                @OA\Property(
     *                   property = "date_rencontre",
     *                   type="date",
     *                   example = "2020-09-04"
     *                  ),
     *                @OA\Property(
     *                   property = "user",
     *                   type="integer",
     *                   example = 1
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
            "team_home_name" => "required",
            "team_away_name" => "required",
            'team_home_logo' => 'required|mimes:jpeg,png,jpg,webp|max:5120',
            'team_away_logo' => 'required|mimes:jpeg,png,jpg,webp|max:5120',
            "team_home_score" => "required",
            "team_away_score" => "required",
            "date_rencontre" => "required|date",
            "user" => "required|numeric",
        ]);

        $rencontre = new rencontre();

        $rencontre->team_home_name = $request->team_home_name;
        $rencontre->team_away_name = $request->team_away_name;
        $rencontre->team_home_score = $request->team_home_score;
        $rencontre->team_away_score = $request->team_away_score;
        $rencontre->date_match    = $request->date_rencontre;
        $rencontre->user_id = $request->user;

        /** Ajout du logo equipe 1 */

        $team_home_logo = $request->file('team_home_logo');

        $completeFileName = $team_home_logo->getClientOriginalName();

        $completeFileName = $team_home_logo->getClientOriginalName();
        $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
        $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

        $img = Image::make($team_home_logo);

        $img->resize(500, 500);

        $img->save(public_path('storage/logo/' . $compPic), 40);

        $rencontre->team_home_logo = $compPic;

        /** Ajout du logo equipe 1 */



        /** Ajout du logo equipe 2 */

        $team_away_logo = $request->file('team_away_logo');

        $completeFileName = $team_away_logo->getClientOriginalName();

        $completeFileName = $team_away_logo->getClientOriginalName();
        $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
        $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

        $img = Image::make($team_away_logo);

        $img->resize(500, 500);

        $img->save(public_path('storage/logo/' . $compPic), 40);

        $rencontre->team_away_logo = $compPic;

        /** Ajout du logo equipe 2 */

        $rencontre->save();

        return response(["message" => "Rencontre enregistré avec succes"], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/rencontres/show/{id}",
     *     tags={"ADMINISTRATION__RENCONTRES"},
     *     summary="Renvoie les informations d'une rencontre par son ID",
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
        $rencontre = rencontre::where('id', $id)->first();

        if (!$rencontre) {
            return response([
                "message" => "Rencontre introuvable",
                "type" => "danger",
                "visibility" => true
            ], 404);
        }

        return $rencontre;
    }

    /**
     * @OA\Post(
     *     path="/admin/rencontres/update",
     *     tags={"ADMINISTRATION__RENCONTRES"},
     *     summary="Edite les informations d'une rencontre",
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
     *                   property = "team_home_name",
     *                   type="string",
     *                   example = "Real madrid"
     *                  ),
     *                @OA\Property(
     *                   property = "team_away_name",
     *                   type="string",
     *                   example = "FC barcelone"
     *                  ),
     *                @OA\Property(
     *                   property = "team_home_logo",
     *                   type="file",
     *                  ),
     *                @OA\Property(
     *                   property = "team_away_logo",
     *                   type="file",
     *                  ),
     *                @OA\Property(
     *                   property = "team_home_score",
     *                   type="integer",
     *                   example = 1
     *                  ),
     *                @OA\Property(
     *                   property = "team_away_score",
     *                   type="integer",
     *                   example = 1
     *                  ),
     *                @OA\Property(
     *                   property = "date_rencontre",
     *                   type="date",
     *                   example = "2020-09-04"
     *                  ),
     *                @OA\Property(
     *                   property = "user",
     *                   type="integer",
     *                   example = 1
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
            "team_home_name" => "required",
            "team_away_name" => "required",
            'team_home_logo' => 'nullable|mimes:jpeg,png,jpg,webp|max:5120',
            'team_away_logo' => 'nullable|mimes:jpeg,png,jpg,webp|max:5120',
            "team_home_score" => "required",
            "team_away_score" => "required",
            "date_rencontre" => "required|date",
            "user" => "required|numeric",
        ]);


        $rencontre = rencontre::where('id', $request->id)->first();

        if (!$rencontre) {
            return response([
                "message" => "Rencontre introuvable",
                "type" => "danger",
                "visibility" => true
            ], 404);
        }

        $rencontre->team_home_name = $request->team_home_name;
        $rencontre->team_away_name = $request->team_away_name;
        $rencontre->team_home_score = $request->team_home_score;
        $rencontre->team_away_score = $request->team_away_score;
        $rencontre->date_match = $request->date_rencontre;
        $rencontre->user_id = $request->user;

        /** Ajout du logo equipe 1 */

        $team_home_logo = $request->file('team_home_logo');

        if (!empty($team_home_logo)) {
            $completeFileName = $team_home_logo->getClientOriginalName();

            $completeFileName = $team_home_logo->getClientOriginalName();
            $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
            $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

            $img = Image::make($team_home_logo);

            $img->resize(500, 500);

            $img->save(public_path('storage/logo/' . $compPic), 40);

            $rencontre->team_home_logo = $compPic;
        }

        /** Ajout du logo equipe 1 */



        /** Ajout du logo equipe 2 */

        $team_away_logo = $request->file('team_away_logo');

        if (!empty($team_away_logo)) {

            $completeFileName = $team_away_logo->getClientOriginalName();

            $completeFileName = $team_away_logo->getClientOriginalName();
            $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
            $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

            $img = Image::make($team_away_logo);

            $img->resize(500, 500);

            $img->save(public_path('storage/logo/' . $compPic), 40);

            $rencontre->team_away_logo = $compPic;
        }

        /** Ajout du logo equipe 2 */

        $rencontre->save();

        return response([
            "message" => "Rencontre modifiée avec succés",
            "type" => "success",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/rencontres/delete/{id}",
     *    tags={"ADMINISTRATION__RENCONTRES"},
     *     summary="Supprime une rencontre par son ID",
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
        $rencontre = rencontre::where('id', $id)->first();

        if (!$rencontre) {
            return response([
                "message" => "Rencontre introuvable",
                "visibility" => false
            ], 404);
        }

        $rencontre->delete();

        return response([
            "message" => "Rencontre supprimée",
            "visibility" => true
        ], 200);
    }
}
