<?php
    require_once 'indicador.class.php';
    use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;

    class IndicadorCierreTicketsAnalista extends Indicador {

        public function __construct($oModelReflection, $sId) {
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {

            $oPanel = PanelUIBlockFactory::MakeForSuccess('Tasa de Cierre de Tickets de Soporte por Analista en el Área de TI', '');
            $oPanel->AddHtml('<p>Objetivo: Mide la cantidad de tickets de soporte cerrados por cada empleado del equipo de TI. Este indicador es esencial para identificar problemas en el rendimiento individual y colectivo, evaluando la eficacia del equipo en resolver incidencias y su capacidad para mantener los estándares de productividad. Ayuda a detectar brechas en el rendimiento, posibles deficiencias en la formación y la falta de estandarización en los procedimientos de soporte.</p>');

            return $oPanel;
        }
    }