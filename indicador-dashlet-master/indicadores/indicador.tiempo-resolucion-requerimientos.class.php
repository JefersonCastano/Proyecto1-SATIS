<?php
    require_once 'indicador.class.php';
    require_once __DIR__ . '/../utils/QueryHelper.php';
    use Utils\QueryHelper;
    use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;

    class IndicadorTiempoResolucionRequerimientos extends Indicador {

        protected $aType = array();
        protected $aDashletGroupBy;

        public function __construct($oModelReflection, $sId) {
            $this->aType = ['general', 'impact', 'origin', 'priority', 'request_type', 'urgency'];
            $this->aDashletGroupBy = new DashletGroupByPie($oModelReflection, $sId);
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {
            session_start();

            // Verificar si se envió el formulario
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter'])) {
                $_SESSION['selected_value'] = $_POST['filter'];
                header('Location: ' . $_SERVER['REQUEST_URI']); // Redirigir a la misma URL
                exit; // Terminar la ejecución del script
            }

            // Recuperar el valor seleccionado de la sesión (si existe)
            $selectedValue = isset($_SESSION['selected_value']) ? $_SESSION['selected_value'] : 0;

            $sCSSFile = utils::GetAbsoluteUrlModulesRoot() . 'indicador-dashlet-master/asset/css/dashlet-indicador.css';
            $oPage->add_linked_stylesheet($sCSSFile);

            $avg = $this->GetSolutionAverageTime();
            $median = $this->GetSolutionMedianTime();
            $mostFrequentType = $this->GetMostFrequentSolutionType();

            $oPanel = PanelUIBlockFactory::MakeForInformation(Dict::S('UI:DashletIndicador:Prop-Type-Tiempo-Resolucion-Requerimientos'), '');
            $oPanel->AddHtml("<p>" . Dict::S('UI:DashletIndicador:Prop-Type-Tiempo-Resolucion-Requerimientos:Prop-AverageTime') . ":</p>");
            $oPanel->AddHtml('<p class="blue-bold-text">' . $avg . '</p>');
            $oPanel->AddHtml("<p>" . Dict::S('UI:DashletIndicador:Prop-Type-Tiempo-Resolucion-Requerimientos:Prop-MedianTime') . ":</p>");
            $oPanel->AddHtml('<p class="blue-bold-text">' . $median . '</p>');
            $oPanel->AddHtml("<p>" . Dict::S('UI:DashletIndicador:Prop-Type-Tiempo-Resolucion-Requerimientos:Prop-MostFrequentType') . ":</p>");
            $oPanel->AddHtml('<p class="blue-bold-text">' . $mostFrequentType . '</p>');
            $oPanel->AddHtml('<hr>');
            
            $oPanel->AddHtml('<form method="post" action="" id="filter-form">
                                <div class="form-group-horizontal">
                                    <label for="filter" class="form-label">' . Dict::S('UI:DashletIndicador:Prop-Type-Tiempo-Resolucion-Requerimientos:Prop-Combobox-Filter') . '</label>
                                    <select id="filter" name="filter" class="form-combobox">
                                        <option value="0"' . ($selectedValue == 0 ? ' selected' : '') . '>' . Dict::S('UI:DashletIndicador:Prop-Type-Tiempo-Resolucion-Requerimientos:Prop-Combobox-Filter-Sel') . '</option>
                                        <option value="1"' . ($selectedValue == 1 ? ' selected' : '') . '>' . Dict::S('UI:DashletIndicador:Prop-Type-Tiempo-Resolucion-Requerimientos:Prop-Combobox-Filter-Option-1') . '</option>
                                        <option value="2"' . ($selectedValue == 2 ? ' selected' : '') . '>' . Dict::S('UI:DashletIndicador:Prop-Type-Tiempo-Resolucion-Requerimientos:Prop-Combobox-Filter-Option-2') . '</option>
                                        <option value="3"' . ($selectedValue == 3 ? ' selected' : '') . '>' . Dict::S('UI:DashletIndicador:Prop-Type-Tiempo-Resolucion-Requerimientos:Prop-Combobox-Filter-Option-3') . '</option>
                                        <option value="4"' . ($selectedValue == 4 ? ' selected' : '') . '>' . Dict::S('UI:DashletIndicador:Prop-Type-Tiempo-Resolucion-Requerimientos:Prop-Combobox-Filter-Option-4') . '</option>
                                        <option value="5"' . ($selectedValue == 5 ? ' selected' : '') . '>' . Dict::S('UI:DashletIndicador:Prop-Type-Tiempo-Resolucion-Requerimientos:Prop-Combobox-Filter-Option-5') . '</option>
                                    </select>
                                    <button type="submit" class="submit-button">Aplicar</button>
                                </div>
                            </form>');
        
            if ($selectedValue != 0) {
                $oPanel->AddHtml('<p class="chart-title">Requerimientos por ' . Dict::S('Class:UserRequest/Attribute:' . $this->aType[$selectedValue]) . '</p>');
                $oPanel->AddMainBlock($this->GetGraphic($oPage, $bEditMode , $aExtraParams, $selectedValue));
            } else {
                $oPanel->AddHtml('<p >' . Dict::S('UI:DashletIndicador:Prop-Type-Tiempo-Resolucion-Requerimientos:Prop-NoSel') . '</p>');
            }
        
            return $oPanel;
        }

        private function GetGraphic($oPage, $bEditMode = false, $aExtraParams = array(), $selectedValue) {

            $type = $this->aType[$selectedValue];

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