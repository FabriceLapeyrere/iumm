--@license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
--@author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>

CREATE TABLE supports (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT,  nom, h_page INT, l_page INT, nb_lignes INT, nb_colonnes INT, mp_gauche INT, mp_droite INT, mp_haut INT, mp_bas INT, mc_gauche INT, mc_droite INT, mc_haut INT, mc_bas INT, tpl, date DEFAULT (CURRENT_TIMESTAMP))
