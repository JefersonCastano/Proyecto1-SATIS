<?php
abstract class Indicador {
    // Constructor de la clase abstracta
    abstract function __construct($oModelReflection, $sId);

    // Método abstracto que debe ser implementado por las clases derivadas
    abstract public function Render($oPage, $bEditMode = false, $aExtraParams = array());
}
?>