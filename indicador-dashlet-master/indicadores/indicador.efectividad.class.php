<?php
    require_once 'indicador.class.php';

    class IndicadorEfectividad extends Indicador {
        
        protected $aDashletGroupBy;

        public function __construct($oModelReflection, $sId) {
            $this->aDashletGroupBy = new DashletGroupByPie($oModelReflection, $sId);
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {

            $properties['title'] = 'Requisitos Reportados';
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