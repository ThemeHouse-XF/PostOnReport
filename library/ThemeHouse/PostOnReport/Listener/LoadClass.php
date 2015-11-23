<?php

class ThemeHouse_PostOnReport_Listener_LoadClass extends ThemeHouse_Listener_LoadClass
{
    protected function _getExtendedClasses()
    {
        return array(
            'ThemeHouse_PostOnReport' => array(
                'model' => array(
                    'XenForo_Model_Report',
                ), /* END 'model' */
                'view' => array(
                    'XenForo_ViewAdmin_Option_ListOptions',
                ), /* END 'view' */
            ), /* END 'ThemeHouse_PostOnReport' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassModel($class, array &$extend)
    {
        $loadClassModel = new ThemeHouse_PostOnReport_Listener_LoadClass($class, $extend, 'model');
        $extend = $loadClassModel->run();
    } /* END loadClassModel */

    public static function loadClassView($class, array &$extend)
    {
        $loadClassView = new ThemeHouse_PostOnReport_Listener_LoadClass($class, $extend, 'view');
        $extend = $loadClassView->run();
    } /* END loadClassView */
}