<?php

namespace Gis\Helpers;
/**
 * Created by PhpStorm.
 * User: namdv
 * Date: 8/31/2015
 * Time: 2:46 PM
 */
class LoggingAction
{
    /**
     * Declaration to switch between Mode 1 and Mode 2
     * In order to enable mode 1, set value =1 and value -2 for mode 2
     */
    const MODE = 1;
    /**
     * Mode1: Login action of general user of administrator
     */
    const MODE1_USER_LOGIN = "User login to application";
    /**
     * Mode1: Login action of guest user
     */
    const MODE1_GUEST_LOGIN ="Guest login to application";
    /**
     * Mode1: User logout
     */
    const MODE1_USER_LOGOUT = "User logout";
    /**
     * Mode1: User download fertilizer map
     */
    const MODE1_DOWNLOAD_FERTILIZER ="User download fertilizer map";
    /**
     * Administrator registers a new user
     */
    const MODE2_REGISTER_USER ="Administrator registers a new user";
    /**
     * Administrator updates an user
     */
    const MODE2_UPDATE_USER ="Administrator updates an user";
    /**
     * Administrator deletes an user
     */
    const MODE2_DELETE_USER ="Administrator deletes an user";
    /**
     * User changes username & password
     */
    const MODE2_CHANGE_USERNAME_PASSWORD ="User changes username & password";
    /**
     *Administrator creates new an user group
     */
    const MODE2_ADD_USER_GROUP = "Administrator creates new an user group";
    /**
     * Administrator updates an user group
     */
    const MODE2_UPDATE_USER_GROUP ="Administrator updates an user group";
    /**
     * Administrator deletes an user group
     */
    const MODE2_DELETE_USER_GROUP ="Administrator deletes an user group";
    /**
     * Update content of footer
     */
    const MODE2_UPDATE_FOOTER_CONTENT ="Update content of footer";
    /**
     * Add new a help link
     */
    const MODE2_ADD_HELP_LINK ="Add new a help link";
    /**
     * Update a help link
     */
    const MODE2_UPDATE_HELP_LINK ="Update a help link";
    /**
     * Delete a help link
     */
    const MODE2_DELETE_HELP_LINK ="Delete a help link";
    /**
     * User opens a help link
     */
    const MODE2_OPEN_HELP ="User opens a help link";

    /**
     * Specify permission for user group
     */
    const MODE2_AUTHORIZATION ="Specify permission for user group";
    /**
     * User agrees with term of use
     */
    const MODE2_AGREE_TERM ="User agrees with term of use";
    /**
     * User doesn't agree with term of use
     */
    const MODE2_DONNOT_AGREE_TERM ="User doesn't agree with term of use";
    /**
     * Add new a folder
     */
    const MODE2_ADD_FOLDER = "Add new a folder";
    /**
     * Upload a fertility map
     */
    const MODE2_UPLOAD_FERTILITY_MAP = "Upload a fertility map";
    /**
     * Grant access right for user to specified folder
     */
    const MODE2_GRANT_ACCESS_RIGHT_TO_FOLDER ="Grant access right for user to specified folder";

    /**
     *Update information of folder
     */
    const MODE2_UPDATE_FOLDER ="Update folder information or set permission for folder";

    /**
     * Add new a terrain layer
     */
    const MODE2_ADD_TERRAIN_LAYER ="Add new a terrain layer";
    /**
     * Change ordinal number for layer
     */
    const MODE2_CHANGE_ORDINAL_NUMBER_FOR_LAYER ="Change ordinal number for layer";
    /**
     * Change ordinal number for folder
     */
    const MODE2_CHANGE_ORDINAL_NUMBER_FOR_FOLDER ="Change ordinal number for folder";
    /**
     * Delete layer
     */
    const MODE2_DELETE_LAYER ="Delete layer";
    /**
     * Update layer
     */
    const MODE2_UPDATE_LAYER ="Update layer";
    /**
     * Delete folder
     */
    const MODE2_DELETE_FOLDER ="Delete folder";
    /**
     * Create new a user-defined fertilization and system fertilization
     */
    const MODE2_ADD_FERTILIZATION_STANDARD ="Create new a user-defined fertilization and system fertilization";
    /**
     * Update a user-defined fertilization and system fertilization
     */
    const MODE2_UPDATE_FERTILIZATION_STANDARD ="Update a user-defined fertilization and system fertilization";
    /**
     * Copy a user-defined fertilization and system fertilization
     */
    const MODE2_COPY_FERTILIZATION_STANDARD ="Copy a user-defined fertilization or system fertilization";
    /**
     * Copy a user-defined fertilization and system fertilization
     */
    const ACTION_COPY_SYSTEM_FERTILIZATION_STANDARD ="Copy a system fertilization";
    /**
     * Delete a user-defined fertilization and system fertilization
     */
    const MODE2_DELETE_FERTILIZATION_STANDARD ="Delete a user-defined fertilization and system fertilization";
    /**
     * Grant access right for user to fertilization standard
     */
    const MODE2_GRANT_ACCESS_RIGHT_FERTILIZATION_STANDARD ="Grant access right for user to fertilization standard";
    /**
     * Create new a standard fertilization for crops
     */
    const MODE2_ADD_STANDARD_FERTILIZATION_FOR_CROPS = "Create new a standard fertilization for crops";

