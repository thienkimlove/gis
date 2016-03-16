<?php

/*
 * |-------------------------------------------------------------------------- | Application Routes |-------------------------------------------------------------------------- | | Here is where you can register all of the routes for an application. | It's a breeze. Simply tell Laravel the URIs it should respond to | and give it the controller to call when that URI is requested. |
 */

/**
 * *************************************************************
 * Main Routes
 * *************************************************************
 */
Route::get('/reload','MainController@reload');
Route::group([
    'middleware' => [
        'auth',
        'agreed'
    ]
], function ($router)
{
    
    Route::get('/folders/create-layer', 'MainController@createLayer');
    
    Route::get('/folders/buy-fertilizer-view/{layer_id}/{numberMesh}', 'MainController@buyFertilizerView');
    Route::get('/folders/download-fertilizer-map/{layer_id}', 'MainController@downloadFertilizerMap');
    Route::get('/folders/get-fertilizer-properties/{layer_id}', 'FertilizerController@fertilizerPropetiesView');
    Route::get('/folders/save-data-payment-record/{layer_id}', 'MainController@saveDataPaymentRecord');

    Route::post('/folders/store-layer', array(
        'as' => 'folders.storeLayer',
        'uses' => 'MainController@storeLayer'
    ));
    
    Route::get('/folders/edit-layer/{layer_id}', 'MainController@editLayer');
    
    Route::post('/folders/update-layer/{layer_id}', array(
        'as' => 'folders.updateLayer',
        'uses' => 'MainController@updateLayer'
    ));
    
    Route::post('/user/updateStateOfUser', array(
        'as' => 'user.updateStateOfUser',
        'uses' => 'MainController@updateStateOfUser'
    ));
    
    Route::put('/folders/change-folder', array(
        'as' => 'folders.changeFolder',
        'uses' => 'MainController@changeFolder'
    ));
    
    Route::post('/folders/delete-folders', array(
        'as' => 'folders.deleteFolder',
        'uses' => 'MainController@deleteFolder'
    ));
    Route::get('/folders/layer-restore/{id}', array(
        'as' => 'folders.layerRestore',
        'uses' => 'MainController@layerRestore'
    ));
    Route::get('/download-file-csv/{id}','MainController@downloadFileCsv');

    $router->resource('/folders', 'MainController');
    
    Route::get('/', array(
        'uses' => 'MainController@index'
    ));
    Route::post('/jsonTree', array(
        'uses' => 'MainController@getTree'
    ));
    
    Route::get('/folders/json-tree', array(
        'as' => 'folders.jsonTree',
        'uses' => 'MainController@jsonTree'
    ));
    
    Route::post('/folders/fertilizer-map-payment', array(
        'as' => 'folders.fertilizerMapPayment',
        'uses' => 'MainController@fertilizerMapPayment'
    ));


});

// login
Route::post('selection', 'MapController@selection');
Route::post('show-map-json', 'MapController@showMapJson');
Route::get('genMap', 'DemoController@genMap');
Route::get('close', 'DemoController@index');
Route::get('mapOption/{mapId}', 'DemoController@mapOption');
Route::get('demo-tree', 'SecurityController@demoTree');
Route::get('login', 'SecurityController@login');

// logout

Route::get('logout', 'SecurityController@logout');

// do login.

Route::post('do-login', array(
    'as' => 'do-login',
    'uses' => 'SecurityController@doLogin'
));

// Login with guest

Route::get('/login-guest', array(
    'as' => 'login-guest',
    'uses' => 'SecurityController@loginWithGuest'
));

// Internal Error.

Route::get('server-error/{error}', function ($error)
{
    return view('errors.' . $error);
});

/**
 * *************************************************************
 * End of Main Routes.
 * *************************************************************
 */

/**
 * *************************************************************
 * Creating map
 * *************************************************************
 */

Route::post('creating-map/{mapId}/{cropId?}', 'MapController@creatingMap');
Route::post('open-map-viewer/{mapId}/{cropId?}', 'MapController@openMapViewer');
Route::post('admin/get-map-confirm-viewer', 'MapController@getMapConfirm');
Route::get('changing-color/{fertilizerId}', 'MapController@openChangingColor');

