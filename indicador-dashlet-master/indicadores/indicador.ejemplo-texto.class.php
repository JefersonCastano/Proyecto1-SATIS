<?php
    use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;
    require_once 'indicador.class.php';

    class IndicadorEjemploTexto extends Indicador {

        public function __construct($oModelReflection, $sId) {
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) 
        {
            $oPanel = PanelUIBlockFactory::MakeForSuccess('Ejemplo Indicador Texto', 'Este es un mero ejemplo.');
            $oPanel->AddHtml('<p>Este es un indicador de ejemplo para SATIS.</p>');

            return $oPanel;
        }
    }



