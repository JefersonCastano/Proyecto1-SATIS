<?php

use Combodo\iTop\Application\UI\Base\Component\Html\Html;
use Combodo\iTop\Application\UI\Base\Layout\UIContentBlockUIBlockFactory;
use Combodo\iTop\Application\UI\Base\Component\Dashlet\DashletContainer;

class DashletIndicador extends Dashlet
{

	public function __construct($oModelReflection, $sId)
	{
		parent::__construct($oModelReflection, $sId);
		$this->aProperties['type'] = '';
		$this->aProperties['width'] = 10;
		$this->aProperties['height'] = 20;
		$this->aCSSClasses[] = 'dashlet-inline';
	}

	public function Render($oPage, $bEditMode = false, $aExtraParams = array())
	{
		$sType = $this->aProperties['type'];
		$iWidth = (int) $this->aProperties['width'];
		$iHeight = (int) $this->aProperties['height'];
		$sId = utils::GetSafeId('dashlet_indicador_'.($bEditMode? 'edit_' : '').$this->sId);
		$sTitle = "Menú de prueba";

		if (version_compare(ITOP_DESIGN_LATEST_VERSION , 3.0) < 0) {
			return null;
		} 
		
		$oDashletContainer = new DashletContainer($this->sId, ['dashlet-content']);
/*
		//-------------------------------------------------------------------------------
		$oDashletContainer->AddHtml("<h1>Datos: </h1>");

		$sQuery = "SELECT UserRequest";
		$oSet = $this->ExecuteQuery($sQuery, $aExtraParams);

		while ($oObj = $oSet->Fetch()) {
			$sLabel = $oObj->GetName(); // Obtener el nombre del objeto
			$UserRequestpriority = $oObj->Get('priority'); // Obtener la prioridad del objeto
			$impact = $oObj->Get('impact'); // Obtener el impacto del objeto
			$urgency = $oObj->Get('urgency'); // Obtener la urgencia del objeto
			$oDashletContainer->AddHtml("<p>Nombre: $sLabel Prioridad: $UserRequestpriority</p>");
			$oDashletContainer->AddHtml("<p>Impacto: $impact Urgencia: $urgency</p>");
		}

		$oDashletContainer->AddHtml("<h1>Grafico: </h1>");
		//-------------------------------------------------------------------------------
*/

		// Iniciar el buffer de salida
		ob_start();

		// Incluir el archivo PHP externo
		include __DIR__."/pages/vistaIris.php";

		// Obtener el contenido del buffer y limpiarlo
		$externalContent = ob_get_clean();

	 	//$externalContent = '<div>Hola mundo</div>';
		$oDashletContainer->AddHtml("<div style=\"background-color:#fff;padding:0.25em;\">$sTitle<div style=\"background-color:#fff;\">$externalContent</div></div>");

		if ($bEditMode) {
			$oDashletContainer->AddHtml('<div class="ibo-dashlet-blocker dashlet-blocker"></div>');
		}

		return $oDashletContainer;
		
	}

	public function GetPropertiesFields(DesignerForm $oForm)
	{
		$oField = new DesignerLongTextField('type', Dict::S('UI:DashletIndicador:Prop-Type'), $this->aProperties['type']);
		$oField->SetMandatory();
		$oForm->AddField($oField);
		
		$oField = new DesignerIntegerField('width', Dict::S('UI:DashletIndicador:Prop-Width'), $this->aProperties['width']);
		$oField->SetMandatory();
		$oForm->AddField($oField);
		
		$oField = new DesignerIntegerField('height', Dict::S('UI:DashletIndicador:Prop-Height'), $this->aProperties['height']);
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

	private function ExecuteQuery($sQuery, $aExtraParams = array())
	{
		// First perform the query - if the OQL is not ok, it will generate an exception : no need to go further
		if (isset($aExtraParams['query_params'])) {
			$aQueryParams = $aExtraParams['query_params'];
		} elseif (isset($aExtraParams['this->class']) && isset($aExtraParams['this->id'])) {
			$oObj = MetaModel::GetObject($aExtraParams['this->class'], $aExtraParams['this->id']);
			$aQueryParams = $oObj->ToArgsForQuery();
		} else {
			$aQueryParams = array();
		}
		$oFilter = DBObjectSearch::FromOQL($sQuery, $aQueryParams);
		$oFilter->SetShowObsoleteData(utils::ShowObsoleteData());

		$oSet = new DBObjectSet($oFilter);
		return $oSet;
	}
}