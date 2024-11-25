<?php
require_once 'indicador.class.php';

use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;

class IndicadorResolucionRequerimientosOrganizacion extends Indicador
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

        // Propiedades del dashlet
        $properties['title'] = 'Tiempo de Resolución por Organización';
        $properties['query'] = 'SELECT UserRequest WHERE status IN ("rejected","closed")';
        $properties['group_by'] = 'org_id';
        $properties['style'] = 'bars';
        $properties['aggregation_function'] = 'avg';
        $properties['aggregation_attribute'] = 'time_spent';
        $properties['limit'] = '';
        $properties['order_by'] = 'function';
        $properties['order_direction'] = '';

        // Configuración del dashlet
        $this->aDashletGroupBy->FromParams($properties);

        // Renderizar el gráfico
        $output = $this->aDashletGroupBy->Render($oPage, $bEditMode, $aExtraParams);

        // Insertar el script de JavaScript para manipular el gráfico
        $script = <<<JS
        <script type="text/javascript">
            function convertTicks() {
                var ticks = document.querySelectorAll('#my_chart_block_{$this->sId}2 .c3-axis-y .tick text tspan');
                
                if (ticks.length === 0) {
                    return;
                } else {
                    ticks.forEach(function(tick) {
                        var seconds = tick.textContent;
                        if (!isNaN(seconds) && seconds[-1] !== 'h') {
                            var hours = Math.floor(seconds / 3600); 
                            tick.textContent = hours + 'h '; 
                        }
                    });
                }
            }

            document.addEventListener("DOMContentLoaded", function() {
                setTimeout(convertTicks, 3000); 

                window.addEventListener('resize', function() {
                    setTimeout(convertTicks, 500);
                });
            });
        </script>
        JS;

        // Agregar el script al final del HTML generado
        $output->AddHtml($script);

        return $output;
    }
}
