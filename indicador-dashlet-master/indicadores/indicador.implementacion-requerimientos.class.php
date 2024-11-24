<?php
    require_once 'indicador.class.php';
    use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;

    class IndicadorImplementacionRequerimientos extends Indicador {

        protected $aDashletGroupBy;

        public function __construct($oModelReflection, $sId) {
            $this->aDashletGroupBy = new DashletGroupByPie($oModelReflection, $sId);
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {

            $properties['title'] = 'Implementación de Requerimientos Aprobados';
            $properties['query'] = 'SELECT UserRequest FROM UserRequest WHERE `status` IN ("waiting_for_approval","Approved")';
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