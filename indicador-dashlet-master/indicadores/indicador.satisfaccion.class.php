<?php
    require_once 'indicador.class.php';

    class IndicadorSatisfaccion extends Indicador {
        
        protected $aDashletGroupBy;

        public function __construct($oModelReflection, $sId) {
            $this->aDashletGroupBy = new DashletGroupByPie($oModelReflection, $sId);
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {

            // Agregar el archivo CSS
            $sCSSFile = utils::GetAbsoluteUrlModulesRoot() . 'indicador-dashlet-master/asset/css/dashlet-indicador.css';
            $oPage->add_linked_stylesheet($sCSSFile);
            
            // Propiedades del dashlet
            $properties['title'] = Dict::S('UI:DashletIndicador:Prop-Type-Satisfaccion');
            $properties['query'] = 'SELECT UserRequest WHERE status IN ("Resolved","Closed")';
            $properties['group_by'] = 'user_satisfaction';
            $properties['style'] = 'bars';
            $properties['aggregation_function'] = 'count';
            $properties['aggregation_attribute'] = '';
            $properties['limit'] = '';
            $properties['order_by'] = 'function';
            $properties['order_direction'] = '';

            // Configurar el dashlet con las propiedades
            $this->aDashletGroupBy->FromParams($properties);

            // Renderizar el dashlet
            return $this->aDashletGroupBy->Render($oPage, $bEditMode, $aExtraParams);
        }
    }