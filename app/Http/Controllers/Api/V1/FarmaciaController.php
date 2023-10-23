<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Services\FarmaciaService;

/**
 * @OA\Info(
 *      version="1.0",
 *      title="Documentación de API",
 *      description="Funciones correspondientes a API",
 *      @OA\Contact(
 *          email="agustin.caputo@gmail.com"
 *      ),
 * )
 */

class FarmaciaController extends Controller
{
    private FarmaciaService $farmaciaService;

    public function __construct(FarmaciaService $farmaciaService) {
        $this->farmaciaService = $farmaciaService;
    }

    /**
     * Se obtiene el listado paginado de farmacias.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     *
     * @OA\Get(
     *     path="/api/v1/farmacias",
     *     tags={"Farmacias"},
     *     summary="Listado de farmacias",
     *     @OA\Response(
     *         response=200,
     *         description="Mostrar el listado de farmacias."
     *     ),
     *     @OA\Response(
     *      response=422,
     *      description="Error en los parámetros enviados"
     *     ),
     *     @OA\Response(
     *      response=400,
     *      description="Error de request"
     *     ),
     *     @OA\Parameter(
     *          name="lat",
     *          in="query",
     *          description="Latitud de punto geográfico a consultar farmacia más cercana entre 0 y 90 (se requiere haber enviado longitud)",
     *          required=false,
     *     ),
     *     @OA\Parameter(
     *          name="lon",
     *          in="query",
     *          description="Longitud de punto geográfico a consultar farmacia más cercana entre 0 y 180 (se requiere haber enviado latitud)",
     *          required=false,
     *     )
     * )
     */

    public function index(Request $request)
    {
        try {
            if($request['lon'] && !$request['lat'])
                throw new \Exception("Falta enviar el dato de la latitud", 1);

            if($request['lat'] && !$request['lon'])
                throw new \Exception("Falta enviar el dato de la longitud", 1);

            if($request['lat'] && $request['lon'])
            {
                $validated = $request->validate([
                    'lat' => 'numeric|gte:0|lte:90',
                    'lon' => 'numeric|gte:0|lte:180'
                ]);
                return response($this->farmaciaService->getNearest($request['lat'],$request['lon']),200);
            }
            return response($this->farmaciaService->index($request->all()),200);
        } catch (ValidationException $e) {
            return response($e->errors(),422);
        } catch (Exception $e) {
            return response($e->getMessage(),400);
        }
    }

    /**
     * Creación de nueva farmacia.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @OA\Post(
     *    path="/api/v1/farmacias",
     *    summary="Crear nueva farmacia",
     *    description="Crear una nueva farmacia con sus datos geográficos",
     *    tags={"Farmacias"},
     *    @OA\RequestBody(
     *      required=true,
     *      description="Datos de la farmacia",
     *      @OA\JsonContent(
     *          required={"nombre","dirección","latitud","longitud"},
     *          @OA\Property(property="nombre", type="string", format="text", example="Farmacindy"),
     *          @OA\Property(property="direccion", type="string", format="text", example="Calle falsa 123"),
     *          @OA\Property(property="latitud", type="number", format="number", example="29"),
     *          @OA\Property(property="longitud", type="number", format="number", example="29"),
     *      ),
     *    ),
     *   @OA\Response(
     *      response=201,
     *      description="Farmciada creada correctamente"
     *    ),
     *    @OA\Response(
     *      response=422,
     *      description="Error en los parámetros enviados"
     *    ),
     *    @OA\Response(
     *      response=400,
     *      description="Error de request"
     *    )
     * )
     */
    public function store(Request $request)
    {

        try {
            $farmacia = $this->farmaciaService->store($request->all());

            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/creation.log'),
              ])->info("{Farmacia: {$farmacia} }");

