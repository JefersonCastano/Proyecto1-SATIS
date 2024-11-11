<?php
    require_once 'indicador.class.php';
    use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;

    class IndicadorTestVariosNombres extends Indicador {

        public function __construct($oModelReflection, $sId) {
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {

            $oPanel = PanelUIBlockFactory::MakeForSuccess('Clase Test', 'Con varias palabras en el nombre');
            $oPanel->AddHtml('<p>Yo se que lo va a leer a la primera.</p>');

            return $oPanel;
        }
    }