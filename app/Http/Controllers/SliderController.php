<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\Models\Slider;
  
class SliderController extends Controller
{
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index()
    {
        $sliders = Slider::get();
        return view('sliders', compact('sliders'));
    }
}