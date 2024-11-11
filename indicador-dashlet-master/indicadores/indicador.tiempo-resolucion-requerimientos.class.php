<?php
    require_once 'indicador.class.php';
    use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;

    class IndicadorTiempoResolucionRequerimientos extends Indicador {

        public function __construct($oModelReflection, $sId) {
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {

            $oPanel = PanelUIBlockFactory::MakeForSuccess('Tiempo de Resolución Promedio, Tipo de Resolución Más Frecuente y Tiempo Medio de Resolución de Requerimientos', '');
            $oPanel->AddHtml('<p>Objetivo: Medir el tiempo promedio de resolución de tickets y requerimientos considerando el tipo de solicitud (request_type), la prioridad, la urgencia, el origen y el impacto. Además, identificar el tipo de resolución más frecuente y evaluar el tiempo medio de resolución para identificar oportunidades de mejora. Este análisis se realizará tanto a nivel general como mediante filtros específicos que incluyan el impacto, prioridad o urgencia, permitiendo una mejor comprensión de los factores que influyen en los tiempos de resolución.</p>');

            return $oPanel;
        }
    }