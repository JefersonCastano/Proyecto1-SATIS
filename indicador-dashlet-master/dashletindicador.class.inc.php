<?php

class DashletIndicador extends Dashlet
{
	public function __construct($oModelReflection, $sId)
	{
		parent::__construct($oModelReflection, $sId);
		$this->aProperties['type'] = '';
	}

	public function Render($oPage, $bEditMode = false, $aExtraParams = array())
	{
		if (version_compare(ITOP_DESIGN_LATEST_VERSION , 3.0) < 0) {
			return null;
		} 

		$sType = $this->aProperties['type'];

		if($sType == '') {
			$dashletTemp = new DashletGroupByPie($this->oModelReflection, $this->sId);
			return $dashletTemp->RenderNoData($oPage, $bEditMode, $aExtraParams);
		}

		$classFileName = "indicador." . $sType . ".class.php";
		$className = $this->GetIndicadorClassName($sType);

		// Incluir el archivo PHP externo que contiene la clase hija
		include_once __DIR__ . "/indicadores/" . $classFileName;

		// Verificar si la clase existe y luego instanciarla
		if (class_exists($className)) {
			$indicador = new $className($this->oModelReflection, $this->sId);

			if ($indicador instanceof Indicador) {
				return $indicador->Render($oPage, $bEditMode, $aExtraParams);
			} else {
				echo "<p>Error: La clase $className no es una instancia de Indicador.</p>";
			}
		} else {
			echo "<p>Error: La clase $className no existe.</p>";
		}
	}

	public function GetPropertiesFields(DesignerForm $oForm)
	{
		$aTypes = $this->GetIndicadoresTypes();

		$oField = new DesignerComboField('type', Dict::S('UI:DashletIndicador:Prop-Type'), $this->aProperties['type']);
		$oField->SetMandatory();
		$oField->SetAllowedValues($aTypes);
		$oForm->AddField($oField);
	}

	static public function GetInfo()
	{
		return array(
				'label' => Dict::S('UI:DashletIndicador:Label'),
				'icon' => 'env-'.utils::GetCurrentEnvironment().'/indicador-dashlet-master/images/icono-indicadores.png',
				'description' => Dict::S('UI:DashletIndicador:Description'),
		);
	}

	private function GetIndicadorClassName(String $sType)
	{
		$parts = explode('-', $sType);
		$className = 'Indicador';

		foreach ($parts as $part) {
			$className .= ucfirst($part);
		}
		return $className;
	}

	private function GetIndicadoresTypes()
	{
		// Directorio de indicadores
		$dir = __DIR__ . '/indicadores';

		// Obtener lista de archivos en el directorio de indicadores
		$files = scandir($dir);

		$aTypes = array();

		foreach ($files as $file) {
			// Ignorar los directorios '.' y '..'
			if ($file !== '.' && $file !== '..') {
				// Hacer split del nombre del archivo por '.'
				$parts = explode('.', $file);
				// Verificar que el resultado tenga exactamente 4 partes
				if (count($parts) === 4 && $parts[0] === 'indicador' && $parts[2] === 'class' && $parts[3] === 'php') {
					// Obtener el nombre del tipo de indicador
					$typeParts = array();

					foreach (explode('-', $parts[1]) as $namePart) {
						$typeParts[] = ucfirst($namePart);
					}

					$typeLabelName = implode('-', $typeParts);
					$type = $parts[1];
					// Agregar el tipo de indicador al array
					$aTypes[$type] = Dict::S('UI:DashletIndicador:Prop-Type-' . ucfirst($typeLabelName));
				}
			}
		}

		return $aTypes;
	}
}