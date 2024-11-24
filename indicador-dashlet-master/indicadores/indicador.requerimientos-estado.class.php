<?php
    require_once 'indicador.class.php';
    require_once __DIR__ . '/../utils/QueryHelper.php';
    use Utils\QueryHelper;
    use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;

    class IndicadorRequerimientosEstado extends Indicador {

        protected $aDashletGroupBy;

        public function __construct($oModelReflection, $sId) {
            $this->aDashletGroupBy = new DashletGroupByPie($oModelReflection, $sId);
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {
            $oPanel = PanelUIBlockFactory::MakeNeutral('Requerimientos por Estado', '');
            $oPanel->AddMainBlock($this->GetGraphic($oPage, $bEditMode, $aExtraParams));
            $oPanel->AddHtml("<p>" . $this->GetReportedVsResolved() . "</p>");

            return $oPanel;
        }

        private function GetReportedVsResolved() {
            $sQuery = "SELECT UserRequest FROM UserRequest WHERE UserRequest .status IN ('Resolved','Closed')";
            $params = array('query_params' => array('count' => true));
            $oSet = QueryHelper::ExecuteQuery($sQuery, $params);
            $resolved = $oSet->Count();

            $sQuery = "SELECT UserRequest FROM UserRequest";
            $oSet = QueryHelper::ExecuteQuery($sQuery);
            $requests = $oSet->Count();

            return "Resueltos: ". $resolved . " Casos Reportados " . $requests . " Porcentaje de Resolución: " . round(($resolved / $requests) * 100, 2) . "%";
        }

        private function GetGraphic($oPage, $bEditMode, $aExtraParams) {

            $properties['title'] = 'Cantidad de Requerimientos por Estado';
            $properties['query'] = 'SELECT UserRequest FROM UserRequest WHERE `status` IN ("new","Assigned","escalated_tto","waiting_for_approval","Pending","escalated_ttr","Approved","Rejected")';
            $properties['group_by'] = 'status';
            $properties['style'] = 'pie';
            $properties['aggregation_function'] = 'count';
            $properties['aggregation_attribute'] = '';
            $properties['limit'] = '';
            $properties['order_by'] = '';
            $properties['order_direction'] = '';

            $this->aDashletGroupBy->FromParams($properties);

            return $this->aDashletGroupBy->Render($oPage, $bEditMode, $aExtraParams);
        }
    }