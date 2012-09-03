--@license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
--@author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
--@author     Alexandre Mouysset <alexandre.mouysset@surlefil.org>

CREATE TABLE emails (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, sujet, date DEFAULT (CURRENT_TIMESTAMP) );
CREATE TABLE news (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, sujet, date DEFAULT (CURRENT_TIMESTAMP) );
CREATE TABLE donnees_news (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, id_news INT, news, date DEFAULT (CURRENT_TIMESTAMP) );
CREATE TABLE news_modele (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, nom, modele, date DEFAULT (CURRENT_TIMESTAMP) );
CREATE TABLE donnees_email (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, id_email INT, html, pj, date DEFAULT (CURRENT_TIMESTAMP) );
CREATE TABLE expediteurs (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, nom, email, date DEFAULT (CURRENT_TIMESTAMP) );
CREATE TABLE envois (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, sujet, html, log, expediteur, nb INT, statut INT, pid INT, date DEFAULT (CURRENT_TIMESTAMP) );
CREATE TABLE boite_envoi (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, id_casquette INT, id_envoi INT, i INT, erreurs INT, date DEFAULT (CURRENT_TIMESTAMP) );

BEGIN TRANSACTION;
insert into news_modele ("rowid", "id_utilisateur", "nom", "modele", "date") values ('1', '1', 'bandeau image#10', '<div style="margin:0 auto;width:700px;height:80px;background:#fff;">
<img src="::image&Image&700&80&0::"/>
</div>', '2012-06-19 10:29:43');
insert into news_modele ("rowid", "id_utilisateur", "nom", "modele", "date") values ('2', '1', 'titre#01', '<div style="margin:0 auto;width:700px;text-align:justify;background:#fff;">
	<div style="color:::couleur&Couleur::;font-family: sans-serif, verdana;font-size:52px;">
		<strong>::texte_court&Titre::</strong>
	</div>
</div>', '2012-08-16 13:18:18');
insert into news_modele ("rowid", "id_utilisateur", "nom", "modele", "date") values ('3', '1', 'texte + image droite#31', '<div style="margin:0 auto;padding:5px;width:690px;_width:700px;font-family:sans-serif, verdana;font-size:12px;text-align:justify;background:#fff;">
		<div style="float:right;margin:0 0 0 10px;width:320px;text-align:right;">
		<img src="::image&Image&320&180&0::"/>	
			<br />
				</div>
			<div>
				<span style="color:::couleur&Couleur lettrine::;font-size:24px;">::texte_court&Lettrine::</span>::texte_long&Texte::
			</div>
<div style="clear:both;"></div>
</div>', '2012-08-16 13:22:26');
insert into news_modele ("rowid", "id_utilisateur", "nom", "modele", "date") values ('4', '1', 'séparateur#80', '<div style="margin:0 auto;padding-top:7px;
width:700px;font-family: verdana, sans-serif;font-size:16px;text-align:center; background:#fff;
color:::couleur&Couleur Séparateur::;">* * *
</div>', '2012-08-16 13:22:40');
insert into news_modele ("rowid", "id_utilisateur", "nom", "modele", "date") values ('5', '1', 'mention légale', '<div style="margin: 0 auto;padding:5px;width:690px;_width:700px;font-family: verdana, sans-serif;font-size:10px;text-align:justify;background:#fff;">
	
Conformément à la loi « informatique et libertés » du 6 janvier 1978 modifiée en 2004, vous bénéficiez d’un droit d’accès et de rectification aux informations qui vous concernent. Pour ce faire, il vous suffit de répondre à cet e-mail en précisant votre demande.
	
</div>
', '2012-08-16 13:23:31');
insert into news_modele ("rowid", "id_utilisateur", "nom", "modele", "date") values ('6', '1', 'image centrée#50', '<div style="margin:0 auto;width:700px;font-family: verdana,sans-serif;font-size:12px;text-align:center;background:#fff;">
	<img style="margin-top:10px" src="::image&Image&480&270&0::"/><br />
	<div style="font-size:0.8em;padding-bottom:5px;text-align:center;">::texte_court&Légende::</div>
</div>', '2012-08-16 13:23:58');
insert into news_modele ("rowid", "id_utilisateur", "nom", "modele", "date") values ('7', '1', 'texte + image gauche#30', '<div style="margin:0 auto;padding:5px;width:690px;_width:700px;font-family : sans-serif, verdana; font-size:12px;text-align:justify;background:#fff;">
<div style="float:left;margin:0 10px 0 0;width:320px;text-align:right;">
	<img src="::image&Image&320&180&0::"/>	
			<br /></div>
<div>  <span style="color:::couleur&Couleur lettrine::;font-size:2em;">::texte_court&Lettrine::</span>::texte_long&Texte::</div>
<div style="clear:both;"></div>
</div>', '2012-08-16 13:24:54');
insert into news_modele ("rowid", "id_utilisateur", "nom", "modele", "date") values ('8', '1', 'texte', '<div style="margin: 0 auto;padding:5px;width:690px;_width:700px;font-family: verdana, sans-serif;font-size:12px;text-align:justify;background:#fff;">

<span style="color:::couleur&Couleur lettrine:: ;font-size:2em; margin-left : 0; ">::texte_court&Lettrine::
</span>::texte_long&Texte::

</div>
', '2012-08-16 13:32:14');
COMMIT;

