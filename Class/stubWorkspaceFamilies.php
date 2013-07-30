<?php
namespace Dcp\Family {
	/** répertoire  */
	class Simplefolder extends \Dcp\Workspace\SimpleFolder { const familyName="SIMPLEFOLDER";}
	/** espace de travail  */
	class Workspace extends \Dcp\Workspace\WorkSpace { const familyName="WORKSPACE";}
	/** fichier  */
	class Simplefile extends \Dcp\Workspace\SimpleFile { const familyName="SIMPLEFILE";}
}
namespace Dcp\AttributeIdentifiers {
	/** répertoire  */
	class Simplefolder extends Dir {
	}
	/** espace de travail  */
	class Workspace extends Dir {
		/** [text] Espace */
		const wsp_ref='wsp_ref';
		/** [docid] id administrateur */
		const wsp_idadmin='wsp_idadmin';
		/** [text] Administrateur */
		const wsp_admin='wsp_admin';
		/** [action] Affectation */
		const wsp_affect='wsp_affect';
		/** [color] couleur intercalaire */
		const gui_color='gui_color';
	}
	/** fichier  */
	class Simplefile {
		/** [tab] fichier */
		const sfi_tab_file='sfi_tab_file';
		/** [frame] Cadre fichier */
		const sfi_fr_file='sfi_fr_file';
		/** [file] fichier */
		const sfi_file='sfi_file';
		/** [tab] Description */
		const sfi_tab_desc='sfi_tab_desc';
		/** [frame] Description */
		const sfi_frdesc='sfi_frdesc';
		/** [file] PDF */
		const sfi_pdffile='sfi_pdffile';
		/** [text] nom du fichier */
		const sfi_title='sfi_title';
		/** [text] titre */
		const sfi_titlew='sfi_titlew';
		/** [text] sujet */
		const sfi_subject='sfi_subject';
		/** [text] mots-clés */
		const sfi_keyword='sfi_keyword';
		/** [longtext] résumé */
		const sfi_description='sfi_description';
		/** [text] type court */
		const sfi_mimetxtshort='sfi_mimetxtshort';
		/** [text] type */
		const sfi_mimetxt='sfi_mimetxt';
		/** [text] type système */
		const sfi_mimesys='sfi_mimesys';
		/** [image] icone */
		const sfi_mimeicon='sfi_mimeicon';
		/** [double] taille */
		const sfi_filesize='sfi_filesize';
		/** [int] nombre de pages */
		const sfi_pages='sfi_pages';
		/** [image] miniature */
		const sfi_thumb='sfi_thumb';
		/** [text] fichier en édition ? */
		const sfi_inedition='sfi_inedition';
		/** [text] version */
		const sfi_version='sfi_version';
	}
}
