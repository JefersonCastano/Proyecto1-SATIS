<?php
    require_once 'indicador.class.php';
    use Combodo\iTop\Application\UI\Base\Component\Dashlet\DashletContainer;

    class IndicadorTest extends Indicador {

        public function __construct($oModelReflection, $sId) {
        }

        public function Render($oPage, $bEditMode = false, $aExtraParams = array()) {

            // Incluir el archivo CSS
            $sCSSFile = utils::GetAbsoluteUrlModulesRoot() . 'indicador-dashlet-master/asset/css/dashlet-indicador.css';
            $oPage->add_linked_stylesheet($sCSSFile);

            $oPanel = new DashletContainer('dashlet-indicador');
            $oPanel->AddCSSClass('dashlet-indicador');
            $oPanel->AddHtml('<p>Este es un indicador de test, puedo insertar código HTML libremente.</p>');
            $oPanel->AddHtml('<p>Como por ejemplo, James Rodriguez.</p>');
            $oPanel->AddHtml('<img src="https://pbs.twimg.com/media/EkQ1PpSU4AU7XG3.jpg" alt="Descripción de la imagen" />');

            return $oPanel;
        }
    }