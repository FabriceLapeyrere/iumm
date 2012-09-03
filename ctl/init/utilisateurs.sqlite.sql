--@license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
--@author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>

CREATE TABLE utilisateurs (rowid INTEGER PRIMARY KEY AUTOINCREMENT, nom , login , mdp , droits INT, date DEFAULT (CURRENT_TIMESTAMP));
INSERT INTO utilisateurs (nom,login,mdp, droits) VALUES ('demo', 'demo', 'ke00zyBQaBhcw', 1);
INSERT INTO utilisateurs (nom,login,mdp, droits) VALUES ('admin', 'admin', 'kepkYbqDNedE2', 4);

