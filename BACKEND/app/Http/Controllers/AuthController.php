<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/login",
     *     tags={"AUHTENTIFICATION"},
     *     summary="Authentifie l'utilisateur avant de se connecter",
     *     description="",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "email",
     *                   type="string",
     *                   example = "bgi@gmail.com"
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
    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email|max:255",
            "password" => "required"
        ]);

        $credentials = $request->only("email", "password");

        if (!Auth::attempt($credentials)) {
            return response([
                "message" => "Adresse mail ou mot de passe invalide",
                "visibility" => false
            ], 200);
        }

        /**
         * @var User $user
         */
        $user = Auth::user();
        $token = $user->createToken($user->name);

        return response([
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
            "telephone" => $user->telephone,
            "created_at" => $user->created_at,
            "update_at" => $user->update_at,
            "token" => $token->accessToken,
            "token_expires_at" => $token->token->expires_at,
            "visibility" => true,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     tags={"AUHTENTIFICATION"},
     *     summary="Deconnexion de l'utilisateur",
     *     description="",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "token",
     *                   type="string",
     *                   schema="Bearer",
     *                   example = "89er4186gjazuihhiZIOJreioiouEIOJIOZERF814879AE8FEA"
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
    public function logout()
    {
        /**
         * @var user $user
         */
        $user = Auth::user();
        $user->tokens->each(function ($token) {
            $token->delete();
        });
        $userToken = $user->token();
        $userToken->delete();
        return response([
            "message" => "Déconnexion effectuée"
        ], 200);
    }

}
