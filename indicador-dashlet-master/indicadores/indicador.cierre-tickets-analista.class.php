<?php
    require_once 'indicador.class.php';
    use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;

    class IndicadorCierreTicketsAnalista extends Indicador {

        protected $aDashletGroupBy;

        public function __construct($oModelReflection, $sId) {
            $this->aDashletGroupBy = new DashletGroupByPie($oModelReflection, $sId);
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {

            $properties['title'] = 'Requerimientos por niveles de satisfacción';
            $properties['query'] = 'SELECT UserRequest WHERE status NOT IN ("rejected","closed")';
            $properties['group_by'] = 'agent_id';
            $properties['style'] = 'table';
            $properties['aggregation_function'] = 'avg';
            $properties['aggregation_attribute'] = 'time_spent';
            $properties['limit'] = '';
            $properties['order_by'] = 'function';
            $properties['order_direction'] = 'desc';

            $this->aDashletGroupBy->FromParams($properties);

            return $this->aDashletGroupBy->Render($oPage, $bEditMode, $aExtraParams);
        }
    }