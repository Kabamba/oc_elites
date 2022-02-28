<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\image_post;
use App\Models\post;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class PostController extends Controller
{

    /**
     * @OA\Get(
     *     path="/admin/posts/list",
     *    tags={"ADMINISTRATION__EVENEMENTS"},
     *     summary="Liste des évènements",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function index()
    {
        $posts =  post::all();

        return PostResource::collection($posts);
    }

    /**
     * @OA\Post(
     *     path="/admin/posts/store",
     *    tags={"ADMINISTRATION__EVENEMENTS"},
     *     summary="Ajouter un évènement",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "titre",
     *                   type="string",
     *                   example = "Evangelisation"
     *                  ),
     *                @OA\Property(
     *                   property = "descriptions",
     *                   type="string",
     *                   example = "Andy Kasanda"
     *                  ),
     *                @OA\Property(
     *                   property = "user_id",
     *                   type="integer",
     *                   example = 1
     *                  ),
     *                @OA\Property(
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
            'titre' => 'required',
            "descriptions" => 'required|string',
            'user_id' => 'required',
            'cover' => 'required|mimes:jpeg,png,jpg|max:5120',
            'images.*' => 'nullable|mimes:jpeg,png,jpg|max:5120'
        ]);

        $post = new post();

        $post->titre = $request->titre;
        $post->descriptions = $request->descriptions;
        $post->user_id = $request->user_id;
        $post->save();

        /** Ajout de la photo de couverture */

        $cover = $request->file('cover');

        $completeFileName = $cover->getClientOriginalName();

        $completeFileName = $cover->getClientOriginalName();
        $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
        $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

        $img = Image::make($cover);

        $img->resize(500, 500);

        $img->save(public_path('storage/posts/' . $compPic), 40);

        $image_post = new image_post();

        $image_post->chemin = $compPic;
        $image_post->post_id = $post->id;
        $image_post->covert = 1;
        $image_post->save();

        /** Ajout de la photo de couverture */

        $images = $request->file('images');

        if (!empty($images)) {

            for ($i = 0; $i < count($images); $i++) {
                $completeFileName = $images[$i]->getClientOriginalName();

                $completeFileName = $images[$i]->getClientOriginalName();
                $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
                $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

                $img = Image::make($images[$i]);

                $img->resize(500, 500);

                $img->save(public_path('storage/posts/' . $compPic), 40);

                $image_post = new image_post();

                $image_post->chemin = $compPic;
                $image_post->post_id = $post->id;
                $image_post->covert = 0;
                $image_post->save();
            }
        }

        return response([
            "message" => "Evènement ajouté avec succés",
            'visibility' => true
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/posts/show/{id}",
     *    tags={"ADMINISTRATION__EVENEMENTS"},
     *     summary="Renvoie les informations d'un évènement par son ID",
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
        $post = post::find($id);

        if (!$post) {
            return response([
                "message" => "Evènement introuvable",
                'visibility' => false
            ], 404);
        }

        return PostResource::make($post);
    }

    /**
     * @OA\Get(
     *     path="/admin/posts/delete/{id}",
     *    tags={"ADMINISTRATION__EVENEMENTS"},
     *     summary="Supprime un évènement par son ID",
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
        $post = post::where('id', $id)->first();

        if (!$post) {
            return response([
                "message" => "Evènement introuvable",
                "visibility" => false
            ], 404);
        }

        $post->images()->delete();
        $post->delete();
      

        return response([
            "message" => "Evènement supprimé",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/posts/update",
     *    tags={"ADMINISTRATION__EVENEMENTS"},
     *     summary="Editer les informations d'un évènement",
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
     *                   property = "titre",
     *                   type="string",
     *                   example = "Evangelisation"
     *                  ),
     *                @OA\Property(
     *                   property = "descriptions",
     *                   type="string",
     *                   example = "Une description"
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
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'titre' => 'required',
            'descriptions' => 'required|string',
        ]);

        $post = post::find($request->id);

        if (!$post) {
            return response([
                "message" => "Evènement introuvable",
                "visibility" => false
            ], 200);
        }

        $post->titre = $request->titre;
        $post->descriptions = $request->descriptions;
        $post->save();

        return response([
            "message" => "Evènement modifié avec succés",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/posts/delete/image",
     *    tags={"ADMINISTRATION__EVENEMENTS"},
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

        $image = image_post::find($request->id);

        if (!$image) {
            return response([
                "message" => "Evènement introuvable",
                "visibility" => false
            ], 200);
        }

        if ($image->covert == 1) {
            return response([
                "message" => "Impossible de supprimer l'image de couverture",
                "visibility" => false
            ], 200);
        }

        unlink(public_path('storage/posts/' . $image->chemin));

        $image->delete();

        return response([
            "message" => "Image supprimée avec succés",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/posts/update/image",
     *    tags={"ADMINISTRATION__EVENEMENTS"},
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
     *                   property = "post_id",
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
            'post_id' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg|max:5120'
        ]);

        $img = image_post::find($request->id);

        if (!$img) {
            return response([
                "message" => "Evènement introuvable",
                "visibility" => false
            ], 200);
        }

        $image = $request->file('image');

        $completeFileName = $image->getClientOriginalName();
        $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
        $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.webp';

        $image = Image::make($image);

        $image->resize(500, 500);

        $image->save(public_path('storage/posts/' . $compPic), 40);

        unlink(public_path('storage/posts/' . $img->chemin));

        $img->chemin = $compPic;
        $img->post_id = $request->post_id;

        $img->save();

        return response([
            "message" => "Image modifiée avec succés",
            "visibility" => true
        ], 200);
    }
}
