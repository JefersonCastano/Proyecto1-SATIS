<?php
    require_once 'indicador.class.php';
    require_once __DIR__ . '/../utils/QueryHelper.php';
    use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;
    use Utils\QueryHelper;

    class IndicadorImplementacionRequerimientos extends Indicador {

        protected $aDashletGroupBy;

        public function __construct($oModelReflection, $sId) {
            $this->aDashletGroupBy = new DashletGroupByPie($oModelReflection, $sId);
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {

            // Agregar el archivo CSS
            $sCSSFile = utils::GetAbsoluteUrlModulesRoot() . 'indicador-dashlet-master/asset/css/dashlet-indicador.css';
            $oPage->add_linked_stylesheet($sCSSFile);
            
            //Obtener el porcentaje de implementación de requerimientos
            $iPercentage = $this->GetImplementationPercentage();
            
            //Crear el panel para el indicador
            $oPanel = PanelUIBlockFactory::MakeForInformation(Dict::S('UI:DashletIndicador:Prop-Type-Implementacion-Requerimientos'), '');

            //Agregar el porcentaje de implementación de requerimientos
            $oPanel->AddHtml("<p>" . Dict::S('UI:DashletIndicador:Prop-Type-Implementacion-Requerimientos:Percentage') . ":</p>");
            $oPanel->AddHtml('<p class="blue-bold-text">' . $iPercentage . '</p>');

            //Agregar el título del gráfico
            $oPanel->AddHtml('<p class="chart-title">' . Dict::S('UI:DashletIndicador:Prop-Type-Implementacion-Requerimientos:chart-title') . '</p>');
            
            //Agregar el gráfico
            $oPanel->AddHtml('<div id = "twoArcPieChart">');
            $oPanel->AddMainBlock($this->GetGraphic($oPage, $bEditMode, $aExtraParams));
            $oPanel->AddHtml('</div>');

            return $oPanel;
        }

        /**
         * Obtiene el porcentaje de implementación de requerimientos
         * @return string Porcentaje de implementación de requerimientos
         */
        private function GetImplementationPercentage() {
            //Obtener el número de requerimientos resueltos
            $sQuery = "SELECT UserRequest FROM UserRequest WHERE UserRequest .status IN ('Resolved','Closed')";
            $params = array('query_params' => array('count' => true));
            $oSet = QueryHelper::ExecuteQuery($sQuery, $params);
            $iResolved = $oSet->Count();

            //Obtener el número total de requerimientos
            $sQuery = "SELECT UserRequest FROM UserRequest WHERE UserRequest .status IN ('Resolved','Closed','new','Assigned','escalated_tto','waiting_for_approval','Pending','escalated_ttr','Approved','Rejected')";
            $params = array('query_params' => array('count' => true));
            $oSet = QueryHelper::ExecuteQuery($sQuery, $params);
            $iTotal = $oSet->Count();

            //Calcular el porcentaje de implementación de requerimientos
            $iPercentage = ($iTotal > 0) ? round(($iResolved / $iTotal) * 100, 1) : 0;

            return $iPercentage . '%';
        }

        /**
         * Obtiene el gráfico del indicador
         * @param $oPage Página
         * @param $bEditMode Modo de edición
         * @param $aExtraParams Parámetros adicionales
         * @return string Gráfico del indicador
         */
        private function GetGraphic($oPage, $bEditMode, $aExtraParams) {

            //Propiedades del dashlet
            $properties['query'] = 'SELECT UserRequest FROM UserRequest WHERE `status` IN ("waiting_for_approval", "Approved")';
            $properties['group_by'] = 'status';
            $properties['style'] = 'pie';
            $properties['aggregation_function'] = 'count';
            $properties['aggregation_attribute'] = '';
            $properties['limit'] = '';
            $properties['order_by'] = 'function';
            $properties['order_direction'] = '';

            //Configurar el dashlet con las propiedades
            $this->aDashletGroupBy->FromParams($properties);

            //Renderizar el dashlet
            return $this->aDashletGroupBy->Render($oPage, $bEditMode, $aExtraParams);
        }
    }