            return response($farmacia,201);

        } catch (ValidationException $e) {
            $errorLog = json_encode(["error" => $e->errors(), "info" => $request->all()]);

            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/error.log'),
              ])->error($errorLog);
            return response($e->errors(),422);
        } catch (Exception $e) {
            $errorLog = json_encode(["error" => $e->getMessage(), "info" => $request->all()]);

            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/error.log'),
              ])->info($errorLog);
            return response($e->getMessage(),400);
        }
    }

        /**
     * Actualización de nueva farmacia.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @OA\Put(
     *    path="/api/v1/farmacias/{id}",
     *    summary="Actualizar farmacia",
     *    description="Actualizar una nueva farmacia con sus datos geográficos",
     *    tags={"Farmacias"},
     *    @OA\RequestBody(
     *      required=true,
     *      description="Datos de la farmacia",
     *      @OA\JsonContent(
     *          required={"nombre","dirección","latitud","longitud"},
     *          @OA\Property(property="nombre", type="string", format="text", example="Farmacindy"),
     *          @OA\Property(property="direccion", type="string", format="text", example="Calle falsa 123"),
     *          @OA\Property(property="latitud", type="number", format="number", example="29"),
     *          @OA\Property(property="longitud", type="number", format="number", example="29"),
     *      ),
     *    ),
     *   @OA\Response(
     *      response=200,
     *      description="Farmciada creada correctamente"
     *    ),
     *    @OA\Response(
     *      response=422,
     *      description="Error en los parámetros enviados"
     *    ),
     *    @OA\Response(
     *      response=400,
     *      description="Error de request"
     *    )
     * )
     */
    public function update(Request $request, int $id)
    {

        try {
            $farmacia = $this->farmaciaService->update($request->all(),$id);

            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/creation.log'),
              ])->info("{Farmacia: {$farmacia} }");

            return response($farmacia,200);

        } catch (ValidationException $e) {
            $errorLog = json_encode(["error" => $e->errors(), "info" => $request->all()]);

            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/error.log'),
              ])->error($errorLog);
            return response($e->errors(),422);
        } catch (Exception $e) {
            $errorLog = json_encode(["error" => $e->getMessage(), "info" => $request->all()]);

            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/error.log'),
              ])->info($errorLog);
            return response($e->getMessage(),400);
        }
    }

    /**
     * Mostrar una farmacia especificada.
     *
     * @param  int $farmacia
     * @return \Illuminate\Http\Response
     *      * @OA\Get(
     *     path="/api/v1/farmacias/{id}",
     *     tags={"Farmacias"},
     *     summary="Se obtiene una farmacia",
     *     @OA\Response(
     *         response=200,
     *         description="Detalle de farmacia."
     *     ),
     *      @OA\Response(
     *      response=422,
     *      description="Error en los parámetros enviados"
     *     ),
     *     @OA\Response(
     *      response=400,
     *      description="Error de request"
     *     ),
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de la farmacia",
     *          required=true,
     *     )
     * )
     */
    public function show(int $farmacia)
    {
        try {
            if( !is_numeric($farmacia) || $farmacia < 0)
                throw new ValidationException("Error en el id de la farmacia", 1);

            return response($this->farmaciaService->show($farmacia),200);
        } catch (ValidationException $e) {
            return response($e->errors(),422);
        } catch (Exception $e) {
            return response($e->getMessage(),400);
        }
    }

        /**
     *
     * @param  int $lat
     * @param  int  $lon
     *
     * @OA\Get(
     *     path="/api/v1/farmacias/distancias/{lat}/{lon}",
     *     tags={"Farmacias"},
     *     summary="Se obtiene la farmacia más cercana desde un punto geográfico",
     *     @OA\Response(
     *         response=200,
     *         description="Farmacia con la menor distancia al punto especificado."
     *     ),
     *     @OA\Response(
     *      response=422,
     *      description="Error en los parámetros enviados"
     *     ),
     *     @OA\Response(
     *      response=400,
     *      description="Error de request"
     *     ),
     *     @OA\Parameter(
     *          name="lat",
     *          in="path",
     *          description="Latitud del punto geográfico a consultar",
     *          required=true,
     *     ),
     *     @OA\Parameter(
     *          name="lon",
     *          in="path",
     *          description="Longitud del punto geográfico a consultar",
     *          required=true,
     *     )
     * )
     *
     * @return \Illuminate\Http\Response
     */

     public function getNearest(int $lat, int $lon){
        try {
            return response()->json($this->farmaciaService->getNearest($lat,$lon),200);
        } catch (ValidationException $e) {
            return response()->json($e->errors(),422);
        }
    }

    /**
     *
     * @param  int $lat
     * @param  int  $lon
     *
     * @OA\Get(
     *     path="/api/v1/farmacias/distances/{lat}/{lon}",
     *     tags={"Farmacias"},
     *     summary="Se obtiene las distancias a las farmacias desde un punto geográfico",
     *     @OA\Response(
     *         response=200,
     *         description="Listado de farmacias con la distancia al punto especificado."
     *     ),
     *     @OA\Response(
     *      response=422,
     *      description="Error en los parámetros enviados"
     *     ),
     *     @OA\Response(
     *      response=400,
     *      description="Error de request"
     *     ),
     *     @OA\Parameter(
     *          name="lat",
     *          in="path",
     *          description="Latitud del punto geográfico a consultar",
     *          required=true,
     *     ),
     *     @OA\Parameter(
     *          name="lon",
     *          in="path",
     *          description="Longitud del punto geográfico a consultar",
     *          required=true,
     *     )
     * )
     *
     * @return \Illuminate\Http\Response
     */

    public function getDistances(int $lat, int $lon){
        try {
            return response()->json($this->farmaciaService->getDistances($lat,$lon),200);
        } catch (ValidationException $e) {
            return response()->json($e->errors(),422);
        }
    }
}
