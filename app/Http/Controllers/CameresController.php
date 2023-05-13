<?php

namespace App\Http\Controllers;

use App\Models\Cameras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use Exception;


class CameresController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        return Cameras::all();
    }


    /**
     * Display a listing of the resource.
     */
    public function location($lat, $lon){
        try {
            $referer  = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' . urlencode($lat) . '&lon=' . urlencode($lon);
            $opts = array(
                'http' => array(
                    'header' => array("Referer: $referer\r\n")
                )
            );
            $context = stream_context_create($opts);
            $json = file_get_contents($referer, false, $context);
            $data = json_decode($json, true);
            $data['address']['status'] = "OK";
            
            return $data['address'];
        } catch (Exception $e) {
            return response()->json([
                "status" => "ERROR",
                "message" => $e->getMessage()
            ]);
        }
    }

    /**
     * Recupera la ubicació geogràfica a partir de les coordenades de latitud i longitud proporcionades. Retorna les dades d'adreça trobades en format JSON.
     */
    public function ubicacio_exacta($id_cam_seguretat){
        try {
            $camara = $this->show($id_cam_seguretat);
            $longitud = $camara->longitud;
            $latitud = $camara->latitud;

            $ubicacio = $this->location($latitud, $longitud);
            $camara->status = "OK";
            return $ubicacio;
        } catch (Exception $e) {
            return response()->json([
                "status" => "ERROR",
                "message" => $e->getMessage()
            ]);
        }
    }
    

    /**
     * Insereix dades a la base de dades. Les dades JSON han de contenir totes les columnes de la taula i els paràmetres han de coincidir amb els noms de les columnes.
     */
    public function store(Request $request){
        $existencia_camara = Cameras::find($request->id_cam_seguretat);
        
        if ($existencia_camara) {
            return response()->json([
                "status" => "ERROR",
                "message" => "El id de la càmara ja existeix"
            ]);
        }
        try {

            $camara = new Cameras;

            foreach ($request->all() as $property => $value) {
                $camara->$property = $value;
            }
            $camara->save();

            
            return response()->json([
                "status" => "OK",
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "ERROR",
                "message" => $e->getMessage()
            ]);
        }
    }


    /**
    * Filtra els registres de la taula càmeres segons els paràmetres proporcionats a la sol·licitud. Els paràmetres s'han d'enviar en format JSON i han de coincidir amb els noms de les columnes de la taula. Torna una llista de resultats que coincideixen amb els criteris de filtratge.
    */
    public function filter(Request $request){

        try {
            $jsonData = $request->json()->all();
            $tableName = 'cameras';
            $query = DB::table($tableName);
            foreach ($jsonData as $key => $value) {
                $query->where($key, $value);
            }
            $result = $query->get();
    
            if ($result->isEmpty()) {
                return "No hi ha cap resultat per les dades que estàs filtrant";
            }
    
            return $result;
        } catch (Exception $e) {
            return response()->json([
                "status" => "ERROR",
                "message" => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id_cam_seguretat){
        try {
            $camara = Cameras::find($id_cam_seguretat);
            
            if (!$camara) {
                return "El id de la càmara no existeix";
            }
            
            $camara->status = "OK";
            return $camara;
        } catch (Exception $e) {
            return response()->json([
                "status" => "ERROR",
                "message" => $e->getMessage()
            ]);
        }
    }


    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id_cam_seguretat){
        try {
            $camara = Cameras::find($id_cam_seguretat);

            if (!$camara) {
                return "El id de la càmara no existeix";
            }

            foreach ($request->all() as $property => $value) {
                if (!empty($value)) {
                    $camara->$property = $value;
                }
            }

            $camara->update();
            
            return response()->json([
                "status" => "OK"
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "ERROR",
                "message" => $e->getMessage()
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_cam_seguretat){

        $camara = Cameras::find($id_cam_seguretat);

        if(is_null($camara)){
            return response() -> json("Id de la camara no trobat", 404);
        }
        $camara -> delete();

        return response() -> noContent();
    }

    /**
     * funcio que Obté tota la informació de cadascuna de les rutes que tenim habilitades per fer consultes, en concret la ruta, els mètodes, els paràmetres i la descripció.
     */
    public function help(){
        $routes = Route::getRoutes();
        $detalls_rutes = [];

        foreach ($routes as $route) {
            if (strpos($route->uri, 'api') !== false) {
                

                $detalls_ruta = [
                    'uri' => $route->uri(),
                    'methods' => $route->methods(),
                    'parameters' => $route->parameterNames(),
                    'description' => '',
                ];

                switch ($route->uri) {
                    case 'api/cameres_seguretat':
                        if (in_array('GET', $route->methods())) {
                            $detalls_ruta['description'] = 'Obtenim tota la informació de cadascuna de les càmeres';
                        } elseif (in_array('POST', $route->methods())) {
                            $detalls_ruta['description'] = 'Insereix dades a la base de dades. Les dades JSON han de contenir totes les columnes de la taula i els paràmetres han de coincidir amb els noms de les columnes.';
                        }
                        break;

                    case 'api/cameres_seguretat/{cameres_seguretat}':
                        if (in_array('GET', $route->methods())) {
                            $detalls_ruta['description'] = 'Obtenim tota la informació de la càmera amb l\'ID que estem filtrant';
                        }
                        elseif (in_array('PUT', $route->methods())) {
                            $detalls_ruta['description'] = 'Actualitzem la informació de la càmera específica identificada amb l\'ID que estem filtrant';
                        }
                        elseif (in_array('DELETE', $route->methods())) {
                            $detalls_ruta['description'] = 'Esborrem la informació de la càmera específica identificada amb l\'ID que estem filtrant';
                        }
                        break;

                    case 'api/filter':
                        $detalls_ruta['description'] = "Filtra els registres de la taula càmeres segons els paràmetres proporcionats a la sol·licitud. Els paràmetres s'han d'enviar en format JSON i han de coincidir amb els noms de les columnes de la taula. Torna una llista de resultats que coincideixen amb els criteris de filtratge.";
                        break;

                    case 'api/location/{lat}/{lon}':
                        $detalls_ruta['description'] = "Recupera la ubicació geogràfica a partir de les coordenades de latitud i longitud proporcionades. Retorna les dades d'adreça trobades en format JSON.";
                        break;

                    case 'api/ubicacio_exacta/{id_cam_seguretat}':
                        $detalls_ruta['description'] = "A partir de l'ID de càmera de seguretat, aquesta funció recupera la ubicació geogràfica precisa de la càmera.";
                        break;
                    
                    case 'api/help':
                        $detalls_ruta['description'] = "Obté tota la informació de cadascuna de les rutes que tenim habilitades per fer consultes, en concret la ruta, els mètodes, els paràmetres i la descripció.";
                        break;
                }

                $detalls_rutes[] = $detalls_ruta;
            }
        }
        return $detalls_rutes;
    }
}
