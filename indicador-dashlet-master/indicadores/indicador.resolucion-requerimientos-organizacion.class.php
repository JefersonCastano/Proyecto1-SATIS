<?php
require_once 'indicador.class.php';

use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;

class IndicadorResolucionRequerimientosOrganizacion extends Indicador
{

    protected $aDashletPie;
    protected $aDashletBar;
    protected $sIdBar;

    public function __construct($oModelReflection, $sId)
    {
        $this->aDashletPie = new DashletGroupByPie($oModelReflection, $sId);
        $this->aDashletBar = new DashletGroupByBars($oModelReflection, $sId . '_bar');
        $this->sIdBar = $sId . '_bar';
    }

    public function Render($oPage, $bEditMode = false, $aExtraParams = array())
    {
        // Crear el panel para el indicador
        $oPanel = PanelUIBlockFactory::MakeForInformation(Dict::S('UI:DashletIndicador:Prop-Type-Resolucion-Requerimientos-Organizacion'), '');

        // Agregar gráfico de cantidad de requerimientos
        $oPanel->AddHtml('<p class="chart-title">' . Dict::S('UI:DashletIndicador:Prop-Type-Resolucion-Requerimientos-Organizacion:chart1-title') . '</p>');
        $oPanel->AddMainBlock($this->GetGraphicAmount($oPage, $bEditMode, $aExtraParams));

        // Agregar gráfico de tiempo medio de resolución
        $oPanel->AddHtml('<p class="chart-title">' . Dict::S('UI:DashletIndicador:Prop-Type-Resolucion-Requerimientos-Organizacion:chart2-title') . '</p>');
        $oPanel->AddMainBlock($this->GetGraphicTime($oPage, $bEditMode, $aExtraParams));
        
        $path = utils::GetAbsoluteUrlModulesRoot() . 'indicador-dashlet-master/asset/js/scripts.js';
        
        // Insertar el script de JavaScript para manipular el gráfico
        $script = <<<JS
            <script type="text/javascript" src={$path}></script>
            <script type="text/javascript">
                window.chartId = '{$this->sIdBar}';
            </script>
        JS;

        //Agregar el script al final del HTML generado
        $oPanel->AddHtml($script);
        return $oPanel; 
    }

    /**
     * Obtiene el gráfico de cantidad de requerimientos
     * @param $oPage Página
     * @param $bEditMode Modo de edición
     * @param $aExtraParams Parámetros extra
     * @return DashletContainer Gráfico de cantidad de requerimientos
     */
    private function GetGraphicAmount($oPage, $bEditMode, $aExtraParams) 
    {
        // Propiedades del dashlet
        $properties['query'] = 'SELECT UserRequest WHERE status IN ("rejected","closed")';
        $properties['group_by'] = 'org_id';
        $properties['style'] = 'pie';
        $properties['aggregation_function'] = 'count';
        $properties['aggregation_attribute'] = '';
        $properties['limit'] = '';
        $properties['order_by'] = 'function';
        $properties['order_direction'] = '';

        // Configurar el dashlet con las propiedades
        $this->aDashletPie->FromParams($properties);

        // Renderizar el dashlet
        return $this->aDashletPie->Render($oPage, $bEditMode, $aExtraParams);
    }

    /**
     * Obtiene el gráfico de tiempo medio de resolución
     * @param $oPage Página
     * @param $bEditMode Modo de edición
     * @param $aExtraParams Parámetros extra
     * @return DashletContainer Gráfico de tiempo medio de resolución
     */
    private function GetGraphicTime($oPage, $bEditMode, $aExtraParams) 
    {
        // Propiedades del dashlet
        $properties['query'] = 'SELECT UserRequest WHERE status IN ("rejected","closed")';
        $properties['group_by'] = 'org_id';
        $properties['style'] = 'bars';
        $properties['aggregation_function'] = 'avg';
        $properties['aggregation_attribute'] = 'time_spent';
        $properties['limit'] = '';
        $properties['order_by'] = 'function';
        $properties['order_direction'] = '';

        // Configurar el dashlet con las propiedades
        $this->aDashletBar->FromParams($properties);

        // Renderizar el dashlet
        return $this->aDashletBar->Render($oPage, $bEditMode, $aExtraParams);
    }
}
