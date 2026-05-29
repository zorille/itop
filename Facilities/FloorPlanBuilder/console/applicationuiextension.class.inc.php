<?php

namespace NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Console\Extension;

use AbstractApplicationUIExtension;
use Combodo\iTop\Application\WebPage\WebPage;
use Dict;
use Exception;
use Location;
use utils;

class ApplicationUIExtension extends AbstractApplicationUIExtension
{
    private function retrieveLang(): string
    {
        return Dict::GetUserLanguage();
    }

    private function retrieveAllLanguages(): array
    {
        $aAll = [];
        foreach (array_keys(Dict::GetLanguages()) as $sLangCode) {
            $aAll[$sLangCode] = Dict::ExportEntries('');
        }

        return $aAll;
    }

    /**
     * @throws Exception
     */
    public function OnDisplayRelations($oObject, WebPage $oPage, $bEditMode = false): void
    {
        // On ne veut l'onglet que sur Location (et typiquement pas en mode édition si tu préfères)
        if (!($oObject instanceof Location)) {
            return;
        }

        $lang = $this->retrieveLang();

        if (!in_array($lang, ['FR FR', 'EN US'])) {
            return;
        }

        Dict::SetUserLanguage('FR FR');
        $frTabTitle = Dict::S('FloorPlanBuilder:Tab:Title');
        Dict::SetUserLanguage('EN US');
        $enTabTitle = Dict::S('FloorPlanBuilder:Tab:Title');
        Dict::SetUserLanguage($lang);

        $parts = parse_url($_SERVER['REQUEST_URI']);
        parse_str($parts['query'], $query);

        $uri = $_SERVER['REQUEST_URI'];
        $uriWithoutObjProps = preg_replace("/&ObjectProperties=[^&#]+/", "", $uri);

        if (!isset($query['ObjectProperties'])) {
            header("Location: {$uriWithoutObjProps}&ObjectProperties=tab_UIPropertiesTab#ObjectProperties=tab_UIPropertiesTab");
        } else if ($query['ObjectProperties'] === "tab_{$frTabTitle}" && $lang === 'EN US') {
            header("Location: {$uriWithoutObjProps}&ObjectProperties=tab_{$enTabTitle}#ObjectProperties=tab_{$enTabTitle}");
        } else if ($query['ObjectProperties'] === "tab_{$enTabTitle}" && $lang === 'FR FR') {
            header("Location: {$uriWithoutObjProps}&ObjectProperties=tab_{$frTabTitle}#ObjectProperties=tab_{$frTabTitle}");
        }

        $oConfig = utils::GetConfig();

        $enabled = $oConfig?->GetModuleSetting('FloorPlanBuilder', 'enabled', true);

        // Crée / active l'onglet (si l'onglet n'existe pas, iTop le crée)
        $tabText = Dict::S('FloorPlanBuilder:Tab:Title');
        if ($enabled) {
            $oPage->SetCurrentTab($tabText);
        }

        $sAssetsBaseUrl = utils::GetAbsoluteUrlAppRoot() . '/env-' . utils::GetCurrentEnvironment() . '/FloorPlanBuilder/common/assets';
        $sWsUrl = utils::GetAbsoluteUrlModulePage('FloorPlanBuilder', 'pages/webservice.php');

        $langKeys = json_encode($this->retrieveAllLanguages(), JSON_UNESCAPED_UNICODE);

        $footprintColors = $oConfig?->GetModuleSetting('FloorPlanBuilder', 'footprint_colors', [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8',
            '#F06292', '#AED581', '#FFD54F', '#4DB6AC', '#BA68C8'
        ]);

        $jsonFootprintColors = json_encode($footprintColors);

        if ($enabled) {
            $layers = json_encode([]);
            // Point de montage pour ton builder (PHP -> JS)
            $oPage->add(<<<HTML
            <style>
                .loader {
                    width: 48px;
                    height: 48px;
                    border: 5px solid #FFF;
                    border-bottom-color: #FF3D00;
                    border-radius: 50%;
                    display: inline-block;
                    box-sizing: border-box;
                    animation: rotation 1s linear infinite;
                }
                
                .tooltip {
                      position: fixed;
                      border-radius: 4px;
                      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                      z-index: 9999;
                      max-width: 400px;
                      max-height: 400px;
                      overflow: auto;
                    
                      .tooltip-loader {
                            color: black;
                      }
                }

                @keyframes rotation {
                    0% {
                        transform: rotate(0deg);
                    }
                    100% {
                        transform: rotate(360deg);
                    }
                }
            </style>
            
            <room-builder 
                room-id="{$oObject->GetKey()}"
                room-name="{$oObject->GetName()}"
                layers="{$layers}"
                radius="5"
                language="{$lang}"
                use-itop-form
                is-data-loading
                footprint-colors='{$jsonFootprintColors}'
            >
                <span slot="loader" class="loader"></span>
            </room-builder>
            
            <script type="module" src="{$sAssetsBaseUrl}/room-builder.webcomponent.js"></script>
            <link rel="stylesheet" href="{$sAssetsBaseUrl}/room-builder.webcomponent.css">
            
            <script type="module"> RoomBuilder.tradKeys = {$langKeys}; </script>

            <script> window.LOCATION_BUILDER_WS = "$sWsUrl"; </script>
            <script type="module" src="{$sAssetsBaseUrl}/room-builder.js"></script>
            <script>
                window.addEventListener('DOMContentLoaded', () => {
                    window.LocationBuilder?.mount?.('room-builder', '{$tabText}');
                });
            </script>
            HTML);
        }
    }

    /**
     * Invoked when an object is being displayed (view or edit), just after the main tab.
     * Typically used to tweak the "Properties" tab.
     */
    public function OnDisplayProperties($oObject, $oPage, $bEditMode = false): void
    {
        // No-op (tu peux ignorer ou filtrer sur Location comme tu l'as fait ailleurs)
    }

    /**
     * Invoked when building the Actions menu for a single object or a list of objects.
     * Must return an array: [ 'Label' => 'URL', ... ]
     */
    public function EnumAllowedActions($oSet)
    {
        return [];
    }

    /**
     * Reserved verb, not yet called by the framework (mais doit exister sur l’interface).
     * Must return an array of attribute codes used by your extension.
     */
    public function EnumUsedAttributes($oObject)
    {
        return [];
    }

    /**
     * Invoked when the object is displayed alone or within a list.
     * Must return one of: HILIGHT_CLASS_CRITICAL / WARNING / OK / NONE
     */
    public function GetHilightClass($oObject)
    {
        return HILIGHT_CLASS_NONE;
    }

    /**
     * Reserved verb, not yet called by the framework (mais doit exister sur l’interface).
     * Must return a string: path of the icon relative to the modules directory.
     * Example: 'location-builder/asset/img/builder.svg'
     */
    public function GetIcon($oObject)
    {
        return '';
    }

    /**
     * Invoked when the end-user clicks on Modify from the object edition form.
     * Called after standard form changes have been applied, before DB write.
     */
    public function OnFormSubmit($oObject, $sFormPrefix = '')
    {
        // No-op
    }

    /**
     * Invoked when the end-user clicks on Cancel from the object edition form.
     * Useful for cleaning temporary uploaded files, etc.
     */
    public function OnFormCancel($sTempId)
    {
        // No-op
    }
}
