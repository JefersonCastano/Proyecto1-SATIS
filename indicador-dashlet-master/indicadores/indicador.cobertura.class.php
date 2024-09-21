<?php
    require_once 'indicador.class.php';

    class IndicadorCobertura extends Indicador {
        
        public function render() {
            return "<h1>Hello, World! Desde indicador.cobertura.class.php</h1>";
        }
    }