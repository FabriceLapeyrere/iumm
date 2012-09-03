CREATE TABLE ass_casquette_categorie (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, id_casquette INT, id_categorie INT, statut
INT, date DEFAULT (CURRENT_TIMESTAMP) );
CREATE TABLE ass_casquette_contact (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, id_casquette INT, id_contact INT, date DEFAULT (CURRENT_TIMESTAMP) );
CREATE TABLE ass_casquette_etablissement (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, id_casquette INT, id_etablissement INT, date DEFAULT (CURRENT_TIMESTAMP) );
CREATE TABLE ass_etablissement_structure (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, id_etablissement INT, id_structure INT, date DEFAULT (CURRENT_TIMESTAMP) );
CREATE TABLE casquettes (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, nom , tri, date DEFAULT (CURRENT_TIMESTAMP));
CREATE TABLE 'categories' (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, 
'nom' ,
'idparent' INT 
);
CREATE TABLE contacts (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, nom, prenom, date DEFAULT (CURRENT_TIMESTAMP));
CREATE TABLE 'donnees_casquette' (rowid INTEGER PRIMARY KEY AUTOINCREMENT, 
'id_utilisateur' INT,
'id_casquette' INT,
'label'  ,
'nom' ,
'type' ,
'valeur' ,
'date' DEFAULT (CURRENT_TIMESTAMP),
'actif'
);
CREATE TABLE 'donnees_etablissement' (rowid INTEGER PRIMARY KEY AUTOINCREMENT, 
'id_utilisateur' INT,
'id_etablissement' INT,
'label'  ,
'nom' ,
'type' ,
'valeur' ,
'date' DEFAULT (CURRENT_TIMESTAMP),
'actif'
);
CREATE TABLE etablissements (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, nom, date DEFAULT (CURRENT_TIMESTAMP));
CREATE TABLE structures (rowid INTEGER PRIMARY KEY AUTOINCREMENT, id_utilisateur INT, nom, date DEFAULT (CURRENT_TIMESTAMP));
