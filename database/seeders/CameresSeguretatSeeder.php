<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;

class CameresSeguretatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->importCsv();
    }


    public function importCsv()
    {
        //ahora vamos a crear una funcion para poder hacer la peticion
        $URL_Peticion = "https://opendata-ajuntament.barcelona.cat/resources/bcn/Infraestructures/Infraestructures_Inventari_CameresSeguretat.csv";
        //creamos un objeto de tipo curl
        $curl = curl_init();
        //configuramos la peticion
        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL_Peticion,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache"
            ),
        ));
        //ejecutamos la peticion
        $respuesta = curl_exec($curl);

        //guardamos la respuesta en un archivo csv  en la ruta ./database/csv_data/
        // mkdir(base_path('storage\database\csv_data'), 0777, true);

        file_put_contents(base_path('storage\database\csv_data\tmp.csv'), $respuesta);



        // file_get_contents(base_path('./database/csv_data/tmp.csv'), $respuesta);

        //mostar la respuesta por consola
        echo $respuesta;


        // Ruta del archivo CSV
        $csvFile = storage_path('database\csv_data\tmp.csv');
        //eliminamos la primera fila del archivo csv


        // Crear un objeto CSV Reader
        $csv = Reader::createFromPath($csvFile, 'r');
        $csv->setHeaderOffset(0);

        //truncate table
        DB::table('cameras')->truncate();


        // Iterar sobre las filas del archivo CSV
        foreach ($csv->getRecords() as $record) {
            // Insertar la fila en la tabla, usando los nombres de las columnas como claves y si el valor es '' o null, usar null
            //recorre el array y si el valor es '' o null, usa null
            foreach ($record as $key => $value) {
                if ($value == '' || $value == null) {
                    $record[$key] = null;
                }
            }
            DB::table('cameras')->insert($record);
        }
        //eliminamos el archivo csv temporal 
        unlink($csvFile);

        //cerramos la peticion
        curl_close($curl);
    }
}