    /**
     * Update a standard fertilization for crops
     */
    const MODE2_UPDATE_STANDARD_FERTILIZATION_FOR_CROPS ="Update a standard fertilization for crops";
    /**
     * Delete a standard fertilization for crops
     */
    const MODE2_DELETE_STANDARD_FERTILIZATION_OF_CROPS ="Delete a standard fertilization for crops";
    /**
     * Copy a standard fertilization for crops
     */
    const MODE2_COPY_STANDARD_FERTILIZATION_FOR_CROPS ="Copy a standard fertilization for crops";
    /**
     * Update N,P,K for a crops
     */
    const MODE2_UPDATE_NPK_FOR_CROPS ="Update N,P,K for a crops";
    /**
     * User performs to get username&password again
     */
    const MODE2_FORGOT_USERNAME_PASSWORD ="User performs to get username&password again";
    /**
     * User resets username & password
     */
    const MODE2_RESET_USERNAME_PASSWORD ="User resets username & password";
    /**
     * Admin export fertility map for user
     */
    const MODE2_EXPORT_FERTILITY ="User resets username & password";
    /**
     * Hide layer
     */
    const MODE2_HIDE_LAYER ="Hide layer";
    /**
     * Show layer
     */
    const MODE2_SHOW_LAYER ="Show layer";
    /**
     * User select mode to create new a fertilizer map
     */
    const MODE2_SELECT_MODE_TO_CREATE_FERTILIZER ="User select mode to create new a fertilizer map";
    /**
     * Zoom-in fertility map
     */
    const MODE2_ZOOM_IN_FERTILITY_MAP ="Zoom-in fertility map";
    /**
     * Zoom-out fertility map
     */
    const MODE2_ZOOM_OUT_FERTILITY_MAP ="Zoom-out fertility map";
    /**
     * Zoom-in fertilizer map
     */
    const MODE2_ZOOM_IN_FERTILIZER_MAP ="Zoom-in fertilizer map";
    /**
     * Zoom-out fertilizer map
     */
    const MODE2_ZOOM_OUT_FERTILIZER__MAP ="Zoom-out fertilizer map";
    /**
     * Specify condition to create fertilizer map
     */
    const MODE2_SPECIFY_CONDITION_TO_CREATE_FERTILIZER_MAP ="Specify condition to create fertilizer map";
    /**
     * Confirm condition to create fertilizer map
     */
    const MODE2_CONFIRM_CONDITION_TO_CREATE_FERTILIZER_MAP ="Confirm condition to create fertilizer map";
    /**
     * Show scale bar
     */
    const MODE2_SHOW_SCALE_BAR ="Show scale bar";
    /**
     * Hide scale bar
     */
    const MODE2_HIDE_SCALE_BAR ="Hide scale bar";
    /**
     * Show zoom-in, zoom-out toolbar
     */
    const MODE2_SHOW_ZOOM_IN_TOOLBAR ="Show zoom-in, zoom-out toolbar";
    /**
     * Hide zoom-in, zoom-out toolbar
     */
    const MODE2_HIDE_ZOOM_IN_TOOLBAR ="Hide zoom-in, zoom-out toolbar";
    /**
     * Hide legend
     */
    const MODE2_HIDE_LEGEND ="Hide legend";
    /**
     * Show legend
     */
    const MODE2_SHOW_LEGEND ="Show legend";
    /**
     * User change volume of main fertilizer
     */
    const MODE2_CHANGE_VOLUME_MAIN_SUB_FERTILIZER ="User change volume of main&sub fertilizer";
    /**
     * View properties of fertilizer map
     */
    const MODE2_VIEW_FERTILIZER_PROPERTIES ="View properties of fertilizer map";
    /**
     * Update parameters of fertilizer map
     */
    const MODE2_UPDATE_PARAMETERS_OF_FERTILIZER_MAP ="Update parameters of fertilizer map";
    /**
     * Download fertilizer
     */
    const MODE2_DOWNLOAD_FERTILIZER_MAP ="Download fertilizer map";
    /**
     * Add new a price unit of fertilizer
     */
    const MODE2_ADD_PRICE_UNIT_OF_FERTILIZER_MAP ="Add new price unit of fertilizer map";
    /**
     * Edit a price unit of fertilizer
     */
    const MODE2_UPDATE_PRICE_UNIT_OF_FERTILIZER_MAP ="Update a price unit of fertilizer map";
    /**
     * Delete a price unit of fertilizer
     */
    const MODE2_DELETE_PRICE_UNIT_OF_FERTILIZER_MAP ="Delete a price unit of fertilizer map";
    /**
     * Open form to Export PDF
     */
    const MODE2_EXPORT_PDF ="User open form to export to PDF file";
    /**
     * Time
     */
    const LOG_TIME ="Time";

