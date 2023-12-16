<?php

namespace App\Twig;

use DOMDocument;
use DOMXPath;
use Symfony\Component\Validator\Constraints\DateTime;
use Twig\Extension\RuntimeExtensionInterface;

class AppRuntime implements RuntimeExtensionInterface
{
    public function AgoCustom($date){
        //date_default_timezone_set('Europe/Paris');

        $now = new \DateTime("now");
        //$now = date_format($now, 'Y/m/d H:i:s');

        $diff = $date->diff($now);

        if ($diff->y > 0) {
            $result = $diff->y . " an" . ($diff->y > 1 ? "s" : "");
        } elseif ($diff->m > 0) {
            $result = $diff->m . " mois";
        } elseif ($diff->d > 0) {
            $result = $diff->d . " jour" . ($diff->d > 1 ? "s" : "");
        } elseif ($diff->h > 0) {
            $result = $diff->h . " heure" . ($diff->h > 1 ? "s" : "");
        } elseif ($diff->i > 0) {
            $result = $diff->i . " minute" . ($diff->i > 1 ? "s" : "");
        } elseif ($diff->s > 0) {
            $result = $diff->s . " seconde" . ($diff->s > 1 ? "s" : "");
        } else {
            $result = "Erreur";
        }
        return $result;
    }

    function extractContentCode($chaine) {
        // Définir la regex pour capturer le contenu entre les balises <code>
        $pattern = '/<code[^>]*>(.*?)<\/code>/s';

        // Utiliser preg_match_all pour trouver toutes les correspondances
        if (preg_match_all($pattern, $chaine, $matches)) {
            // Retourner les résultats
            return $matches[1]; // Utiliser l'indice 1 pour récupérer le contenu capturé
        } else {
            // Retourner un tableau vide si aucune correspondance n'est trouvée
            return array();
        }
    }

    function extractContentNoneCode($chaine) {
        // Définir la regex pour trouver le contenu entre les balises <code> et </code>
        $pattern = '/<code[^>]*>.*?<\/code>/s';

        // Utiliser preg_split pour diviser la chaîne en utilisant la regex comme séparateur
        $contenus = preg_split($pattern, $chaine);

        // Supprimer les éléments vides du tableau résultant
        $contenus = array_filter($contenus, function ($element) {
            return !empty($element);
        });

        // Retourner le tableau de contenus
        return $contenus;
    }

    function getContentCommentArray($chaine){
        $arrayContent = $this->extractContentNoneCode($chaine);
        $arrayCode = $this->extractContentCode($chaine);

        // Initialiser le tableau résultant
        $tableauFinal = array();

        // Récupérer la longueur maximale entre les deux tableaux
        $longueurMax = max(count($arrayContent), count($arrayCode));

        // Parcourir les indices jusqu'à la longueur maximale
        for ($i = 0; $i < $longueurMax; $i++) {
            // Ajouter l'élément du tableau1 si l'indice existe
            if (isset($arrayContent[$i])) {
                array_push($tableauFinal,['type'=>'noCode','content'=>$arrayContent[$i]]);
            }

            // Ajouter l'élément du tableau2 si l'indice existe
            if (isset($arrayCode[$i])) {
                array_push($tableauFinal,['type'=>'code','content'=>$arrayCode[$i]]);
            }
        }

        return $tableauFinal;
    }

}