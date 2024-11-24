<?php
    require_once 'indicador.class.php';
    use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;

    class IndicadorResolucionRequerimientosOrganizacion extends Indicador {

        protected $aDashletGroupBy;

        public function __construct($oModelReflection, $sId) {
            $this->aDashletGroupBy = new DashletGroupByPie($oModelReflection, $sId);
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {

            $properties['title'] = 'Tiempo de Resolución por Organización';
            $properties['query'] = 'SELECT UserRequest WHERE status IN ("rejected","closed")';
            $properties['group_by'] = 'org_id';
            $properties['style'] = 'bars';
            $properties['aggregation_function'] = 'avg';
            $properties['aggregation_attribute'] = 'time_spent';
            $properties['limit'] = '';
            $properties['order_by'] = 'function';
            $properties['order_direction'] = '';

            $this->aDashletGroupBy->FromParams($properties);

            return $this->aDashletGroupBy->Render($oPage, $bEditMode, $aExtraParams);
        }
    }