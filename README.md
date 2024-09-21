# Extensión Indicadores de Rendimiento TIC

## Pasos de instalación

1. **Descargar o clonar el repositorio**. El directorio descargado deberá llamarse `satis-indicadores-tic-main`.
2. Dentro del código fuente de SATIS, dirigirse al directorio `/extensions`.
3. Pegar la carpeta completa descargada en el paso 1 dentro de `extensions`.
4. En la carpeta raíz de SATIS, dirigirse a:
    ```sh
    conf/production
    ```
    Dentro de este directorio, hay un archivo llamado **config-itop.php**. A este archivo se le deben otorgar permisos de escritura:
   
   - **Windows**: Hacer clic derecho en el archivo, seleccionar "Propiedades", desmarcar la casilla "Solo lectura" y luego aplicar y aceptar.
   - **Unix (Linux/MacOS)**: Otorgar permisos ejecutando el siguiente comando:
     ```sh
     chmod u+w config-itop.php
     ```

5. Dirigirse a la URL base de SATIS y reejecutar el setup:
    - La ruta tiene la forma:
    ```sh
    http://<tu_dominio>/setup/wizard.php
    ```
    - **<tu_dominio>** es la URL base configurada en la instalación de SATIS. Si está configurada en `localhost`, sería:
    ```sh
    http://localhost/setup/wizard.php
    ```

6. Continuar con el setup sin realizar modificaciones hasta llegar al menú de extensiones. Aquí, marcar la extensión con el nombre **"Satis Indicadores TIC Extension"**.
7. Finalizar la instalación. La opción "Indicadores" debería aparecer en el menú de opciones.

## Para desarrollo

1. **Instalar el Toolkit**. Sigue las instrucciones de instalación en: [https://www.itophub.io/wiki/page?id=3_0_0:customization:datamodel](https://www.itophub.io/wiki/page?id=3_0_0:customization:datamodel)
2. Realizar la instalación de la extensión.
3. Cada vez que se realice un cambio en la extensión, compilar SATIS para ver las modificaciones. Para hacerlo, ingresar a:
    ```sh
    http://<tu_dominio>/toolkit
    ```
    Luego, dirigirse al menú "iTop Update". En el apartado "Compilation", hacer clic en "UPDATE ITOP CODE" y aceptar. Al finalizar este proceso, los cambios en SATIS se verán al recargar la página.

> **Nota**: La instalación del Toolkit es solo para desarrollo y no se recomienda para el despliegue en producción.

---
# Créditos de Desarrollo

Este proyecto fue desarrollado por:

- **Katherin Alexandra Zuñiga** - kzunigam@unicauca.edu.co
- **Valentina Fernández Guerrero** - vfernandezg@unicauca.edu.co
- **David Santiago Giron Muñoz** - davidgiron@unicauca.edu.co
- **Jeferson Castano Ossa** - jcastanoossa@unicauca.edu.co
- **David Santiago Fernández Dejoy** - dfernandezd@unicauca.edu.co
- **Jose David Chilito Cometa** - jdchilito@unicauca.edu.co
