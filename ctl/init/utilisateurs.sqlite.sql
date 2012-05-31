CREATE TABLE utilisateurs (rowid INTEGER PRIMARY KEY AUTOINCREMENT, nom , login , mdp , droits INT, date DEFAULT (CURRENT_TIMESTAMP));
INSERT INTO utilisateurs (nom,login,mdp, droits) VALUES ('demo', 'demo', 'ke00zyBQaBhcw', 4);

