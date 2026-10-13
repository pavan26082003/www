<?php




namespace App\Controllers;



class Forms extends Security_Controller
{


function __construct() {
        parent::__construct();
        $this->init_permission_checker("mytable");
    }





	public function display($modelNumber, $pageNo)
{
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
    $deviceModel = new \App\Models\Forms_model();
    $modelsWithPages = $deviceModel->getModelsWithPagesByUserId($userId);

    return view('select_model', ['modelsWithPages' => $modelsWithPages]);
}
}