Route::post('open-value-changing-color', array(
    'as' => 'openEditingColor',
    'uses' => 'MapController@openValueChangingColor',
    'middleware' => [
        'auth',
        'permission'
    ],
));
Route::post('editing-color', array(
    'as' => 'openEditingColor',
    'uses' => 'MapController@saveMergingColor',
    'middleware' => [
        'auth',
        'permission'
    ],
));

Route::post('merging-map-color-map', array(
    'as' => 'merging-map-color-map',
    'uses' => 'MapController@mergeMapColor',
    'middleware' => [
        'auth',
        'permission'
    ],
));
Route::post('submit-merging-map-color-map', array(
    'as' => 'submit-merging-map-color-map',
    'uses' => 'MapController@submitMergingMapColor',
    'middleware' => [
        'auth',
        'permission'
    ],
));
Route::post('merging-other-color-map', array(
    'as' => 'merging-other-color-map',
    'uses' => 'MapController@mergeDataMapColor',
    'middleware' => [
        'auth',
        'permission'
    ],
));
Route::post('submit-editing-color', array(
    'as' => 'submit-editing-color',
    'uses' => 'MapController@submitEditingColor',
    'middleware' => [
        'auth',
        'permission'
    ],
));

Route::post('submit-changing-color', array(
    'as' => 'submit-changing-color',
    'uses' => 'MapController@submitChangingColor',
    'middleware' => [
        'auth',
        'permission'
    ],
));

Route::post('submit-creating-map', array(
    'as' => 'submit-creating-map',
    'uses' => 'MapController@submitCreatingMap',
    'middleware' => [
        'auth',
        'permission'
    ]
));

Route::get('/get-fertilizer/{fertilizerId}/{cropId}', array(
    'as' => 'get-fertilizer',
    'uses' => 'FertilizerController@getFertilizer',
    'middleware' => array(
        'auth',
        'permission'
    )
));

Route::get('/get-list-fertilizer/{fertilizerId}/{userId}', array(
    'as' => 'get-list-fertilizer',
    'uses' => 'FertilizerController@getListFertilizers',
    'middleware' => array(
        'auth',
        'permission'
    )
));

Route::get('/get-list-standard-fertilizer/{fertilizerId}/{userId}/{cropId}', array(
    'as' => 'get-list-fertilizer',
    'uses' => 'FertilizerController@getListStandardFertilizers',
    'middleware' => array(
        'auth',
        'permission'
    )
));

Route::get('/get-options/{fertilizerId}/{cropId}', array(
    'as' => 'get-options',
    'uses' => 'MapController@getOptions',
    'middleware' => array(
        'auth',
        'permission'
    )
));

/**
 * *************************************************************
 * End creating map
 * *************************************************************
 */

/**
 * *************************************************************
 * Help view Route.
 * *************************************************************
 */
Route::get('view/{file}', array(
    'uses' => 'HelpLinkController@view',
    'middleware' => [
        'auth'
    ]
));
Route::post('get/help', array(
    'uses' => 'HelpLinkController@getHelp',
    'middleware' => [
        'auth'
    ]
));
Route::post('log-help', array(
    'uses' => 'HelpLinkController@logHelp',
    'middleware' => [
        'auth'
    ]
));
/**
 * *************************************************************
 * End of Heplview
 * *************************************************************
 */
/**
 * *************************************************************
 * UserGroups
 * *************************************************************
 */
// CURD routes.

Route::group([
    'middleware' => [
        'auth',
        'permission',
        'agreed'
    ],
    'roles' => 'auth_user_group'
], function ($router)
{
    $router->resource('admin/groups', 'GroupsController');
});

// get grid Data route.

Route::get('groupGrid', array(
    'uses' => 'GroupsController@groupGrid',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_user_group'
));

Route::post('delete-group', array(
    'as' => 'delete-group',
    'uses' => 'GroupsController@deleteGroup',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_user_group'
));
Route::post('get-admin-group', array(
    'as' => 'get-admin-group',
    'uses' => 'GroupsController@getAdminGroup',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_user_group'
));
/**
 * *************************************************************
 * End of UserGroups
 * *************************************************************
 */

