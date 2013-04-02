<?php 
$_SESSION["r_id"] = 4;
$_SESSION["x_id"] = 0;
$_SESSION["vers"] = "1.2";
/*
 * options dosponibles: 
 * site : blog, worknet(CRM), Gestion (Boutique)
 * compta : devis, factures, bilan
 * lalie : Newsletter
 * groupe : utilisateurs d'adeli
 * picto : editeur d'image
 * stats : statistiques d'accès
 * mail : client IMAP
 * agenda : Agenda
 */ 
$opt="site,compta,lalie,groupe,stats,reglages";

// serveur SQL
$host = "localhost";
$base = "";
$login = "";
$passe = "";

// si différent pour lalie
$dhost = "localhost";
$dbase = "";
$dlogin = "";
$dpasse = "";

// Afficher ou non les mots de passes
$aff_pass = true;

// prefixe
$prefixe="atouts_";

// Table pour lalie
$laliedb="adeli_lalie";
// Table pour les rapports lalie
$lalierp="adeli_lalie_rapports";

// Domaine de base pour les liens de fichiers
$link_domain='fichiers.urbancube.fr';

// Table pour l'agenda
//$agenda_base = "test_agenda";


// Table pour les messages worknet
$message_base = "adeli_messages";


// Table pour compta
$compta_base = "adeli_compta";
// Etat des devis/factures
$custom_defstat = array('EN COURS','PAYE','ANNULE','PAS FINI');
$custom_colorstatut = array('999999','00FF00','990000','000099');

$edit_font_site = array('Arial','Verdana');
$edit_size_site = array('1','2','3','h2');

$edit_font_lalie = array('Arial','Verdana');
$edit_size_lalie = array('10px','12px','14px','h1','h2');


// Menu pour le site
$menu=array(
  "worknet"=>array("clients","fournisseurs","adeli_messages","mod&egrave;les de messages"=>"adeli_message_template","projets","suivi des projets"=>"actions"),
  "site"=>array("blog","news","contact"),
  "test"=>array("blocknote","Test multiple"=>"z-test","Test unique"=>"z-reglage"),
  "gestion"=>array("gestion_caisse","gestion_rappel","gestion_langue","gestion_rayons","gestion_articles","adeli_compta")
);

// Boutique gestion
$depth_gestion["rayon"]=1;
$gestion_articles_taille_name = "Cat&eacute;gorie";
$gestion_articles_couleur_name = "Type";
$gestion_articles_desc_name = "Description";
$gestion_articles_taille_liste = array('forfait','1 heure','1 jour','1 semaine','1 mois', '1 an');
//$gestion_articles_couleur_liste = array('unique','test');
$fournisseurs_db="fournisseurs";
$firescom="fournisseurs";

// encodage des mots de passe avec mysql password()
$pass_sql_encode = array('z-test');

// Type de tables : nom_de_table => valeur
// valeurs possible (séparée par une virgule : txt,plain,nonew,active,dir,ico,noedit)
$types= array(  
	"z-reglage"=>"txt,nonew",
	"blog"=>"txt,plain,ico,dir",
);

// Chemin associés aux tables
$fichiers = array(
	"blog"=>array(
		"aperçu"=>array("../blog/","$edit.ico"),
		"fichiers"=>array("../blog/$edit/",".dir",'image/jpg,image/png,image/gif',true)
	)
);

// Nom affiché des champs SQL
$alias=array(
	"z-test"=>array(
		"clients"=>"client nom et email",
	)
);

// Fonctionnalité des champs SQL
/*
 * table_cle_champ  : un select ayant comme valeur "cle" affichant "champ" depuis "table"
 * table_cle_champ_ch  : une liste de chexbox ayant comme valeur "cle" affichant "champ" depuis "table"
 * table_cle_champ/champ2/champ3  : une liste de chexbox ayant comme valeur "cle" affichant "champ champ2 champ3" depuis "table"
 * _nom : select avec les valeurs deja utilisées pour le meme champ
 * bool : vrai / faux
 * code : textarea
 *  
 */
$r_alias=array(
	"blog"=>array(
		"categorie"=>"_categorie",
	),
	"z-test"=>array(
		"clients"=>"clients_id_nom/email",
	)
);

// preision des actions de champs avec close WHERE
$lim=date('Y-m-d H:i:s', strtotime("-15 days"));
$w_alias=array(
	"z-test"=>array(
		"clients_id_nom_ch"=>"`active`=1 AND `last`>='$lim'",
	),
);

$multiple_depend=array(
	"z-test"=>array("gestion_langue","id","nom","langage","ref_int"),
);

// Autoremplissage d'un champs à partie de la valeur d'un autre en appliquant une fonction
$autocomplete = array(
"z-test"=>array(
  "test"=>array("sous_chapitre","validlink"),
  ),
);

$mapcoord["z-test"]="data/france.jpg";

$dirfiles["easygals"] = "easygals";
$meta_dir["easygals"] = "oui";
$depth_dir["easygals"] = 1;

$dirfiles["fonds"] = "bgs";
$meta_dir["fonds"] = "non";
$depth_dir["fonds"] = 0;
