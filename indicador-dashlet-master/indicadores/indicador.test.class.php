<?php
    require_once 'indicador.class.php';
    use Combodo\iTop\Application\UI\Base\Component\Panel\PanelUIBlockFactory;

    class IndicadorTest extends Indicador {

        public function __construct($oModelReflection, $sId) {
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {

            $oPanel = PanelUIBlockFactory::MakeForInformation('Indicador de Test', 'Indicador de Test');
            $oPanel->AddHtml('<p>Este es un indicador de test, puedo insertar código HTML libremente.</p>');
            $oPanel->AddHtml('<p>Como por ejemplo, James Rodriguez.</p>');
            $oPanel->AddHtml('<img src="https://pbs.twimg.com/media/EkQ1PpSU4AU7XG3.jpg" alt="Descripción de la imagen" />');

            return $oPanel;
        }
    }