<?php
namespace App\Controleurs;

use App\Modeles\Joueur;

class JoueurControleur {

    private $joueurModel;

    public function __construct() {
        $this->joueurModel = new Joueur(); // Créer une instance du modèle Joueur
    }

    // Afficher la liste des joueurs
    public function liste_joueurs() {
        try {
            // Récupérer les joueurs depuis le modèle
            return $this->joueurModel->getAllJoueurs();
        } catch (\Exception $e) {
            // Gérer les erreurs
            error_log("Erreur lors de la récupération des joueurs : " . $e->getMessage());
            return [];
        }
    }

    // Afficher la liste des joueurs actifs
    public function liste_joueurs_actifs() {
        try {
            // Récupérer les joueurs actifs depuis le modèle
            return $this->joueurModel->getJoueursActifs();
        } catch (\Exception $e) {
            // Gérer les erreurs
            error_log("Erreur lors de la récupération des joueurs actifs : " . $e->getMessage());
            return [];
        }
    }

    // Afficher le formulaire d'ajout et gérer la soumission
    public function ajouter_joueur() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $numero_licence = trim($_POST['numero_licence']);
            $nom = trim($_POST['nom']);
            $prenom = trim($_POST['prenom']);
            $date_naissance = $_POST['date_naissance'];
            $taille = isset($_POST['taille']) ? (float)$_POST['taille'] : null;
            $poids = isset($_POST['poids']) ? (float)$_POST['poids'] : null;
            $statut = $_POST['statut'] ?? 'Actif';
            $position_preferee = isset($_POST['position_preferee']) ? trim($_POST['position_preferee']) : null;
            $commentaire = isset($_POST['commentaire']) ? trim($_POST['commentaire']) : null;

            // Validation des champs
            if (empty($nom) || empty($prenom) || empty($date_naissance)) {
                echo "Tous les champs obligatoires doivent être remplis !";
                return;
            }

            if ($taille < 1.00 || $taille > 2.50) {
                echo"La taille doit être comprise entre 1.00 mètre et 2.50 mètres.";
                return;
             }

            if ($poids !== null && ($poids < 30 || $poids > 200)) { // Vérifie un poids raisonnable
                echo "Le poids doit être compris entre 30 kg et 200 kg.";
                return;
            }

            // Ajouter le joueur via le modèle
            try {
                $this->joueurModel->ajouterJoueur($numero_licence, $nom, $prenom, $date_naissance, $taille, $poids, $statut, $position_preferee, $commentaire);
                // Redirection vers la liste des joueurs après succès
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            } catch (\Exception $e) {
                echo "Erreur lors de l'ajout du joueur : " . $e->getMessage();
            }
        }
    }

    // Modifier un joueur
    public function modifier_joueur($numero_licence) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom = trim($_POST['nom']);
            $prenom = trim($_POST['prenom']);
            $date_naissance = $_POST['date_naissance'];
            $taille = isset($_POST['taille']) ? (float)$_POST['taille'] : null;
            $poids = isset($_POST['poids']) ? (float)$_POST['poids'] : null;
            $statut = $_POST['statut'] ?? 'Actif';
            $position_preferee = isset($_POST['position_preferee']) ? trim($_POST['position_preferee']) : null;
            $commentaire = isset($_POST['commentaire']) ? trim($_POST['commentaire']) : null;

            // Validation des champs
            if (empty($nom) || empty($prenom) || empty($date_naissance)) {
                echo "Tous les champs obligatoires doivent être remplis !";
                return;
            }

            if ($taille < 1.00 || $taille > 2.50) {
                echo"La taille doit être comprise entre 1.00 mètre et 2.50 mètres.";
                return;
            }

            // Modifier le joueur via le modèle
            try {
                $this->joueurModel->modifierJoueur($numero_licence, $nom, $prenom, $date_naissance, $taille, $poids, $statut, $position_preferee, $commentaire);
                // Redirection vers la liste des joueurs après succès
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            } catch (\Exception $e) {
                echo "Erreur lors de la modification du joueur : " . $e->getMessage();
            }
        } else {
            // Récupérer les informations du joueur pour pré-remplir le formulaire
            try {
                return $this->joueurModel->getJoueurByNumeroLicence($numero_licence);
            } catch (\Exception $e) {
                echo "Erreur lors de la récupération des informations du joueur : " . $e->getMessage();
                return null;
            }
        }
    }

    // Supprimer un joueur
    public function supprimer_joueur($numero_licence) {
        try {
            if ($this->joueurModel->estJoueurSelectionneEnCours($numero_licence)) {
                echo "<p>Le joueur ne peut pas être supprimé car il est dans une selection en cours.
                        Le score n'a pas encore été défini.</p>";
            } else {
                $this->joueurModel->supprimerJoueur($numero_licence);
                // Redirection vers la liste des joueurs après succès
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }
        } catch (\Exception $e) {
            echo "Erreur lors de la suppression du joueur : " . $e->getMessage();
        }
    }

    public function nombreTitularisation($numero_licence) {
        try {
            // Appel au modèle pour récupérer le nombre de notes
            return $this->joueurModel->getNombreTitularisationParJoueur($numero_licence);
        } catch (\Exception $e) {
            // Gérer les erreurs
            error_log("Erreur lors de la récupération du nombre de notes : " . $e->getMessage());
            return 0; // Retourne 0 en cas d'erreur
        }
    }

    public function nombreRemplacementsJoueur($numero_licence) {
        try {
            return $this->joueurModel->getNombreRemplacementsJoueur($numero_licence);
        } catch (\Exception $e) {
            error_log("Erreur lors de la récupération des remplacements : " . $e->getMessage());
            return 0; // Retourne 0 en cas d'erreur
        }
    }

    public function moyenneNotesJoueur($numero_licence) {
        try {
            return $this->joueurModel->getMoyenneNotesJoueur($numero_licence);
        } catch (\Exception $e) {
            error_log("Erreur lors de la récupération de la moyenne des notes : " . $e->getMessage());
            return 0;
        }
    }

    public function pourcentageVictoiresJoueur($numero_licence) {
        try {
            return $this->joueurModel->getPourcentageVictoiresJoueur($numero_licence);
        } catch (\Exception $e) {
            error_log("Erreur lors de la récupération du pourcentage de victoires : " . $e->getMessage());
            return 0;
        }
    }

}
