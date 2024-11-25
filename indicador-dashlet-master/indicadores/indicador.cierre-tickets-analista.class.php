<?php
require_once 'indicador.class.php';

use Combodo\iTop\Application\UI\Base\Component\Dashlet\DashletContainer;
use Combodo\iTop\Application\UI\Base\iUIBlock;
use Combodo\iTop\Application\UI\Base\UIBlock;

class IndicadorCierreTicketsAnalista extends Indicador
{

    protected $aDashletGroupBy;
    protected $sId;

    public function __construct($oModelReflection, $sId)
    {
        $this->aDashletGroupBy = new DashletGroupByPie($oModelReflection, $sId);
        $this->sId = $sId;
    }

    public function Render($oPage, $bEditMode = false, $aExtraParams = array())
    {
        // Configuración de las propiedades del dashlet
        $properties['title'] = 'Rendimiento de Cierre de Tickets por Analista';
        $properties['query'] = 'SELECT UserRequest WHERE status NOT IN ("rejected","closed")';
        $properties['group_by'] = 'agent_id';
        $properties['style'] = 'table';
        $properties['aggregation_function'] = 'avg';
        $properties['aggregation_attribute'] = 'time_spent';
        $properties['limit'] = '';
        $properties['order_by'] = 'function';
        $properties['order_direction'] = 'desc';

        // Configurar el dashlet con las propiedades
        $this->aDashletGroupBy->FromParams($properties);

        // Renderizar el dashlet
        $output = $this->aDashletGroupBy->Render($oPage, $bEditMode, $aExtraParams);

        // Insertar el script de JavaScript para manipular los datos después de cargar la página
        $script = <<<JS
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                var table = document.querySelector('#block_{$this->sId} table');
                if (table) {
                    var rows = table.querySelectorAll('tr');
                    rows.forEach(function(row) {
                        var cells = row.querySelectorAll('td');
                        cells.forEach(function(cell) {
                            var value = parseInt(cell.textContent.trim());
                            if (!isNaN(value)) {
                                // Convertir los segundos a horas, minutos y segundos
                                var days = Math.floor(value / 86400); // 86400 segundos en un día
                                var hours = Math.floor((value % 86400) / 3600);
                                var minutes = Math.floor((value % 3600) / 60);
                                var seconds = value % 60;
                                cell.textContent = days + 'd ' + hours + 'h ' + minutes + 'm ' + seconds + 's';
                            }
                        });
                    });
                }
            });
        </script>
        JS;

        // Agregar el script al final del HTML generado
        $output->AddHtml($script);

        return $output;
    }
}
