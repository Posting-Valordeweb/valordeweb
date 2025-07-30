<?php
class Widget extends CWidget {
	public function getViewFile($viewName) {
		$viewPath = $this->getOwner()->getViewPath(true);
		$viewFile = $viewPath.DIRECTORY_SEPARATOR.$viewName.".php";
		if(is_file($viewFile)) {
			return $viewFile;
		}
		return parent::getViewFile($viewName);
	}
}