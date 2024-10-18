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
		$className = "Indicador" . ucfirst($sType);

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

	private function GetIndicadoresTypes()
	{
		// Tipos de indicadores
		// Se pueden agregar más tipos de indicadores aquí
		//TODO: Hacer que se lean automaticamente los archivos de la carpeta indicadores para obtener los tipos de indicadores?
		$aTypes = array(
			'efectividad' => Dict::S('UI:DashletIndicador:Prop-Type-Efectividad'),
			'satisfaccion' => Dict::S('UI:DashletIndicador:Prop-Type-Satisfaccion'),
			'test' => Dict::S('UI:DashletIndicador:Prop-Type-Test'),
		);

		return $aTypes;
	}
}