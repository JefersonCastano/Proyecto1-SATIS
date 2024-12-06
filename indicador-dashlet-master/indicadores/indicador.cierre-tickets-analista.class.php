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
        // Agregar el archivo CSS
        $sCSSFile = utils::GetAbsoluteUrlModulesRoot() . 'indicador-dashlet-master/asset/css/dashlet-indicador.css';
        $oPage->add_linked_stylesheet($sCSSFile);
        
        // Propiedades del dashlet
        $properties['title'] = Dict::S('UI:DashletIndicador:Prop-Type-Cierre-Tickets-Analista');
        $properties['query'] = 'SELECT UserRequest WHERE status NOT IN ("rejected","closed")';
        $properties['group_by'] = 'agent_id';
        $properties['style'] = 'table';
        $properties['aggregation_function'] = 'avg';
        $properties['aggregation_attribute'] = 'time_spent';
        $properties['limit'] = '';
        $properties['order_by'] = 'function';
        $properties['order_direction'] = '';

        // Configurar el dashlet con las propiedades
        $this->aDashletGroupBy->FromParams($properties);

        // Renderizar el dashlet
        $output = $this->aDashletGroupBy->Render($oPage, $bEditMode, $aExtraParams);

        // Insertar el script de JavaScript para manipular los datos después de cargar la página
        $path = utils::GetAbsoluteUrlModulesRoot() . 'indicador-dashlet-master/asset/js/scripts.js';
        $script = <<<JS
            <script type="text/javascript" src={$path}></script>
            <script type="text/javascript">
                window.tableId = '{$this->sId}';
            </script>
        JS;

        // Agregar el script al final del HTML generado
        $output->AddHtml($script);

        return $output;
    }
}
