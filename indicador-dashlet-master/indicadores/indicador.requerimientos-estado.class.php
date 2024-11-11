<?php
    require_once 'indicador.class.php';
    use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;

    class IndicadorRequerimientosEstado extends Indicador {

        protected $aDashletGroupBy;

        public function __construct($oModelReflection, $sId) {
            $this->aDashletGroupBy = new DashletGroupByPie($oModelReflection, $sId);
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {

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