<?php
    require_once 'indicador.class.php';
    require_once __DIR__ . '/../utils/QueryHelper.php';
    use Utils\QueryHelper;
    use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;
    use Combodo\iTop\Application\UI\Base\Component\Dashlet\DashletContainer;

    class IndicadorRequerimientosEstado extends Indicador {

        protected $aDashletGroupByAll;
        protected $sId;

        public function __construct($oModelReflection, $sId) {
            $this->aDashletGroupByAll = new DashletGroupByPie($oModelReflection, $sId);
            $this->sId = $sId . '2';
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {

            $sCSSFile = utils::GetAbsoluteUrlModulesRoot() . 'indicador-dashlet-master/asset/css/dashlet-indicador.css';
            $oPage->add_linked_stylesheet($sCSSFile);

            $oPanel = PanelUIBlockFactory::MakeForInformation(Dict::S('UI:DashletIndicador:Prop-Type-Requerimientos-Estado'), '');

            $oPanel->AddHtml('<p class="chart-title">' . Dict::S('UI:DashletIndicador:Prop-Type-Requerimientos-Estado:chart1-title') . '</p>');
            $oPanel->AddMainBlock($this->GetGraphicAll($oPage, $bEditMode, $aExtraParams));
        
            $oPanel->AddHtml('<p class="chart-title">' . Dict::S('UI:DashletIndicador:Prop-Type-Requerimientos-Estado:chart2-title') . '</p>');
            $oPanel->AddSubBlock($this->GetGraphicReportedVsResolved($oPage, $bEditMode, $aExtraParams));
            
            return $oPanel;
        }

        private function GetGraphicAll($oPage, $bEditMode, $aExtraParams) {

            $properties['query'] = 'SELECT UserRequest FROM UserRequest WHERE `status` IN ("new","Assigned","escalated_tto","waiting_for_approval","Pending","escalated_ttr","Approved","Rejected")';
            $properties['group_by'] = 'status';
            $properties['style'] = 'pie';
            $properties['aggregation_function'] = 'count';
            $properties['aggregation_attribute'] = '';
            $properties['limit'] = '';
            $properties['order_by'] = '';
            $properties['order_direction'] = '';

            $this->aDashletGroupByAll->FromParams($properties);

            return $this->aDashletGroupByAll->Render($oPage, $bEditMode, $aExtraParams);
        }

        private function GetGraphicReportedVsResolved($oPage, $bEditMode, $aExtraParams) {

            $sQuery = "SELECT UserRequest FROM UserRequest WHERE UserRequest .status IN ('Resolved','Closed')";
            $params = array('query_params' => array('count' => true));
            $oSet = QueryHelper::ExecuteQuery($sQuery, $params);
            $resolved = $oSet->Count();

            $sQuery = "SELECT UserRequest FROM UserRequest WHERE UserRequest .status IN ('new','Assigned','escalated_tto','waiting_for_approval','Pending','escalated_ttr','Approved','Rejected')";
            $params = array('query_params' => array('count' => true));
            $oSet = QueryHelper::ExecuteQuery($sQuery, $params);
            $requests = $oSet->Count();

            return $this->RenderReportedVsResolved($oPage, $bEditMode, $aExtraParams, $resolved, $requests);
        }

        public function RenderReportedVsResolved($oPage, $bEditMode = false, $aExtraParams = array(), $resolved, $requests)
        {
            $oDashletContainer = new DashletContainer(null, ['dashlet-content']);
            $sBlockId = 'block_fake_'.$this->sId.($bEditMode ? '_edit' : ''); 
            $oDashletContainer->AddHtml("<div style=\"background-color:#fff;padding:0.25em;\"><div id=\"$sBlockId\" style=\"background-color:#fff;\"></div></div>");
            $aDisplayValues = [
                                    ['label' => 'Reportados', 'value' => $requests],
                                    ['label' => 'Resueltos', 'value' => $resolved],
                            ];

            $aColumns = array();
            $aNames = array();
            foreach ($aDisplayValues as $idx => $aValue) {
                $aColumns[] = array('series_'.$idx, (int)$aValue['value']);
                $aNames['series_'.$idx] = $aValue['label'];
            }
            $sJSColumns = json_encode($aColumns);
            $sJSNames = json_encode($aNames);
            $oPage->add_ready_script(
                <<<EOF
    window.setTimeout(function() {
    var chart = c3.generate({
        bindto: '#{$sBlockId}',
        data: {
            columns: $sJSColumns,
            type: 'pie',
            names: $sJSNames,
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