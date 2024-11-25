<?php
    require_once 'indicador.class.php';

    class IndicadorSatisfaccion extends Indicador {
        
        protected $aDashletGroupBy;

        public function __construct($oModelReflection, $sId) {
            $this->aDashletGroupBy = new DashletGroupByPie($oModelReflection, $sId);
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {

            $properties['title'] = Dict::S('UI:DashletIndicador:Prop-Type-Satisfaccion');
            $properties['query'] = 'SELECT UserRequest WHERE status IN ("Resolved","Closed")';
            $properties['group_by'] = 'user_satisfaction';
            $properties['style'] = 'bars';
            $properties['aggregation_function'] = 'count';
            $properties['aggregation_attribute'] = '';
            $properties['limit'] = '';
            $properties['order_by'] = '';
            $properties['order_direction'] = '';

            $this->aDashletGroupBy->FromParams($properties);

            return $this->aDashletGroupBy->Render($oPage, $bEditMode, $aExtraParams);
        }
    }