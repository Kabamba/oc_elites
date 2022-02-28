<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlayerResource;
use App\Models\player;
use App\Models\player_image;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class PlayerController extends Controller
{

    /**
     * @OA\Get(
     *     path="/admin/players/list",
     *     tags={"ADMINISTRATION__JOUEURS"},
     *     summary="Liste des joueurs",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function index()
    {
        $players =  player::all();

        return PlayerResource::collection($players);
    }

    /**
     * @OA\Post(
     *     path="/admin/players/store",
     *     tags={"ADMINISTRATION__JOUEURS"},
     *     summary="Ajouter un joueur",
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
     *                 @OA\Property(
     *                   property = "poids",
     *                   type="float",
     *                   example = 70
     *                  ),
     *                 @OA\Property(
     *                   property = "taille",
     *                   type="float",
     *                   example = 189
     *                  ),
     *                 @OA\Property(
     *                   property = "dorsale",
     *                   type="integer",
     *                   example = 9
     *                  ),
     *                 @OA\Property(
     *                   property = "position",
     *                   type="integer",
     *                   example = 4
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
            "poids" => "required|numeric",
            "taille" => "required|numeric",
            "dorsale" => "required|numeric",
            "position" => "required|numeric",
            "descriptions" => "required",
            "cover" => "required|mimes:jpeg,png,jpg,webp|max:5120",
            'images.*' => 'nullable|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        $player = new player();

        $player->nom = $request->nom;
        $player->prenom = $request->prenom;
        $player->postnom = $request->postnom;
        $player->date_naiss = $request->date_naissance;
        $player->lieu_naiss = $request->lieu_naissance;
        $player->date_deb_car = $request->date_debut_carriere;
        $player->date_deb_equipe = $request->date_signature;
        $player->nationality = $request->nationalite;
        $player->poids = $request->poids;
        $player->taille = $request->taille;
        $player->dorsale_number = $request->dorsale;
        $player->position_id = $request->position;
        $player->descriptions = $request->descriptions;
        $player->save();

        /** Ajout de l'image de couverture */

        $cover = $request->file('cover');

        $completeFileName = $cover->getClientOriginalName();

        $completeFileName = $cover->getClientOriginalName();
        $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
        $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

        $img = Image::make($cover);

        $img->resize(500, 500);

        $img->save(public_path('storage/players/' . $compPic), 40);

        $player_image = new player_image();

        $player_image->chemin = $compPic;
        $player_image->player_id = $player->id;
        $player_image->covert = 1;
        $player_image->save();

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

                $img->save(public_path('storage/players/' . $compPic), 40);

                $player_image = new player_image();

                $player_image->chemin = $compPic;
                $player_image->player_id = $player->id;
                $player_image->covert = 0;
                $player_image->save();
            }
        }

        return response(["message" => "Joueur enregistré avec succes"], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/players/show/{id}",
     *     tags={"ADMINISTRATION__JOUEURS"},
     *     summary="Renvoie les informations d'un joueur par son ID",
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
        $player = player::where('id', $id)->first();

        if (!$player) {
            return response([
                "message" => "Joueur introuvable",
                "type" => "danger",
                "visibility" => true
            ], 404);
        }

        return PlayerResource::make($player);
    }

    /**
     * @OA\Post(
     *     path="/admin/players/update",
     *     tags={"ADMINISTRATION__JOUEURS"},
     *     summary="Editer les informations d'un Joueur",
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
     *                   property = "poids",
     *                   type="float",
     *                   example = 70
     *                  ),
     *                 @OA\Property(
     *                   property = "taille",
     *                   type="float",
     *                   example = 70
     *                  ),
     *                 @OA\Property(
     *                   property = "dorsale",
     *                   type="integer",
     *                   example = 9
     *                  ),
     *                 @OA\Property(
     *                   property = "position",
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
            "poids" => "required|numeric",
            "taille" => "required|numeric",
            "dorsale" => "required|numeric",
            "position" => "required|numeric",
            "descriptions" => "required",
        ]);

        $player = player::where('id', $request->id)->first();

        if (!$player) {
            return response([
                "message" => "Joueur introuvable",
                "type" => "danger",
                "visibility" => true
            ], 404);
        }

        $player->nom = $request->nom;
        $player->prenom = $request->prenom;
        $player->postnom = $request->postnom;
        $player->date_naiss = $request->date_naissance;
        $player->lieu_naiss = $request->lieu_naissance;
        $player->date_deb_car = $request->date_debut_carriere;
        $player->date_deb_equipe = $request->date_signature;
        $player->nationality = $request->nationalite;
        $player->poids = $request->poids;
        $player->taille = $request->taille;
        $player->dorsale_number = $request->dorsale;
        $player->position_id = $request->position;
        $player->descriptions = $request->descriptions;
        $player->save();

        return response([
            "message" => "Joueur modifié avec succés",
            "type" => "success",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/players/update/image",
     *    tags={"ADMINISTRATION__JOUEURS"},
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
     *                   property = "player_id",
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
            'player_id' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg|max:5120'
        ]);

        $img = player_image::find($request->id);

        if (!$img) {
            return response([
                "message" => "Joueur introuvable",
                "visibility" => false
            ], 200);
        }

        $image = $request->file('image');

        $completeFileName = $image->getClientOriginalName();
        $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
        $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

        $image = Image::make($image);

        $image->resize(500, 500);

        $image->save(public_path('storage/players/' . $compPic), 40);

        unlink(public_path('storage/players/' . $img->chemin));

        $img->chemin = $compPic;
        $img->player_id = $request->player_id;

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
     *     path="/admin/players/delete/image",
     *    tags={"ADMINISTRATION__JOUEURS"},
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

        $image = player_image::find($request->id);

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

        unlink(public_path('storage/players/' . $image->chemin));

        $image->delete();

        return response([
            "message" => "Image supprimée avec succés",
            "visibility" => true
        ], 200);
    }
}
