<?php

namespace Drupal\search\Controller;

use Drupal\Component\Utility\Tags;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AutocompleteController.
 */
class AutocompleteController extends ControllerBase
{
    /**
     * Returns a JSON list of node content suggestions for the YouTube/Vimeo.
     *
     * @param $field_name
     * @param $language
     */
    public function handleAutocomplete(Request $request): JsonResponse
    {
        $suggestions = [];

        // Get the typed string from the URL, if it exists.
        $query = $request->query->get('q');

        if ($query) {
            $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
            $typed_string = Tags::explode($query);
            $typed_string = Unicode::strtolower(array_pop($typed_string));
            $suggestions = $this->getSuggestions($typed_string, $langcode);
        }

        return new JsonResponse($suggestions);
    }

    public function getSuggestions(string $typedString, string $langcode): array
    {
        $suggestions = [];
        $query = \Drupal::entityQuery('node')
            ->condition('title', "$typedString%", 'LIKE', $langcode)
            ->condition(
                'type',
                [
                    'news',
                    'images',
                    'image_packs',
                    'vimeo',
                    'video_packs',
                    'press_kits',
                    'tech_specs',
                    'broadcast_video',
                ],
                'IN'
            )
            ->condition('field_brands', [\Drupal::config('jlr_brand')->get('brand_id')], 'IN')
            ->range(0, 3)
            ->execute();
        $nodes = Node::loadMultiple($query);

        foreach ($nodes as $node) {
            if (!($node instanceof Node)) {
                continue;
            }

            $suggestions[] = [
                'label' => $node->getTitle(),
                'value' => $node->getTitle(),
            ];
        }

        return $suggestions;
    }

    public function handleCorporateAutocomplete(Request $request): JsonResponse
    {
        $suggestions = [];

        $query = $request->query->get('q');

        // Get the typed string from the URL, if it exists.
        if ($query) {
            $typed_string = Tags::explode($query);
            $typed_string = Unicode::strtolower(array_pop($typed_string));
            $suggestions = $this->getCorporateSuggestions($typed_string);
        }

        return new JsonResponse($suggestions);
    }

    public function getCorporateSuggestions(string $typedString): array
    {
        $suggestions = [];
        $query = \Drupal::entityQuery('taxonomy_term')
            ->condition('vid', "tags")
            ->condition('name', "$typedString%", 'LIKE', 'en')
            ->range(0, 3)
            ->execute();
        $terms = Term::loadMultiple($query);

        foreach ($terms as $term) {
            if (!($term instanceof Term)) {
                continue;
            }

            $suggestions[] = [
                'label' => $term->getName(),
                'value' => $term->getName(),
            ];
        }

        return $suggestions;
    }
}
