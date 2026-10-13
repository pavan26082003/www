<?php

namespace App\Controllers;

use App\Models\DeviceModel;

class DeviceController extends BaseController
{

	public function display($modelNumber, $pageNo)
{
    $model = new DeviceModel();
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
    $deviceModel = new \App\Models\DeviceModel();
    $modelsWithPages = $deviceModel->getModelsWithPagesByUserId($userId);

    return view('select_model', ['modelsWithPages' => $modelsWithPages]);
}
}
