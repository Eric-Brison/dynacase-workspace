<?php
/*
 * @author Anakeen
 */

namespace Dcp\Workspace;

include_once ("GENERIC/generic_util.php");

class Utils
{
    /**
     * set attribute search mode
     *
     * @param string $split [REGEXP|FULL]
     */
    public static function setSearchMode(&$action, $famid, $mode)
    {
        return setFamilyParameter($action, $famid, 'WS_MODESEARCH', $mode);
    }
    /**
     * return  if search is also in inherit famileis
     *
     * @return string [Y|N] Yes/No  according to family
     */
    public static function getSearchMode(\Action & $action, $famid = "")
    {
        return getFamilyParameter($action, $famid, "WS_MODESEARCH", "REGEXP");
    }
}
