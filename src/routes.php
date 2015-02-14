<?php


Route::get('/petja', function()
{


    die('tst');

    //$cats = Category::allLeaves();
    $cats = Category::allLeaves();

    foreach ($cats as $cat) {
        echo "<pre>";
        var_dump($cat->name);
        echo "</pre>";
    }
    die;


    return View::make('pages.main_page')->with('categories', $cats);


    //return View::make('pages.main_page', array('title' => 'Title'));


});

//Route::get('import', 'Petja\Import\Controllers\ImportController@index');
//Route::get('import/{path}/', 'Petja\Import\Controllers\ImportController@import');