/**
 * *************************************************************
 * Footer Management
 * *************************************************************
 */

Route::get('footer', array(
    'uses' => 'FooterController@index',
    'middleware' => array(
        'auth',
        'permission',
        'agreed'
    ),
    'roles' => 'auth_footer'
));

Route::post('saveFooter', array(
    'uses' => 'FooterController@saveFooter',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_footer'
));
/**
 * *************************************************************
 * End of Footer Management
 * *************************************************************
 */

/**
 * *************************************************************
 * Helper Management
 * *************************************************************
 */
Route::group([
    'middleware' => [
        'auth',
        'permission',
        'agreed'
    ],
    'roles' => 'auth_help'
], function ($router)
{
    $router->resource('helplink', 'HelpLinkController');
});

// get grid Data route.

Route::get('helpGrid', array(
    'uses' => 'HelpLinkController@helpGrid',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_help'
));

Route::post('delete-helplink', array(
    'as' => 'delete-helplink',
    'uses' => 'HelpLinkController@deleteHelplink',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_help'
));
/**
 * *************************************************************
 * End of HelpLink
 * *************************************************************
 */

/**
 * *************************************************************
 * User Management
 * *************************************************************
 */

Route::group([
    'middleware' => [
        'auth',
        'permission',
        'agreed'
    ],
    'roles' => 'auth_user_registration'
], function ($router)
{
    $router->resource('admin/users', 'UserController');
});

Route::get('admin/user/get-grid', array(
    'as' => 'user/get-grid',
    'uses' => 'UserController@userGetGrid',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_user_registration'
));

Route::post('admin/search-users', array(
    'as' => 'search-users',
    'uses' => 'UserController@searchUser',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_user_registration'
));

Route::post('admin/delete-user', array(
    'as' => 'delete-user',
    'uses' => 'UserController@deleteUser',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_user_registration'
));

/**
 * *************************************************************
 * End of User Management
 * *************************************************************
 */

Route::get('/forgot-password', 'UserController@forgotPassword');

/**
 * Change account infomation
 */
Route::post('/process-change-account', array(
    'as' => 'process-change-account',
    'uses' => 'UserController@dochangeAccount'
));

/**
 * Forget password
 */
Route::get('/forget-password', 'UserController@openForgetPassword');
Route::post('/send-email', 'UserController@sendEmail');

Route::get('/reset-password/{username}/{guid}', 'UserController@openResetPassword');
Route::get('/reset-success/', 'UserController@openResetPassword');
Route::post('/submit-reset-password', 'UserController@submitResetPassword');

Route::get('/test-function', 'UserController@testFunction');
Route::get('/get-paging-data/{searchSetting}', 'UserController@loadGridData');

Route::get('change-account', array(
    'as' => 'change-account',
    'uses' => 'UserController@openChangingUser',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_change_username_password'
));
Route::get(
    '/users/confirm-change-account/{username}/{password}/{email}/{token}',
    array(
        'uses' => 'UserController@confirmChangingUser',
        'middleware' => [
            'auth',
            'permission'
        ]
    )
);
Route::post('submit-changing-user', array(
    'uses' => 'UserController@submitChangingUser',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_change_username_password'
));

Route::get('authorization', array(
    'uses' => 'UserController@openAuthorization',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_authorization'
));

Route::get('get-authorization-group/{groupId}', array(
    'uses' => 'UserController@getAuthorizationGroup',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_authorization'
));

Route::post('submit-authorization', array(
    'uses' => 'UserController@submitAuthorization',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_authorization'
));

/**
 * *************************************************************
 * Fertilizer
 * *************************************************************
 */

Route::get('fertilizers', array(
    'uses' => 'FertilizerController@openFertilizerList',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_user_fertilizer_definition'
));

Route::get('get-fertilizers', array(
    'uses' => 'FertilizerController@getFertilizers',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_user_fertilizer_definition'
));

