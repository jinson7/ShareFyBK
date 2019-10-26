<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Publication;

class PublicationController extends Controller
{
    
    public function __construct(){
        //$this->middleware('jwt');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/user/{id_user}/publications",
     *     tags={"publication"},
     *     summary="Dado un id de usuario existente, devuelve todas sus publicaciones",
     *     description="Dado un id de usuario existente, devuelve todas sus publicaciones",
     *     @OA\Response(
     *         response=200,
     *         description="Devuelve un json con la información de la publicación."
     *     ),
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="Valor del token_access",
     *         required=true
     *     )
     * )
      */
    public function list_publication_user($id_user){
        $publications = Publication::where('id_user', $id_user)->get();
        return response()->json([
            'value' => $publications
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/api/publication",
     *     tags={"publication"},
     *     summary="Es crea la publicació amb la informació enviada.",
     *     description="Es crea la publicació amb la informació enviada.",
     *     @OA\Response(
     *         response=200,
     *         description="Retorna un json amb el missatge 'operació correcta' "
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="El vídeo no té un format vàlid, format suportat .mp4"
     *     ),
     *     @OA\Parameter(
     *         name="id_user",
     *         in="query",
     *         description="string amb el id de l'usuari",
     *         required=true
     *     ),
     *     @OA\Parameter(
     *         name="game",
     *         in="query",
     *         description="string amb el valor del game",
     *         required=true
     *     ),
     *     @OA\Parameter(
     *         name="video",
     *         in="query",
     *         description="valor del video en el form",
     *         required=true
     *     ),
     *     @OA\Parameter(
     *         name="text",
     *         in="query",
     *         description="string amb el text de la publicació",
     *         required=true
     *     ),
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="Valor del token_access",
     *         required=true
     *     )
     * )
    */
    public function store(Request $request)
    {

        // agafar el video
        $file = $request->file('video');
        $ext = $file->getClientOriginalExtension();

        if ( $ext === 'mp4' ) {
            // crear publicació
            $publication = Publication::create([
                'id_user' => $request->id_user,
                'game' => $request->game,
                'text' => $request->text
            ]);

            // crear ruta per el clip
            $id_publication = str_pad($publication->id, 3, "0", STR_PAD_LEFT);
            $path = '/media/clips/'.$id_publication[0].'/'.$id_publication[1].'/'.$id_publication[2].'/';
        
            $date = now()->timestamp;
            $name_file = $date . '.' . $ext;
            $file->move(public_path($path), $name_file);
            $publication->video_path = $path.$name_file;
            $publication->save();
            return response()->json([
                'message' => 'Publicació creada correctament.'
            ], 200);
        }else{
            return response()->json([
                'error' => "El vídeo no té un format vàlid, format suportat .mp4"
            ], 401);
        }
        
    }

    /**
     * @OA\Get(
     *     path="/api/publication/{id}",
     *     tags={"publication"},
     *     summary="Dado un id de publicación existente, devuelve su información.",
     *     description="Dado un id de publicación existente, devuelve su información.",
     *     @OA\Response(
     *         response=200,
     *         description="Devuelve un json con la información de la publicación."
     *     ),
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="Valor del token_access",
     *         required=true
     *     )
     * )
      */
    public function show($id) {
        $publication = Publication::select('*')->where('id', $id)->first();
        return response()->json([
            'value' => $publication
        ], 200);
    }

    /** @OA\GET(
    *     path="/api/publication/{id}/edit",
    *     tags={"publication"},
    *     summary="Dado un id de publicación existente, edita dicha publicación.",
    *     description="Dado un id de publicación existente, y los campos a modificados guarda la dicha informacioón.",
    *     @OA\Response(
    *         response=200,
    *         description="Devuelve un json con el mensaje: 'Publicació editada correctament'."
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="Devuelve un json con el error: 'No existe la publicación a editar'."
    *     ),
    *     @OA\Parameter(
    *         name="game",
    *         in="query",
    *         description="Nombre del juego",
    *     ),
    *     @OA\Parameter(
    *         name="text",
    *         in="query",
    *         description="Texto descriptivo de la publicación",
    *     ),
    *     @OA\Parameter(
    *         name="token",
    *         in="query",
    *         description="Valor del token_access",
    *         required=true
    *     )
    * )
     */
    public function edit(Request $request, $id) {
        $publication = Publication::find($id);
        if ($publication !== null) {
            if ($request->game !== null) $publication->game = $request->game;
            if ($request->text !== null) $publication->text = $request->text;
            $publication->save();
            return response()->json([
                'message' => 'Publicació editada correctament.'
            ], 200);
        }
        return response()->json([
            'error' => 'No existe la publicación a editar.'
        ], 400);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}