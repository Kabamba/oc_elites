<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdministrateurController extends Controller
{

    /**
     * @OA\Get(
     *     path="/admin/admins/list",
     *     tags={"ADMINISTRATION__ADMINISTRATEUR"},
     *     summary="Liste des administrateurs",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function index()
    {
        return User::all();
    }

    /**
     * @OA\Post(
     *     path="/admin/admins/store",
     *     tags={"ADMINISTRATION__ADMINISTRATEUR"},
     *     summary="Ajouter un administrateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "name",
     *                   type="string",
     *                   example = "Mulamba"
     *                  ),
     *                @OA\Property(
     *                   property = "email",
     *                   type="string",
     *                   example = "bgi@gmail.com"
     *                  ),
     *                @OA\Property(
     *                   property = "telephone",
     *                   type="string",
     *                   example = "0852277379"
     *                  ),
     *                @OA\Property(
     *                   property = "password",
     *                   type="string",
     *                   example = "12345678"
     *                  ),
     *                @OA\Property(
     *                   property = "password_confirmation",
     *                   type="string",
     *                   example = "12345678"
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
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "telephone" => "required|unique:users",
            "password" => "required|confirmed",
        ]);

        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->telephone = $request->telephone;
        $user->password = Hash::make($request->password);
        $user->save();

        return response(["message" => "Administrateur enregistré avec succes"], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/admins/show/{id}",
     *     tags={"ADMINISTRATION__ADMINISTRATEUR"},
     *     summary="Renvoie les informations d'un administrateur par son ID",
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
        $user = User::where('id', $id)->first();

        if (!$user) {
            return response([
                "message" => "Administrateur introuvable",
                "type" => "danger",
                "visibility" => true
            ], 404);
        }

        return $user;
    }

    /**
     * @OA\Post(
     *     path="/admin/admins/update",
     *     tags={"ADMINISTRATION__ADMINISTRATEUR"},
     *     summary="Editer les informations d'un administrateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "id",
     *                   type="integer",
     *                   example = 10
     *                  ),
     *                @OA\Property(
     *                   property = "name",
     *                   type="string",
     *                   example = "Mulamba"
     *                  ),
     *                @OA\Property(
     *                   property = "email",
     *                   type="string",
     *                   example = "bgi@gmail.com"
     *                  ),
     *                @OA\Property(
     *                   property = "telephone",
     *                   type="string",
     *                   example = "0852277378"
     *                  ),
     *                @OA\Property(
     *                   property = "password",
     *                   type="string",
     *                   example = "12345678"
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
            "id" => "required",
            "name" => "required",
            "telephone" => "required",
            "email" => "required|email",
        ]);

        $user = User::where('id', $request->id)->first();

        if (!$user) {
            return response([
                "message" => "Administrateur introuvable",
                "type" => "danger",
                "visibility" => true
            ], 404);
        }

        $email = User::where('email', $request->email)
            ->where('id', '<>', $request->id)
            ->first();

        if ($email) {
            return response([
                "message" => "Adresse mail déjà utilisée",
                "type" => "danger",
                "visibility" => true
            ], 200);
        }

        $telephone = User::where('telephone', $request->telephone)
            ->where('id', '<>', $request->id)
            ->first();

        if ($telephone) {
            return response([
                "message" => "Numéro de téléphone déjà utilisé",
                "type" => "danger",
                "visibility" => true
            ], 200);
        }

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->name = $request->name;
        $user->telephone = $request->telephone;
        $user->email = $request->email;
        $user->save();

        return response([
            "message" => "Administrateur modifié avec succés",
            "type" => "success",
            "visibility" => true
        ], 200);
    }

}
