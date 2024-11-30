function convertTicks(chartId) {
    var ticks = document.querySelectorAll('#block_' + chartId + ' .c3-axis-y .tick text tspan');
    
    if (ticks.length === 0) {
        return;
    } else {
        ticks.forEach(function(tick) {
            var seconds = parseInt(tick.textContent, 10);
            if (!isNaN(seconds) && !tick.textContent.includes('h')) {
                var hours = Math.floor(seconds / 3600); 
                tick.textContent = hours + 'h '; 
            }
        });
    }
}

function convertTableValues(tableId) {
    const table = document.querySelector('#block_' + tableId + ' table');
    if (table) {
        const rows = table.querySelectorAll('tr');
        rows.forEach(function(row) {
            const cells = row.querySelectorAll('td');
            if (cells.length === 0) {
                return;
            }

            const cell = cells.item(1);

            if (isNaN(cell.textContent) || cell.textContent === '') {
                return;
            }

            const value = parseInt(cell.textContent.trim());

            if (!isNaN(value)) {
                // Convertir los segundos a días, horas, minutos y segundos
                var days = Math.floor(value / 86400); // 86400 segundos en un día
                var hours = Math.floor((value % 86400) / 3600);
                var minutes = Math.floor((value % 3600) / 60);
                var seconds = value % 60;
                cell.textContent = days + 'd ' + hours + 'h ' + minutes + 'm ' + seconds + 's';
            }
        });
    }
}

document.addEventListener("DOMContentLoaded", function() {
    // Convertir los ticks del gráfico
    setTimeout(function() {
        convertTicks(window.chartId);
        convertTicks(window.timeChartId);
    }, 5000); 

    window.addEventListener('resize', function() {
        setTimeout(function() {
            convertTicks(window.chartId);
            convertTicks(window.timeChartId);
        }, 500);
    });

    // Convertir los valores de la tabla
    convertTableValues(window.tableId);
});