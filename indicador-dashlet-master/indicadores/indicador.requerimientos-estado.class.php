<?php
    require_once 'indicador.class.php';
    require_once __DIR__ . '/../utils/QueryHelper.php';
    use Utils\QueryHelper;
    use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;
    use Combodo\iTop\Application\UI\Base\Component\Dashlet\DashletContainer;

    class IndicadorRequerimientosEstado extends Indicador {

        protected $aDashletGroupBy;
        protected $sId;

        public function __construct($oModelReflection, $sId) {
            $this->aDashletGroupBy = new DashletGroupByPie($oModelReflection, $sId);
            $this->sId = $sId;
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {

            // Agregar el archivo CSS
            $sCSSFile = utils::GetAbsoluteUrlModulesRoot() . 'indicador-dashlet-master/asset/css/dashlet-indicador.css';
            $oPage->add_linked_stylesheet($sCSSFile);
            
            //Crear el panel para el indicador
            $oPanel = PanelUIBlockFactory::MakeForInformation(Dict::S('UI:DashletIndicador:Prop-Type-Requerimientos-Estado'), '');

            //Agregar gráfico de requerimientos por estado
            $oPanel->AddHtml('<p class="chart-title">' . Dict::S('UI:DashletIndicador:Prop-Type-Requerimientos-Estado:chart1-title') . '</p>');
            $oPanel->AddMainBlock($this->GetGraphicByState($oPage, $bEditMode, $aExtraParams));
        
            //Agregar gráfico de requerimientos reportados vs resueltos
            $oPanel->AddHtml('<p class="chart-title">' . Dict::S('UI:DashletIndicador:Prop-Type-Requerimientos-Estado:chart2-title') . '</p>');
            $oPanel->AddMainBlock($this->GetGraphicReportedVsResolved($oPage, $bEditMode, $aExtraParams));
            return $oPanel;
        }

        /**
         * Obtiene el gráfico de requerimientos por estado
         * @param $oPage Página
         * @param $bEditMode Modo de edición
         * @param $aExtraParams Parámetros extra
         * @return DashletContainer Gráfico de requerimientos por estado
         */
        private function GetGraphicByState($oPage, $bEditMode, $aExtraParams) {

            //Propiedades del gráfico
            $properties['query'] = 'SELECT UserRequest FROM UserRequest WHERE `status` IN ("new","Assigned","escalated_tto","waiting_for_approval","Pending","escalated_ttr","Approved","Rejected")';
            $properties['group_by'] = 'status';
            $properties['style'] = 'pie';
            $properties['aggregation_function'] = 'count';
            $properties['aggregation_attribute'] = '';
            $properties['limit'] = '';
            $properties['order_by'] = 'function';
            $properties['order_direction'] = '';

            //Configurar el dashlet con las propiedades
            $this->aDashletGroupBy->FromParams($properties);

            return $this->aDashletGroupBy->Render($oPage, $bEditMode, $aExtraParams);
        }

        /**
         * Obtiene el gráfico de requerimientos reportados vs resueltos
         * @param $oPage Página
         * @param $bEditMode Modo de edición
         * @param $aExtraParams Parámetros extra
         * @return DashletContainer Gráfico de requerimientos reportados vs resueltos
         */
        private function GetGraphicReportedVsResolved($oPage, $bEditMode, $aExtraParams) {

            //Obtener el número de requerimientos resueltos y cerrados
            $sQuery = "SELECT UserRequest FROM UserRequest WHERE UserRequest .status IN ('Resolved','Closed')";
            $params = array('query_params' => array('count' => true));
            $oSet = QueryHelper::ExecuteQuery($sQuery, $params);
            $resolved = $oSet->Count();

            //Obtener el número de requerimientos reportados
            $sQuery = "SELECT UserRequest FROM UserRequest WHERE UserRequest .status IN ('new','Assigned','escalated_tto','waiting_for_approval','Pending','escalated_ttr','Approved','Rejected')";
            $params = array('query_params' => array('count' => true));
            $oSet = QueryHelper::ExecuteQuery($sQuery, $params);
            $report = $oSet->Count();

            //Renderizar el gráfico
            return $this->RenderReportedVsResolved($oPage, $bEditMode, $aExtraParams, $resolved, $report);
        }

        /**
         * Renderiza el gráfico de requerimientos reportados vs resueltos
         * @param $oPage Página
         * @param $bEditMode Modo de edición
         * @param $aExtraParams Parámetros extra
         * @param $resolved Requerimientos resueltos
         * @param $report Requerimientos reportados
         * @return DashletContainer Gráfico de requerimientos reportados vs resueltos
         */
        public function RenderReportedVsResolved($oPage, $bEditMode = false, $aExtraParams = array(), $resolved, $report)
        {
            //Crear el contenedor del dashlet
            $oDashletContainer = PanelUIBlockFactory::MakeForInformation('','');
            //Identificador del bloque
            $sBlockId = 'block_fake_'.$this->sId.($bEditMode ? '_edit' : ''); 
            //Agregar contenedor donde se renderizará el gráfico
            $oDashletContainer->AddHtml("<div id=\"$sBlockId\" style=\"background-color:#fff;\"></div>");
            //Valores a mostrar en el gráfico
            $aDisplayValues = [
                                    ['label' => Dict::S('UI:DashletIndicador:Prop-Type-Requerimientos-Estado:chart2-legend-report'), 'value' => $report],
                                    ['label' => Dict::S('UI:DashletIndicador:Prop-Type-Requerimientos-Estado:chart2-legend-resolved'), 'value' => $resolved]
                            ];
            //Convertir valores a formato JSON
            $aColumns = array();
            $aNames = array();
            foreach ($aDisplayValues as $idx => $aValue) {
                $aColumns[] = array('series_'.$idx, (int)$aValue['value']);
                $aNames['series_'.$idx] = $aValue['label'];
            }
            $sJSColumns = json_encode($aColumns);
            $sJSNames = json_encode($aNames);

            //Script de la gráfica que se ejecutará cuando la página esté lista
            $oPage->add_ready_script(
                <<<EOF
                window.setTimeout(function() {
                var chart = c3.generate({
                    bindto: '#{$sBlockId}',
                    size: {
                        height: 255 
                    },
                    data: {
                        columns: $sJSColumns,
                        type: 'pie',
                        names: $sJSNames,
                        colors: {
                            series_0: '#e92e2e', 
                            series_1: '#1f77b4'  
                        }
                    },
                    legend: {
                    show: true,
                    position: 'right',
                    },
                    tooltip: {
                    format: {
                        value: function (value, ratio, id) { return value; }
                    }
                    }
                });}, 100);
                EOF
            );
            return $oDashletContainer;
        }
    }