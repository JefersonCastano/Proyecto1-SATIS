<?php
    require_once 'indicador.class.php';
    use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;

    class IndicadorResolucionRequerimientosOrganizacion extends Indicador {

        public function __construct($oModelReflection, $sId) {
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {

            $oPanel = PanelUIBlockFactory::MakeForSuccess('Cantidad de requerimientos y tiempo medio de resolución según la organización', '');
            $oPanel->AddHtml('<p>Objetivo: Identificar oportunidades de mejora evaluando el tiempo medio de resolución de los requerimientos según su organización, también teniendo en cuenta el número de estos..</p>');

            return $oPanel;
        }
    }