    /**
     * Action
     */
    const LOG_ACTION ="Action";
    /**
     * User changes state of scale bar, zoom-in&zoom-out toolbar, legend bar
     */
    const MODE2_USER_CHANGE_STATE ="User changes state of scale bar, zoom-in&zoom-out toolbar, legend bar";
    /**
     * Move layer to bin folder
     */
    const MODE2_MOVE_LAYER_TO_BIN ="Move layer to bin folder";
    /**
     * Delete fertility map
     */
    const MODE2_DELETE_FERTILITY_MAP ="Delete fertility map";
    /**
     * Delete fertilizer map
     */
    const MODE2_DELETE_FERTILIZER_MAP ="Delete fertilizer map";
    /**
     * Create new a layer
     */
    const MODE2_ADD_LAYER ="Create new a layer";
    /**
     * Update Fertilization detail
     */
    const MODE2_UPDATE_FERTILIZATION_DETAIL ="Update System fertilization detail";
    /**
     * Update colors, main, sub of fertilizer map
     */
    const MODE2_UPDATE_COLORS_MAIN_SUB_FERTILIZER_MAP ="Update colors, main, sub of fertilizer map";
    /**
     * User selects mode all to create new a fertilizer map
     */
    const ACTION_MODE_SELECTION_ALL = "User selects mode ALL and open form to create new a fertilizer map";

    /**
     * User selects mode condition to create new a fertilizer map
     */
    const ACTION_MODE_SELECTION_CONDITION = "User selects mode CONDITION and open form to create new a fertilizer map";
    /**
     * User open form to change color or volume for fertilizer map
     */
    const ACTION_OPEN_CHANGING_COLOR = "User opens form to change color for fertilizer map";
    /**
     * User submit change color or volume for fertilizer
     */
    const ACTION_SUBMIT_CHANGING_COLOR = "User saves changing color or volume for fertilizer";
    /**
     * Confirm condition to edit fertilizer map
     */
    const ACTION_CONFIRM_CONDITION_TO_EDIT_FERTILIZER_MAP ="Confirm condition to edit fertilizer map";
    /**
     * User exports to fertilizer map to PDF file
     */
    const ACTION_EXPORT_TO_PDF_FILE ="User exports fertilizer map to PDF file";
    /**
     * User predicts shortage location of fertilizer
     */
    const ACTION_OPEN_PREDICTION_FORM = "User opens form to predict shortage location of fertilizer";
    /**
     * User predicts shortage location of fertilizer
     */
    const ACTION_PREDICT_SHORTAGE_LOCATION_FERTILIZER ="User predicts shortage location of fertilizer";
    /**
     * User opens download history
     */
    const ACTION_OPEN_DOWNLOAD_HISTORY ="User opens download history";
    /**
     * User update a download history
     */
    const ACTION_UPDATE_DOWNLOAD_HISTORY ="User updates a download history data";
    /**
     * User deletes one or many download
     */
    const ACTION_DELETE_DOWNLOAD_HISTORY ="User deletes one or many items of download history";
    /**
     * User exports download history data
     */
    const ACTION_EXPORT_DOWNLOAD_HISTORY ="User exports download hisotry to CSV file";
    /**
     * Open initial screen
     */
    const ACTION_OPEN_INITIAL_SCREEN ="User opens initial screen";
    /**
     * User click on a layer
     */
    const ACTION_CLICK_LAYER ="User clicks on a layer";
    /**
     * Update fertilizer
     */
    const ACTION_UPDATE_FERTILIZER_STANDARD = "User updates fertilizer standard";
    /**
     * Create fertilizer standard
     */
    const ACTION_CREATE_FERTILIZER_STANDARD = "User create new a fertilizer standard";
    /**
     * Add crops for fertilization standard
     */
    const ACTION_ADD_CROPS_FOR_FERTILIZATION_STANDARD ="User adds crops for fertilizer standard";
    /**
     * Update detail information for crops
     */
    const ACTION_UPDATE_DETAIL_INFO_FOR_CROPS ="User updates detail information for crops of fertilizer standard";
    /**
     * Copy crops information to another fertilizer standard
     */
    const ACTION_COPY_CROPS_INFORMATION_TO_ANOTHER_FERTILIZER_STANDARD = "Copy crops information to a fertilizer standard";
    /**
     * Delete crops of fertilizer standard
     */
    const ACTION_DELETE_CROPS_OF_FERTILIZER_STANDARD ="Delete crops of fertilizer standard";
    /**
     * Create a record for history of download fertilizer
     */
    const ACTION_CREATE_HISTORY_DOWNLOAD = "Create a record for history of download fertilizer map";
    /**
     * map created conditions specified screen
     */
    const ACTION_OPEN_FORM_TO_CREATE_FERTILIZER_MAP ="Open specified screen to create conditions for fertilizer map ";
    /**
     *Update information of layer
     */
    const ACTION_UPDATE_LAYER ="Update layer information";
}