<?php
class IndicadorIrisOB extends Indicador {
    private static $instanceCount = 10; // Contador de instancias para garantizar IDs únicos

    public function Render()
    {
        self::$instanceCount++; // Incrementar el contador cada vez que se llame a Render()

        // Generar IDs únicos para los gráficos
        $barChartID = 'bar-chart-' . self::$instanceCount;
        $boxplotChartID = 'boxplot-chart-' . self::$instanceCount;

        // Captura el contenido en un buffer para devolverlo como cadena
        ob_start();

        // Obtener y procesar los datos del dataset Iris
        $dataset = $this->fetch_and_process_iris_dataset();
        $json_data = $dataset['json_data'];
        $boxplot_data = $dataset['boxplot_data'];

        // Incluir los enlaces a los scripts y estilos necesarios para C3.js y D3.js
        ?>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.20/c3.min.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/5.16.0/d3.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.20/c3.min.js"></script>

        <!-- Contenido dinámico que incluirá los gráficos -->
        <h1 style="text-align: center;">OB Distribución de Especies en el Dataset Iris</h1>
        <div id="<?php echo $barChartID; ?>"></div>

        <h2 style="text-align: center;">Boxplot de Características</h2>
        <div id="<?php echo $boxplotChartID; ?>"></div>

        <script>
            // Gráfico de Barras (Distribución de Especies)
            var barChartData = <?php echo json_encode($json_data); ?>;
            var barChart = c3.generate({
                bindto: '#<?php echo $barChartID; ?>', // Usar el ID único generado
                data: {
                    columns: barChartData,
                    type: 'bar',
                },
                axis: {
                    x: {
                        type: 'category',
                        categories: barChartData.map(function(item) { return item[0]; }) // Las especies (Setosa, Versicolor, etc.)
                    }
                },
                bar: {
                    width: {
                        ratio: 0.5
                    }
                }
            });

            // Gráfico de Boxplot (Características de las flores)
            var boxplotData = <?php echo json_encode($boxplot_data); ?>;
            var categories = ['Sepal Length', 'Sepal Width', 'Petal Length', 'Petal Width'];
            var boxplotChart = c3.generate({
                bindto: '#<?php echo $boxplotChartID; ?>', // Usar el ID único generado
                data: {
                    columns: [
                        ['Min'].concat(boxplotData.map(function(item) { return item[0]; })),
                        ['Q1'].concat(boxplotData.map(function(item) { return item[1]; })),
                        ['Median'].concat(boxplotData.map(function(item) { return item[2]; })),
                        ['Q3'].concat(boxplotData.map(function(item) { return item[3]; })),
                        ['Max'].concat(boxplotData.map(function(item) { return item[4]; }))
                    ],
                    type: 'line',
                    groups: [['Min', 'Q1', 'Median', 'Q3', 'Max']]
                },
                axis: {
                    x: {
                        type: 'category',
                        categories: categories // Nombres de las características
                    },
                    y: {
                        label: {
                            text: 'Values',
                            position: 'outer-middle'
                        }
                    }
                }
            });
        </script>
        <?php
        
        // Capturar el contenido del buffer y devolverlo como cadena
        return ob_get_clean();
    }

    private function fetch_and_process_iris_dataset() {
        // URL del archivo ZIP del dataset Iris
        $zip_url = 'https://archive.ics.uci.edu/static/public/53/iris.zip';
        $zip_file = 'iris.zip';
        $csv_file = 'iris.data';

        // Descargar y descomprimir el archivo ZIP
        file_put_contents($zip_file, file_get_contents($zip_url));

        $zip = new ZipArchive;
        if ($zip->open($zip_file) === TRUE) {
            $zip->extractTo('./'); // Extrae en el directorio actual
            $zip->close();
        }

        // Leer el archivo CSV
        $data = array_map('str_getcsv', file($csv_file));

        // Contar la cantidad de especies
        $species_count = [];
        foreach ($data as $row) {
            // Verificar si la fila tiene al menos 5 columnas
            if (count($row) < 5) {
                continue; // Saltar esta fila si no tiene suficientes columnas
            }

            $species = $row[4]; // Columna 5 contiene la especie
            if (!isset($species_count[$species])) {
                $species_count[$species] = 0;
            }
            $species_count[$species]++;
        }

        // Preparar datos para el gráfico de barras en formato JSON para usar en C3.js
        $json_data = [];
        foreach ($species_count as $species => $count) {
            $json_data[] = [$species, $count];
        }

        // Organizar las columnas de características (sepal_length, sepal_width, petal_length, petal_width)
        $columns = [[], [], [], []]; // 4 columnas vacías
        foreach ($data as $row) {
            if (count($row) >= 5) {
                $columns[0][] = (float)$row[0]; // sepal_length
                $columns[1][] = (float)$row[1]; // sepal_width
                $columns[2][] = (float)$row[2]; // petal_length
                $columns[3][] = (float)$row[3]; // petal_width
            }
        }

        // Calcular las estadísticas para el Boxplot
        $boxplot_data = array_map([$this, 'calculate_statistics'], $columns);

        return [
            'json_data' => $json_data,
            'boxplot_data' => $boxplot_data
        ];
    }

    private function calculate_statistics($column_data) {
        sort($column_data);
        $count = count($column_data);
        $min = $column_data[0];
        $max = $column_data[$count - 1];
        $median = $column_data[intval($count / 2)];
        $q1 = $column_data[intval($count / 4)];
        $q3 = $column_data[intval(3 * $count / 4)];
        return [$min, $q1, $median, $q3, $max];
    }
}
