<?php

namespace App\Http\Controllers;

use App\Http\Resources\Signature as SignatureResource;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SignatureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $signatures  = Signature::paginate(5);

        return SignatureResource::collection($signatures);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($user = Auth::user())
        {
            $inputs = $request->all();

            $inputs['user_id'] = $user->id;

            $validation = Signature::getValidation($inputs);

            if ($validation->fails())
            {
                return response()->json(['errors' => $validation->errors()], Response::HTTP_BAD_REQUEST);
            }

            $signature = Signature::createOne($inputs);

            return response()->json($signature, Response::HTTP_CREATED);
        }
        else
        {
            return response()->json(["error" => "403 Forbidden"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        SignatureResource::withoutWrapping();

        if($signature = Signature::find($id))
        {
            return new SignatureResource($signature);
        }
        else
        {
            return response()->json(["error" => "404 Not Found"], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($signature = Signature::find($id))
        {
            if ($signature->user_id == Auth::user()->id)
            {

            }
            else
            {
                return response()->json(['error' => '401 Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }
        }
        else
        {
            return response()->json(["error" => "404 Not Found"], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($signature = Signature::find($id))
        {
            $signature->delete();

            return response()->json(["error" => "204 No Content"], Response::HTTP_NO_CONTENT);
        }
        else
        {
            return response()->json(["error" => "404 Not Found"], Response::HTTP_NOT_FOUND);
        }
    }
}