Route::get('fertilization-out-prediction/{layer_id}', array(
    'uses' => 'FertilizerController@fertilizationOutPrediction',
    'middleware' => array(
        'auth',
        'permission'
    )
));

Route::get('fertilization-predict-popup/{barrel_type}', array(
    'uses' => 'FertilizerController@fertilizationPredictPopup',
    'middleware' => array(
        'auth',
        'permission'
    )
));

Route::post('delete-fertilizers', array(
    'as' => 'delete-fertilizers',
    'uses' => 'FertilizerController@deleteFertilizers',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_user_fertilizer_definition'
));

Route::get('/fertilizer-info', array(
    'as' => 'fertilizer-info',
    'uses' => 'FertilizerController@openFertilizerInfo',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_user_fertilizer_definition'
));

Route::get('/edit-fertilizer/{fertilizerId}', array(
    'as' => 'edit-fertilizer',
    'uses' => 'FertilizerController@editFertilizer',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_user_fertilizer_definition'
));

Route::get('/specify-user/{fertilizerId}', array(
    'as' => 'specify-user',
    'uses' => 'FertilizerController@openSpecifyUser',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_user_fertilizer_definition'
));

Route::post('copy-fertilizer', array(
    'as' => 'copy-fertilizer',
    'uses' => 'FertilizerController@copyFertilizer',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_user_fertilizer_definition'
));
Route::post('save-copy-system-fertilizer', array(
    'as' => 'save-copy-system-fertilizer',
    'uses' => 'FertilizerController@copySystemFertilizer',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_user_fertilizer_definition'
));

Route::post('submit-fertilizer', array(
    'as' => 'submit-fertilizer',
    'uses' => 'FertilizerController@submitFertilizer',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_user_fertilizer_definition'
));

Route::get('get-specify-users/{searchModel}', array(
    'uses' => 'FertilizerController@getSpecifyUsers',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_user_fertilizer_definition'
));

Route::post('submit-specify-user', array(
    'as' => 'submit-specify-user',
    'uses' => 'FertilizerController@submitSpecifyUser',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_user_fertilizer_definition'
));

Route::get('standard-crops/{standardId}', array(
    'uses' => 'FertilizerController@openStandardCropList',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_user_fertilizer_definition'
));

Route::get('get-standard-crops/{standardId}', array(
    'uses' => 'FertilizerController@getStandardCrops',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_user_fertilizer_definition'
));

Route::get('get-standard-crop-details/{standardCropId}', array(
    'uses' => 'FertilizerController@getStandardCropDetails',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_user_fertilizer_definition'
));

Route::get('system-standard-crop-admin/{standardId}', array(
    'uses' => 'FertilizerController@openSystemStandardCropAdmin',
    'middleware' => array('auth', 'permission'),
    'roles' => 'auth_user_fertilizer_definition'
));

Route::get('get-system-standard-crop-details/{fertilizerStandardId}/{cropId}', array(
    'uses' => 'FertilizerController@getSystemStandardCropDetails',
    'middleware' => array('auth', 'permission'),
    'roles' => 'auth_user_fertilizer_definition'
));
Route::get('clear-system-standard-crop-details/{fertilizerStandardId}/{cropId}', array(
    'uses' => 'FertilizerController@clearSystemStandardCropDetails',
    'middleware' => array('auth', 'permission'),
    'roles' => 'auth_user_fertilizer_definition'
));

Route::get('standard-crop-info/{standardId}', array(
    'uses' => 'FertilizerController@openStandardCropInfo',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_user_fertilizer_definition'
));

Route::get('edit-standard-crop/{standardCropId}', array(
    'uses' => 'FertilizerController@editStandarCropInfo',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_user_fertilizer_definition'
));

Route::get('standard-crop-copying/{standardCropId}', array(
    'uses' => 'FertilizerController@openStandardCropCopying',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_user_fertilizer_definition'
));

Route::get('standard-crop-detail/{standardCropId}', array(
    'uses' => 'FertilizerController@openStandardCropDetail',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_user_fertilizer_definition'
));

