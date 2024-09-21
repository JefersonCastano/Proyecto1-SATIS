<?php

class FileReader {
    private $subcarpeta;

    public function __construct($subcarpeta) {
        $this->subcarpeta = $subcarpeta;
    }

    public function leerArchivos() {
        $archivos = [];

        // Usar DirectoryIterator para iterar sobre los archivos
        $dir = new DirectoryIterator($this->subcarpeta);
        foreach ($dir as $archivo) {
            if ($archivo->isFile()) {
                $archivos[] = $archivo->getFilename();
            }
        }

        return $archivos;
    }
}
