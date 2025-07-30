<?php
  
namespace Database\Seeders;
  
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Slider;
  
class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sliders = [
            [
                'title' => 'Image 1',
                'url' => 'https://picsum.photos/seed/picsum/700/400'
            ],
            [
                'title' => 'Image 2',
                'url' => 'https://picsum.photos/id/237/700/400'
            ],
            [
                'title' => 'Image 3',
                'url' => 'https://picsum.photos/id/200/700/400'
            ]
        ];
 
        foreach ($sliders as $key => $value) {
            Slider::create($value);
        }
    }
}