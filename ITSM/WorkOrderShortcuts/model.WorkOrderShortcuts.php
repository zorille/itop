<?php
//
// Menus
//
class MenuCreation_WorkOrderShortcuts extends ModuleHandlerAPI
{
        public static function OnMenuCreation()
        {
                global $__comp_menus__; // ensure that the global variable is indeed global !
                $__comp_menus__['WorkOrderManagement'] = new MenuGroup('WorkOrderManagement', 40 , null, UR_ACTION_MODIFY, UR_ALLOWED_YES, null);
        }
} // class MenuCreation_WorkOrderShortcuts