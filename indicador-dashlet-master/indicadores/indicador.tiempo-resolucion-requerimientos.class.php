<?php
    require_once 'indicador.class.php';
    require_once __DIR__ . '/../utils/QueryHelper.php';
    use Utils\QueryHelper;
    use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;

    class IndicadorTiempoResolucionRequerimientos extends Indicador {

        protected $aType = array();
        protected $aDashletGroupBy;

        public function __construct($oModelReflection, $sId) {
            $this->aType = ['request_type', 'priority', 'impact', 'urgency', 'origin'];
            $this->aDashletGroupBy = new DashletGroupByPie($oModelReflection, $sId);
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {

            $oPanel = PanelUIBlockFactory::MakeNeutral('Tiempo de Resolución Promedio, Tipo de Resolución Más Frecuente y Tiempo Medio de Resolución de Requerimientos', '');

            $avg = $this->GetSolutionAverageTime();
            $median = $this->GetSolutionMedianTime();
            $mostFrequentType = $this->GetMostFrequentSolutionType();

            $oPanel->AddHtml("<p>Tiempo de Resolución Promedio: $avg</p>");
            $oPanel->AddHtml("<p>Tiempo de Resolución Medio: $median</p>");
            $oPanel->AddHtml("<p>Tipo de Resolución Más Frecuente: $mostFrequentType</p>");

            //TODO: BORRAR
            $oPanel->AddMainBlock($this->EJEMPLO($oPage, $bEditMode , $aExtraParams));
            
            return $oPanel;
        }

        private function EJEMPLO($oPage, $bEditMode = false, $aExtraParams = array()){

            $type = $this->aType[1];

            $properties['title'] = 'Requerimientos por ' . Dict::S('Class:UserRequest/Attribute:' . $type);
            $properties['query'] = "SELECT UserRequest FROM UserRequest WHERE UserRequest.status IN ('Resolved','Closed')";
            $properties['group_by'] = $type;
            $properties['style'] = 'bars';
            $properties['aggregation_function'] = 'count';
            $properties['aggregation_attribute'] = '';
            $properties['limit'] = '';
            $properties['order_by'] = '';
            $properties['order_direction'] = '';

            $this->aDashletGroupBy->FromParams($properties);

            return $this->aDashletGroupBy->Render($oPage, $bEditMode, $aExtraParams);
        }

        private function GetSolutionAverageTime(){
            $sQuery = "SELECT UserRequest FROM UserRequest WHERE UserRequest .status IN ('Resolved','Closed')";
            $oSet = QueryHelper::ExecuteQuery($sQuery);
            $timeSpent = 0;
            $dataCount = 0;

            while ($oObj = $oSet->Fetch()) {
                $timeSpent += $oObj->Get('time_spent'); // Obtener el impacto del objeto
                $dataCount++;
            }

            $timeSpent = $timeSpent / $dataCount;
            return QueryHelper::TransformSecondsToTime($timeSpent);
        }

        private function GetSolutionMedianTime()
        {
            $sQuery = "SELECT UserRequest FROM UserRequest WHERE UserRequest.status IN ('Resolved','Closed')";
            $oSet = QueryHelper::ExecuteQuery($sQuery);
            $timeSpentArray = array();
        
            while ($oObj = $oSet->Fetch()) {
                $timeSpentArray[] = $oObj->Get('time_spent'); // Obtener el tiempo de resolución del objeto
            }
        
            // Ordenar el array de tiempos
            sort($timeSpentArray);
            $dataCount = count($timeSpentArray);
        
            if ($dataCount == 0) {
                return QueryHelper::TransformSecondsToTime(0);
            }
        
            // Calcular la mediana
            $middleIndex = floor($dataCount / 2);
            if ($dataCount % 2 == 0) {
                // Si hay un número par de elementos, la mediana es el promedio de los dos elementos del medio
                $medianTimeSpent = ($timeSpentArray[$middleIndex - 1] + $timeSpentArray[$middleIndex]) / 2;
            } else {
                // Si hay un número impar de elementos, la mediana es el elemento del medio
                $medianTimeSpent = $timeSpentArray[$middleIndex];
            }
        
            return QueryHelper::TransformSecondsToTime($medianTimeSpent);
        }

        private function GetMostFrequentSolutionType()
        {
            $sQuery = "SELECT UserRequest FROM UserRequest WHERE UserRequest.status IN ('Resolved','Closed')";
            $oSet = QueryHelper::ExecuteQuery($sQuery);
            $aTypeCount = array();
        
            while ($oObj = $oSet->Fetch()) {
                $sType = $oObj->Get('resolution_code'); // Obtener el tipo de solicitud del objeto
                if (!isset($aTypeCount[$sType])) {
                    $aTypeCount[$sType] = 0;
                }
                $aTypeCount[$sType]++;
            }
        
            arsort($aTypeCount);
            $aType = array_keys($aTypeCount);
            $sMostFrequentType = $aType[0];
            return Dict::S('Class:UserRequest/Attribute:resolution_code/Value:' . $sMostFrequentType);
        }
    }