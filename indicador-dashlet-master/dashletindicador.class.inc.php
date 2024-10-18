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
		$oField = new DesignerLongTextField('type', Dict::S('UI:DashletIndicador:Prop-Type'), $this->aProperties['type']);
		$oField->SetMandatory();
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
}