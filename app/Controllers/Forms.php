<?php

namespace App\Controllers;

class Forms extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("mytable");
    }



  function index() {
  

return $this->template->rander("forms/index");

   

        
    }



public function display($modelNumber, $pageNo)
{
$modelNumber='22';

$userId='167';
$pageNo='1';
    $model = new Forms_model();
    // Use the new method to get components by model number and page number
    $components = $model->getUiComponentsByModelAndPage($modelNumber, $pageNo);

    return view('page', [
        'components' => $components,
        'modelNumber' => $modelNumber, // Pass modelNumber to the view for reference
        'pageNo' => $pageNo // Pass pageNo to the view for reference
    ]);
}

public function selectModel($userId)
{

$userId='167';
    $deviceModel = new \App\Models\Forms_model();
    $modelsWithPages = $deviceModel->getModelsWithPagesByUserId($userId);

    return view('select_model', ['modelsWithPages' => $modelsWithPages]);
}


    

}

