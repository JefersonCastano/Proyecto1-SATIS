function handleSelectChange(selectElement) {
    var selectedValue = selectElement.value;
    document.getElementById('selected-value').innerText = selectedValue;
}

var chart = c3.generate({
    bindto: '#my_chart_block_IndicadoresOverView_ID_row0_col0_21',
    data: {
        columns: [
            ['data1', 30, 200, 100],
            ['data2', 50, 20, 10]
        ],
        type: 'bar'
    },
    color: {
        pattern: ['#ff0000', '#00ff00', '#0000ff'] // Colores personalizados
    }
});