Route::post('submit-standard-crop-copying', array(
    'as' => 'submit-standard-crop-copying',
    'uses' => 'FertilizerController@copyStandardCrop',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_user_fertilizer_definition'
));

Route::post('delete-standard-crops', array(
    'as' => 'delete-standard-crops',
    'uses' => 'FertilizerController@deleteStandardCrops',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_user_fertilizer_definition'
));

Route::post('submit-standard-crop', array(
    'as' => 'submit-standard-crop',
    'uses' => 'FertilizerController@submitStandardCropInfo',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_user_fertilizer_definition'
));

Route::post('submit-standard-crop-details', array(
    'as' => 'submit-standard-crop-details',
    'uses' => 'FertilizerController@submitStandardCropDetails',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_user_fertilizer_definition'
));
Route::post('submit-system-standard-crop-details', array(
    'as' => 'submit-system-standard-crop-details',
    'uses' => 'FertilizerController@submitSystemStandardCropDetails',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_user_fertilizer_definition'
));

Route::get('standard-crops/{standardId}', array(
    'uses' => 'FertilizerController@openStandardCropList',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_user_fertilizer_definition'
));

Route::post('load-fertilizer-autocomplete', array(
    'as' => 'load-fertilizer-autocomplete',
    'uses' => 'FertilizerController@ajaxAutocomplete',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_user_fertilizer_definition'
));


Route::post('edit-fertilizer', array(
    'as' => 'edit-fertilizer',
    'uses' => 'FertilizerController@editFertilizer',
    
    // 'uses' => 'GroupsController@deleteGroup',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_authorization'
));
Route::get('copy-system-fertilizer/{fertilizerId}', array(
    'as' => 'copy-system-fertilizer',
    'uses' => 'FertilizerController@copySystemFertilizerPopup',
    'middleware' => [
        'auth'
    ],
    'roles' => 'auth_authorization'
));
/**
 * *************************************************************
 * End of Fertilizer
 * *************************************************************
 */

/**
 * sample Map
 */
Route::get('/map-sample', 'MapController@index');
Route::get('/quan', 'MapController@quan');

Route::get('term', array(
    'as' => 'term',
    'uses' => 'TermOfUseController@index',
    'middleware' => [
        'auth'
    ]
));
Route::get('showTerm', array(
    'as' => 'showTerm',
    'uses' => 'TermOfUseController@showTerm',
    'middleware' => [
        'auth'
    ]
));

Route::post('submitTerm', array(
    'uses' => 'TermOfUseController@submit',
    'middleware' => [
        'auth'
    ]
));

/**
 * Data import screen
 */
Route::get('/admin/import-data', array(
    'as' => 'import.data',
    'uses' => 'ImportController@index',
    'middleware' => [
        'auth'
    ]
));
Route::post('/import-data/store', array(
    'as' => 'import.data.store',
    'uses' => 'ImportController@store',
    'middleware' => [
        'auth'
    ]
));
Route::get('/import-data/map', array(
    'uses' => 'ImportController@importMap',
    'middleware' => [
        'auth'
    ]
));
Route::get('/import-data/layer-map', array(
    'as' => 'import.data.layer.map',
    'uses' => 'ImportController@importLayerMap',
    'middleware' => [
        'auth'
    ]
));
Route::post('/import-data/ajax-autocomplete', array(
    'uses' => 'ImportController@ajaxAutocomplete',
    'middleware' => [
        'auth'
    ]
));

Route::post('/load-fertilizer-auto', array(
    'uses' => 'FertilizerController@ajaxAutocomplete',
    'middleware' => [
        'auth'
    ]
));

Route::post('/load-main-users', array(
    'uses' => 'MainController@getAutocompleteUsers',
    'middleware' => [
        'auth'
    ]
));

/**
 * Administrator Upload Files
 */
Route::get('/upload-layer', array(
    'as' => 'upload.layer',
    'uses' => 'UploadController@index',
    'middleware' => [
        'auth'
    ]
));

Route::get('/upload-layer/filter/{query?}', array(
    'as' => 'upload-layer-filter',
    'uses' => 'UploadController@filterFertilityMap',
    'middleware' => [
        'auth'
    ]
));

