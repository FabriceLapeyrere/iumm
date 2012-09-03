--@license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
--@author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>

CREATE VIRTUAL TABLE indexes USING fts3(tri, text, dept, email, adresse, id_contact, nom_contact, id_etablissement, id_structure, nom_structure, categories);

