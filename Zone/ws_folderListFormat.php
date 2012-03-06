<?php
/*
 * Format column for folder list
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/
/**
 * Format column for folder list
 *
 */
class wsFolderListFormat
{
    
    static public function getIcon(Doc & $doc, DocCollection & $dir = null)
    {
        $icon = $doc->getIcon('', 20);
        $link = '';
        if (($dir->doctype == 'D') && ($doc->prelid != $dir->initid)) {
            global $action;
            $link = sprintf('<img class="ilink" src="%s">', $action->getImageUrl('link.gif'));
        }
        return sprintf('<img class="icon" src="%s">%s', $icon, $link);
    }
    static public function getMDate(Doc & $doc)
    {
        return strftime("%d %b %Y %H:%M", $doc->revdate);
    }
    static public function getFileSize(Doc & $doc)
    {
        $size = $doc->getValue("sfi_filesize", -1);
        if ($size < 0) $dsize = "";
        else if ($size < 1024) $dsize = _("<1 kb");
        else if ($size < 1048576) $dsize = sprintf(_("%d kb") , $size / 1024);
        else $dsize = sprintf(_("%.01f Mb") , $size / 1048576);
        return $dsize;
    }
    static public function getFileMime(Doc & $doc)
    {
        return $doc->getValue("sfi_mimetxtshort");
    }
    static public function getColumnDescription()
    {
        return array(
            "icon" => array(
                "htitle" => _("icon") ,
                "horder" => "title",
                "issort" => false,
                "method" => "wsFolderListFormat::getIcon(THIS, DIR)"
            ) ,
            "title" => array(
                "htitle" => _("Filename Menu") ,
                "horder" => "title",
                "issort" => false,
                "method" => "::getHtmlTitle()"
            ) ,
            "date" => array(
                "htitle" => _("Modification Date Menu") ,
                "horder" => "date",
                "issort" => false,
                "method" => "wsFolderListFormat::getMDate(THIS)"
            ) ,
            "size" => array(
                "htitle" => _("File Size Menu") ,
                "horder" => "size",
                "issort" => false,
                "method" => "wsFolderListFormat::getFileSize(THIS)"
            ) ,
            "mime" => array(
                "htitle" => _("File Type Menu") ,
                "horder" => "mime",
                "issort" => false,
                "method" => "wsFolderListFormat::getFileMime(THIS)"
            )
        );
    }
}
?>