Route::post('/upload-layer/destroy/{id?}', array(
    'as' => 'upload.layer.destroy',
    'uses' => 'UploadController@destroy',
    'middleware' => [
        'auth'
    ]
));

Route::get('/upload-layer/export', array(
    'as' => 'upload.layer.export',
    'uses' => 'UploadController@export',
    'middleware' => [
        'auth',
        'permission'
    ]
));
Route::get('/upload-layer/jsonData', 'UploadController@create');

Route::post('/upload-layer/process-export', array(
    'as' => 'upload.layer.process.export',
    'uses' => 'UploadController@processExport'
));

// Route::post('/import-data/ajax-autocomplete','ImportController@ajaxAutocomplete');
Route::group([
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_folder_layer'
], function ($router)
{
    Route::get('/admin/folders/create-layer', 'FolderController@createLayer');
    
    Route::post('/admin/folders/store-layer', array(
        'as' => 'admin.folders.storeLayer',
        'uses' => 'FolderController@storeLayer'
    ));
    
    Route::get('/admin/folders/edit-layer/{layer_id}', 'FolderController@editLayer');
    
    Route::post('/admin/folders/update-layer/{layer_id}', array(
        'as' => 'admin.folders.updateLayer',
        'uses' => 'FolderController@updateLayer'
    ));
    
    Route::put('/admin/folders/change-folder', array(
        'as' => 'admin.folders.changeFolder',
        'uses' => 'FolderController@changeFolder'
    ));
    
    Route::post('/admin/folders/delete-folders', array(
        'as' => 'admin.folders.deleteFolder',
        'uses' => 'FolderController@deleteFolder'
    ));
    
    $router->resource('admin/folders', 'FolderController');
    
    Route::get('/admin/folders/json-tree', array(
        'as' => 'admin.folders.jsonTree',
        'uses' => 'FolderController@jsonTree'
    ));
});

Route::get('/admin/folder-import', array(
    'as' => 'admin.folders.import',
    'uses' => 'FolderController@import',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_folder_layer'
));

/**
 * *******************************************
 * ************** PRESENT MAP ***************
 * ********************************************
 */
Route::post('/api/show-map', [
    'as' => 'api.show.map',
    'uses' => 'MapController@showMap'
]);

/**
 * ***********Organic Matter******************
 */
Route::get('admin/organicmatter/byproduct', array(
    'as' => 'admin.organicmatter.byproduct',
    'uses' => 'OrganicMatterController@byProduct',
    'middleware' => [
        'auth'
    ]
));
Route::post('admin/organicmatter/get-data-byproduct', array(
    'uses' => 'OrganicMatterController@getDataByProduct',
    'middleware' => [
        'auth'
    ]
));
Route::get('admin/organicmatter/greenmanure', array(
    'as' => 'admin.organicmatter.greenmanure',
    'uses' => 'OrganicMatterController@greenManure',
    'middleware' => [
        'auth'
    ]
));
Route::post('admin/organicmatter/get-data-greenmanure', array(
    'uses' => 'OrganicMatterController@getDataGreenManure',
    'middleware' => [
        'auth'
    ]
));
Route::get('admin/organicmatter/compost', array(
    'as' => 'admin.organicmatter.compost',
    'uses' => 'OrganicMatterController@compost',
    'middleware' => [
        'auth'
    ]
));
Route::post('admin/organicmatter/get-data-compost', array(
    'uses' => 'OrganicMatterController@getDataCompost',
    'middleware' => [
        'auth'
    ]
));
Route::post('admin/organicmatter/get-data-fertilizer-efficiency', array(
    'uses' => 'OrganicMatterController@getDataFertilizerEfficiency',
    'middleware' => [
        'auth'
    ]
));
Route::get('map-prefix/nito/{user_id}', array(
    'uses' => 'MapController@showNitoMap',
    'middleware' => [
        'auth'
    ]
));

Route::get('map-prefix/export/{layer_id}', array(
    'uses' => 'MapController@showExportMap',
    'middleware' => [
        'auth'
    ]
));

