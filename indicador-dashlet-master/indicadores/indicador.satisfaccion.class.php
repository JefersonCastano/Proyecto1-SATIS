<?php
    require_once 'indicador.class.php';

    class IndicadorSatisfaccion extends Indicador {
        
        public function render() {
            return "<h1>Hello, World! Desde indicador.satisfaccion.class.php</h1>";
        }
    }