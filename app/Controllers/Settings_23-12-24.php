<?php

namespace App\Controllers;

use App\Libraries\Imap;

class Settings extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->access_only_admin();
    }

    function index() {
      //  app_redirect('settings/general');
        app_redirect('efactmodules/general');
        
    }

    function general() {
        $tzlist = \DateTimeZone::listIdentifiers();
        $view_data['timezone_dropdown'] = array();
        foreach ($tzlist as $zone) {
            $view_data['timezone_dropdown'][$zone] = $zone;
        }

        $view_data['language_dropdown'] = get_language_list();

        $view_data["currency_dropdown"] = get_international_currency_code_dropdown();
        return $this->template->rander("efactmodules/general", $view_data);
        
    }

   
}

/* End of file general_settings.php */
/* Location: ./app/controllers/general_settings.php */