Route::post('map-prefix/show-selection', array(
    'uses' => 'MapController@showSelectionMap',
    'middleware' => [
        'auth'
    ]
));

Route::post('map-prefix/store-guest-map', array(
    'uses' => 'MapController@storeGuestMap',
    'middleware' => [
        'auth'
    ]
));

Route::post('generate-guess-direction', array(
    'uses' => 'MapController@generateGuessDirection',
    'middleware' => [
        'auth'
    ]
));

Route::post('display-final', array(
    'uses' => 'MapController@displayFinal',
    'middleware' => [
        'auth'
    ]
));

Route::post('map-prefix/confirm-data', array(
    'as' => 'map.confirm',
    'uses' => 'MapController@confirmData',
    'middleware' => [
        'auth'
    ]
));

/**
 * *************Fertilization Price***************
 */
Route::group([
    'middleware' => [
        'auth',
        'permission',
        'agreed'
    ],
    'roles' => 'auth_fertilizer_price'
], function ($router)
{
    $router->resource('admin/fertilizationprice', 'FertilizationPriceController');
});

Route::get('admin/fertilization-price/index', array(
    'as' => 'admin.fertilizationprice.index',
    'uses' => 'FertilizationPriceController@index',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_fertilizer_price'
));
Route::get('admin/get-grid-data-price', array(
    'uses' => 'FertilizationPriceController@priceGetGrid',
    'middleware' => [
        'auth'
    ]
));
Route::post('admin/fertilization-price/store', array(
    'as' => 'admin/fertilization-price/store',
    'uses' => 'FertilizationPriceController@store',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_fertilizer_price'
));
Route::get('admin/download-management', array(
    'as' => 'admin.downloadmanagement',
    'uses' => 'DownloadManagementController@index',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_purchasing_management'
));
Route::any('download-management/download-grid', array(
    'uses' => 'DownloadManagementController@downloadGrid',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_purchasing_management'
));
Route::post('admin/download-management/store', array(
    'as' => 'admin.downloadmanagement.store',
    'uses' => 'DownloadManagementController@store',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_purchasing_management'
));
Route::get('admin/download-management/edit/{id}', array(
    'as' => 'admin.downloadmanagement.edit',
    'uses' => 'DownloadManagementController@edit',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_purchasing_management'
));
Route::post('admin/download-management/update', array(
    'as' => 'admin.downloadmanagement.update',
    'uses' => 'DownloadManagementController@update',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_purchasing_management'
));

Route::post('admin/download-management/search-download', array(
    'as' => 'admin.downloadmanagement.search',
    'uses' => 'DownloadManagementController@searchDownload',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_purchasing_management'
));
Route::any('admin/download-management/export-download', array(
    'as' => 'admin.downloadmanagement.export',
    'uses' => 'DownloadManagementController@exportDownload',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_purchasing_management'
));
Route::post('admin/download-management/get-list-data-csv', array(
    'as' => 'admin.downloadmanagement.getlistDataCSV',
    'uses' => 'DownloadManagementController@getlistDataCSV',
    'middleware' => array(
        'auth',
        'permission'
    ),
    'roles' => 'auth_purchasing_management'
));
Route::get('fertilization-price/afterDelete', array(
    'uses' => 'FertilizationPriceController@afterDelete',
    'middleware' => [
        'auth',
        'permission'
    ]
));
Route::post('fertilization-price/delete-fertilization', array(
    'as' => 'fertilization-price/delete-fertilization',
    'uses' => 'FertilizationPriceController@deleteFertilization',
    'middleware' => [
        'auth',
        'permission'
    ],
    'roles' => 'auth_fertilizer_price'
));
Route::post('fertilizer/validate-specification', array(
    'as' => 'fertilizer.validate.specification',
    'uses' => 'MapController@validateSpecificationFertilizer',
    'middleware' => [
        'auth'
    ]
));
Route::get('fertilizer-edit/{fertilizerMapId}', array(
    'uses' => 'MapController@editFertilizerMap',
    'middleware' => [
        'auth',
        'permission'
    ]
));