<?php
header('Content-Type: application/json');

// Generar datos personalizados
$data = [
    'labels' => ['Enero', 'Febrero', 'Marzo', 'Abril'], // Cambiar los valores aquí
    'datasets' => [
        [
            'label' => 'Ventas',
            'backgroundColor' => ['rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)'],
            'borderColor' => ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)'],
            'data' => [65, 59, 80, 81]
        ]
    ]
];

// Crear un elemento canvas para el gráfico
echo '<canvas id="myChart"></canvas>';

// Incluir la biblioteca Chart.js
echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';

// Inicializar el gráfico de barras
echo '<script>
    var ctx = document.getElementById("myChart").getContext("2d");
    var myChart = new Chart(ctx, {
        type: "bar",
        data: ' . json_encode($data) . ',
        options: {}
    });
</script>';
?>
