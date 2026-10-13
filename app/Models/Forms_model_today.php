<?php






namespace App\Models;

class Forms_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'mytable';
        parent::__construct($this->table);
    }



  public function getUiComponentsByModelNumber($modelNumber)
    {
        return $this->asArray()
                    ->where('model_number', $modelNumber)
                    ->findAll();
    }
	public function getModelsByUserId($userId)
{
    return $this->asArray()
                ->where('user_id', $userId)
				 ->groupBy('model_number') // Ensures unique model numbers
                ->findAll();
}
public function getUiComponentsByModelAndPage($modelNumber, $pageNo)
{
    return $this->asArray()
                ->where('model_number', $modelNumber)
                ->where('page_no', $pageNo)
                ->findAll();
}


public function getModelsWithPagesByUserId($userId)
{
    $query = $this->select('model_number, page_no')
                  ->where('user_id', $userId)
                  ->groupBy('model_number, page_no') // Group by both model_number and page_no
                  ->orderBy('model_number', 'ASC')
                  ->orderBy('page_no', 'ASC');

    $results = $query->findAll();

    // Organize the results to ensure unique page numbers under each model
    $organizedModels = [];
    foreach ($results as $result) {
        $organizedModels[$result['model_number']][] = $result['page_no'];
    }

    // Remove duplicates (if any) and sort
    foreach ($organizedModels as $modelNumber => $pages) {
        $organizedModels[$modelNumber] = array_unique($pages);
        sort($organizedModels[$modelNumber]);
    }

    return $organizedModels;
}

}
