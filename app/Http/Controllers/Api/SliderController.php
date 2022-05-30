<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\SplashScreen as ResourcesSplashScreen;
use App\Models\Slider;
use Illuminate\Http\Request;
use App\Models\SplashScreen;
use Validator;

class SliderController extends BaseController
{    
    public function getSplashScreen(Request $request,$type)
    {
        try
        {   
            $sliders = Slider::get();
            if(!empty($splashData)){
                return $this->sendSuccess('SLIDER DATA GET SUCCESSFULLY',$sliders); 
            }
        }
        catch (\Throwable $e)
        {
         return $this->sendFailed($e->getMessage().' on line '.$e->getLine(), 400);  
     }
     
     return $this->sendFailed('SORRY! DATA NOT FOUND', 400);  
 }
}